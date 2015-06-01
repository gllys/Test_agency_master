<?php

/**
 * Class RefundApplyModel
 */
class RefundApplyModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'refund_apply';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|RefundApplyModel|';

    private $online_pay_types = array('alipay', 'kuaiqian', 'union');

    public function getTable()
    {
        return $this->tblname;
    }

    /**
     *
     * author : yinjian
     * @param $order
     * @param $ota_info
     * @param $return_ticket
     */
    public function refundOrder($order, $order_item, $return_ticket, $data)
    {
        $now = time();
        $money = ($order['amount']/$order['nums']) * $data['nums'];
        $refunding_nums = $order['refunding_nums'] + $data['nums'];
        $order_item_ids = array_keys($order_item);
        // order
        if ($order_item_ids) {
            OrderModel::model()->updateByAttr(array('refunding_nums' => $refunding_nums,'refund_status'=>1), array('id' => $order['id']));
            OrderItemModel::model()->updateByAttr(array('status' => 0), array('id|in' => $order_item_ids));
            TicketModel::model()->updateByAttr(array('status' => 0), array('order_item_id|in' => $order_item_ids));
            TicketItemsModel::model()->updateByAttr(array('status' => 0), array('order_item_id|in' => $order_item_ids));
        }
        // refund_apply
        $refund_apply_id = Util_Common::payid(2);  
        $this->add(array(
            'id' => $refund_apply_id, 
            'order_id' => $order['id'],
            'remark' => $data['remark'],
            'name' => $order['name'],
            'money' => $money,
            'nums' => $data['nums'],
            'u_id' => $data['user_id'],
            'pay_type' => $order['pay_type'],
            'ip' => Tools::getIp(),
            'pay_app_id' => $order['payment'],
            'order_at' => $order['created_at'],
            'distributor_id' => $order['distributor_id'],
            'supplier_id' => $order['supplier_id'],
            'landscape_id' => $order['landscape_ids'],
            'refund_items' => json_encode($order_item_ids),
            'created_by' => $data['user_id'],
            'created_at' => $now,
            'updated_at' => $now,
            'ticket_status' => 3,
            'notify_url' => isset($data['notify_url']) ? trim($data['notify_url']) : '',
        ));
        // 平台支付的退票处理 && 单独的优惠券退款
        if (in_array($order['payment'], $this->online_pay_types) || $order['activity_paid']>0) {
            if(!in_array($order['payment'], $this->online_pay_types) && $order['activity_paid']>0){
                $money = $order['activity_paid'];
            }
            $unionData = array(
                'org_id' => $order['distributor_id'],
                'user_id' => $data['user_id']?$data['user_id']:($this->body['user_id']?$this->body['user_id']:$order['user_id']),
                'user_account' => $data['user_account'] ? $data['user_account'] : ($this->body['user_account']?$this->body['user_account']:$order['user_account']),
                'user_name' => $data['user_name'] ? $data['user_name'] : ($this->body['user_name']?$this->body['user_name']:$order['user_name']),
                'money' => $money,
                'in_out' => 1,
                'trade_type' => 2,
                'frozen_type' => 1,
                'remark' => $refund_apply_id,
            );
            $r = ApiUnionMoneyModel::model()->unionInout($unionData);
            if (!$r || $r['code'] == 'fail') {
                return false;
            }
        }
        return $refund_apply_id;
    }

    /**
     * 拉手退款并审核
     * author : yinjian
     */
    public function refundOrderForLashou($order, $order_item, $return_ticket, $data)
    {
        try {
            $now = time();
            $this->begin();
            $money = $order_item['price'] * $data['nums'];
            $refund_apply_id = Util_Common::payid(2);
            // order 非在线支付直接打钱，减去订单正在退钱票数，加上订单中已退票数，加上订单中退款额度
            OrderModel::model()->setTable($order['id'])->updateByAttr(
                array(
                    'refunded_nums=refunded_nums+' . $data['nums'],
                    'refunded=refunded+' . $money,
                    'updated_at' => $now,
                ),
                array('id' => $order['id'])
            );
            OrderItemModel::model()->setTable($order['id'])->updateByAttr(
                array(
                    'refunded_nums=refunded_nums+' . $data['nums'],
                    'updated_at' => $now,
                ),
                array('order_id' => $order['id'])
            );
            // refund_apply
            $this->add(array(
                'id' => $refund_apply_id,
                'order_id' => $order['id'],
                'remark' => $data['remark'],
                'name' => $order_item['name'],
                'money' => $money,
                'nums' => $data['nums'],
                'u_id' => $data['user_id'],
                'pay_type' => $order['pay_type'],
                'ip' => Tools::getIp(),
                'allow_status' => 1,
                'status' => 1,
                'pay_app_id' => $order['payment'],
                'order_at' => $order['created_at'],
                'distributor_id' => $order['distributor_id'],
                'supplier_id' => $order['supplier_id'],
                'landscape_id' => $order['landscape_ids'],
                'created_by' => $data['user_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ));
            // refund_apply_items,tickets
            foreach ($return_ticket as $k => $v) {
                // refund_apply_items 批量添加
                RefundApplyItemsModel::model()->add(array(
                    'refund_apply_id' => $refund_apply_id,
                    'ticket_id' => $v['id'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ));
                // tickets 批量更新
                TicketModel::model()->setTable($order['id'])->updateByAttr(array('status' => 0, 'updated_at' => $now), array('id' => $v['id']));
            }
            // 储值款项退款到账户
            $refund_type_map = array('credit' => 1, 'advance' => 0);
            if (in_array($order['payment'], array('credit', 'advance'))) {
                $info['distributor_id'] = $order['distributor_id'];
                $info['supplier_id'] = $order['supplier_id'];
                $info['type'] = $refund_type_map[$order['pay_type']];
                $info['money'] = '+' . $money;
                $info['remark'] = $data['remark'];
                $info['op_id'] = $data['user_id'];
                $res = OrganizationModel::model()->addRefund($info);
                if (!$res) {
                    return false;
                }
            }
            // transflow
            $transflowParam = array(
                'id' => Util_Common::payid(),
                'mode' => $order['payment'],
                'type' => 2,
                'amount' => $money,
                'supplier_id' => $order['supplier_id'],
                'agency_id' => $order['distributor_id'],
                'ip' => Tools::getIp(),
                'op_id' => $order['user_id'],
                'user_name' => $order['user_name'],
                'created_at' => $now,
            );
            TransactionFlowModel::model()->add($transflowParam);
            // 退票操作后需要对日库存更改
            $ticketTemplateInfo = TicketTemplateModel::model()->getInfo($order_item['ticket_template_id'], $order_item['price_type'], $order_item['distributor_id'], $order_item['use_day'], 0,1,1);
            if (isset($ticketTemplateInfo['day_reserve']) && $ticketTemplateInfo['day_reserve'] > 0) {
                TicketTemplateModel::model()->updateTicketDayUsedReserve($order_item['ticket_template_id'], $ticketTemplateInfo['rule_id'], $order_item['use_day'], $data['nums'], 1);
            }
            $this->commit();
            return $refund_apply_id;
        } catch (PDOException $e) {
            // 回滚事务
            $this->rollBack();
            return false;
        }
    }

    /**
     * 审核退款操作
     * author : yinjian
     * @param $data
     * @param $refund_apply
     * @return bool
     */
    public function checkRefund($data, $refund_apply)
    {
        try {
            $now = time();
            // 获取需审核的退款order_items
            $this->begin();
            $refund = array();
            $refund['allow_status'] = $data['allow_status'];
            $refund['audited_by'] = $data['user_id'];
            $refund['updated_at'] = $now;
            $refund['op_id'] = $data['user_id'];
            // order_items
            if ($refund_apply['refund_items']) {
                $order_items_ids = json_decode($refund_apply['refund_items'], true);
            } else {
                $order_items = OrderItemModel::model()->search(array('status' => 0, 'use_time' => 0, 'order_id' => $refund_apply['order_id']), '*', null, $refund_apply['nums']);
                $order_items_ids = array_keys($order_items);
            }
            $orderInfo = reset(OrderModel::model()->search(array('id'=>$refund_apply['order_id'])));
            $refunding_nums = $orderInfo['refunding_nums'] - $refund_apply['nums'];

            if($orderInfo['local_source'] == 1) { //实际针对所有ota，通知OTA退票，返回成功后继续退款操作
                if($data['allow_status'] == 3) {
                    $refund_apply['reject_reason'] = $data['reject_reason'];
                }
                if(!OtaCallbackModel::model()->refund($orderInfo, $refund_apply, $refund['allow_status']==3 ? false : true)) {
                    return false;
                }
            }

            // 订单表判断
            if ($data['allow_status'] == 3) {
                // 拒绝
                $refund['status'] = 2;
                $refund['reject_reason'] = $data['reject_reason'];
                $r = $this->reject($orderInfo, $refund_apply, $refunding_nums, $now, $data, $order_items_ids);
            } else {
                // 允许
                $refund['status'] = 1;
                $r = $this->allow($orderInfo, $refund_apply, $refunding_nums, $now, $data, $order_items_ids);
            }
            if(!$r){
                return false;
            }
            $this->updateByAttr($refund, array('id' => $data['id']));
            $this->commit();
            return true;
        } catch (PDOException $e) {
            // 回滚事务
            $this->rollBack();
            return false;
        }
    }

    /**
     * 允许退款
     * author : yinjian
     */
    public function allow($orderInfo, $refund_apply, $refunding_nums, $now, $data, $order_items_ids)
    {
        // 退票操作后需要对日库存更改
        $ticketTemplateInfo = TicketTemplateModel::model()->getInfo($orderInfo['product_id'],0,$orderInfo['distributor_id'],$orderInfo['use_day'],0,0,1);
        if(isset($ticketTemplateInfo['day_reserve']) && $ticketTemplateInfo['day_reserve']>0){
            TicketTemplateModel::model()->updateTicketDayUsedReserve($orderInfo['product_id'],$orderInfo['rule_id'],$orderInfo['use_day'],$refund_apply['nums'],1);
        }
        $refunded_nums = $orderInfo['refunded_nums'] + $refund_apply['nums'];
        $refunded = $orderInfo['refunded'] + $refund_apply['money'];
        //减去订单正在退钱票数，加上订单中已退票数，加上订单中退款额度
        $orderParams = array(
            'refunding_nums' => $refunding_nums,
            'refunded_nums' => $refunded_nums,
            'refunded' => $refunded
        );
        if($refunded>$orderInfo['amount']){
            return false;
        }
        if($refunding_nums ==0 && $refunded_nums>0){
            $orderParams['refund_status'] = 2;
        }
        if ($orderInfo['used_nums'] == $orderInfo['nums'] - $orderInfo['refunded_nums'] - $refund_apply['nums']) {
            $orderParams['status'] = 'finish';
        }
        // 订单
        OrderModel::model()->updateByAttr($orderParams, array('id' => $refund_apply['order_id']));
        // 流水
        TransactionFlowModel::model()->add(array(
            'id' => Util_Common::payid(),
            'mode' => $refund_apply['pay_app_id'],
            'type' => 2,
            'amount' => $refund_apply['money'],
            'supplier_id' => $refund_apply['supplier_id'],
            'agency_id' => $refund_apply['distributor_id'],
            'ip' => Tools::getIp(),
            'op_id' => intval($orderInfo['user_id']),
            'user_name' => strval($orderInfo['user_name']),
            'created_at' => $now,
        ));
        // 抵用券
        if($orderInfo['activity_paid']>0 && !in_array($orderInfo['payment'], $this->online_pay_types)){
            $refund_apply['money'] = $refund_apply['money'] - $orderInfo['activity_paid'];
        }
        // 微信的票不需要退款
        if ($orderInfo && $orderInfo['ota_type'] != 'weixin') {
            // 储值款项退款到账户@TODO
            $refund_type_map = array('credit' => 1, 'advance' => 0);
            if (in_array($refund_apply['pay_type'], array('credit', 'advance'))) {
                $info['distributor_id'] = $refund_apply['distributor_id'];
                $info['supplier_id'] = $refund_apply['supplier_id'];
                $info['type'] = $refund_type_map[$refund_apply['pay_type']];
                $info['money'] = '+' . $refund_apply['money'];
                $info['remark'] = $refund_apply['id'];
                $info['op_id'] = $refund_apply['op_id'];
                $res = OrganizationModel::model()->addRefund($info);
                if (!$res) {
                    return false;
                }
            }
        }
        //平台支付的退票处理
        if (in_array($orderInfo['payment'], $this->online_pay_types) || $orderInfo['activity_paid']>0) {
            if(!in_array($orderInfo['payment'], $this->online_pay_types)){
                $refund_apply['money'] = $orderInfo['activity_paid'];
            }
            $unionParams = array(
                'org_id' => $orderInfo['distributor_id'],
                'user_id' => $data['user_id'] ? $data['user_id'] : intval($this->body['user_id']),
                'user_account' => $data['user_account'] ? $data['user_account'] : strval($this->body['user_account']),
                'user_name' => $data['user_name'] ? $data['user_name'] : strval($this->body['user_name']),
                'money' => $refund_apply['money']-$orderInfo['activity_paid'],
                'in_out' => 1,
                'trade_type' => 2,
                'activity_money' => $orderInfo['activity_paid'],
                'frozen_type' => 0,
                'remark' => $data['id'],
                'allow_refund' => 1,
                'payment_id' => $orderInfo['payment_id'],
                'payment' => $orderInfo['payment'],
            );
            $r = ApiUnionMoneyModel::model()->unionInout($unionParams);
            if (!$r || $r['code'] == 'fail') {
                $this->rollBack();
                !empty($r['message']) && Lang_Msg::error($r['message']);
                return false;
            }
        }
        return true;
    }

    /**
     * 拒绝退款
     * author : yinjian
     */
    public function reject($orderInfo, $refund_apply, $refunding_nums, $now, $data, $order_items_ids)
    {
        // 审核拒绝，将订单中退款中数量减去对应的数目，根据票张数状态修改
        $change_order_attr = array('refunding_nums' => $refunding_nums, 'updated_at' => $now);
        if($refunding_nums ==0 && $orderInfo['refunded_nums']==0){
            $change_order_attr['refund_status'] = 0;
        }elseif($refunding_nums ==0 && $orderInfo['refunded_nums']>0){
            $change_order_attr['refund_status'] = 2;
        }elseif($refunding_nums >0 ){
            $change_order_attr['refund_status'] = 1;
        }
        OrderModel::model()->updateByAttr($change_order_attr, array('id' => $refund_apply['order_id']));
        OrderItemModel::model()->updateByAttr(array('status' => 1), array('id|in' => $order_items_ids));
        TicketModel::model()->updateByAttr(array('status' => 1), array('order_item_id|in' => $order_items_ids));
        TicketItemsModel::model()->updateByAttr(array('status' => 1), array('order_item_id|in' => $order_items_ids));
        //平台支付的退票处理
        if (in_array($orderInfo['payment'], $this->online_pay_types)) {
            $unionParams = array(
                'org_id' => $orderInfo['distributor_id'],
                'user_id' => $data['user_id'] ? $data['user_id'] : $this->body['user_id'],
                'user_account' => $data['user_account'] ? $data['user_account'] : $this->body['user_account'],
                'user_name' => $data['user_name'] ? $data['user_name'] : $this->body['user_name'],
                'money' => $refund_apply['money'],
                'in_out' => 1,
                'trade_type' => 2,
                'frozen_type' => 0,
                'remark' => $data['id'],
                'allow_refund' => 0,
                'payment_id' => $orderInfo['payment_id'],
            );
            $r = ApiUnionMoneyModel::model()->unionInout($unionParams);
            if (!$r || $r['code'] == 'fail') {
                $this->rollBack();
                !empty($r['message']) && Lang_Msg::error($r['message']);
                return false;
            }
        }
        // 驳回消息
        MessageModel::model()->addBase(array(
            'content'=>'<a href="/order/detail/index/id/'.$refund_apply['order_id'].'">'.$refund_apply['order_id'].'</a>订单退票被驳回，点击订单号查看相应订单',
            'sms_type'=>0,
            'sys_type'=>5,
            'send_source'=>1,
            'send_status'=>1,
            'send_user'=> intval($data['user_id']),
            'send_organization'=>$orderInfo['supplier_id'],
            'receiver_organization'=>$orderInfo['distributor_id'],
            'organization_name'=> $orderInfo['supplier_name'],
        ));
        return true;
    }


    /**
     * 申请退票
     * author : yinjian
     * @param 参数
     * @return bool|string
     */
    public function applyRefund($params)
    {
        if (intval($params['nums']) < 1) Lang_Msg::error('ERROR_REFUNDAPPLY_1');
        !$params['order_id'] && Lang_Msg::error('ERROR_REFUNDAPPLY_2');
        !$params['user_id'] && Lang_Msg::error('ERROR_REFUNDAPPLY_3');
        // 订单是否存在
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $ticketModel = new TicketModel();
        $ticketModel->begin();
        $order = $orderModel->get(array('id' => $params['order_id'], 'deleted_at' => 0));
        !$order && Lang_Msg::error('ERROR_REFUNDAPPLY_4');
        // 是否使用了优惠券
        if($order['activity_paid']>0 && $params['nums'] != $order['nums']){
            Lang_Msg::error('使用优惠券的订单只能退全部票数');
        }
        // 未支付单不能退款
        (!in_array($order['status'], array('paid', 'finish'))) && Lang_Msg::error('ERROR_REFUNDAPPLY_5');
        // 可退票数=总票数-已使用票数-退款中票数-已退款张数
        $remain_ticket = $order['nums'] - $order['used_nums'] - $order['refunding_nums'] - $order['refunded_nums'];
        ($params['nums'] > $remain_ticket) && Lang_Msg::error('ERROR_REFUNDAPPLY_6');
        // 票模板为可退属性
        $order['refund'] == 0 && Lang_Msg::error('ERROR_REFUNDAPPLY_7');
        // 根据可退产品数来进行筛选order_items
        $order_item = $orderItemModel->search(array('order_id' => $params['order_id'], 'use_time' => 0, 'status' => 1), '*', null, $params['nums']);
        // 获取退票
        $return_ticket = $ticketModel->search(array('order_item_id|in' => array_keys($order_item), 'status' => 1, 'poi_used_num' => 0, 'deleted_at' => 0));
        // 申请退票操作
        try {
            $refund_apply_id = $this->refundOrder($order, $order_item, $return_ticket, $params);
            if (!$refund_apply_id) {
                throw new Exception("ERROR_REFUNDAPPLY_8");
            }
            if($order['partner_type']>0 && $order['partner_product_code']!='') { //合作伙伴的订单先在合作伙伴退票
                $r = OpenApiPartnerModel::model()->partnerRefundOrder($order,$params['nums']);
                if($r===false){
                    $ticketModel->rollback();
                    Lang_Msg::error('该订单是合作伙伴订单，在合作伙伴退票失败，无法继续操作！');
                } else { //合作伙伴退票成功后，自动审核
                    $r = $this->checkApply(array(
                        'id'=>$refund_apply_id,
                        'allow_status'=>1,
                        'user_id'=>$params['user_id'],
                        'user_account'=>$params['user_account'],
                        'user_name'=>$params['user_name'],
                    ));
                    if(empty($r)){
                        MessageModel::model()->addBase(array(
                            'content'=>'订单['.$order['id'].']是合作伙伴订单，在合作伙伴退票成功，但退票申请自动审核失败，请处理！',
                            'sms_type'=>1,
                            'sys_type'=>5,
                            'send_source'=>2,
                            'send_status'=>1,
                            'send_user'=>$params['user_id']>0?$params['user_id']:$order['user_id'],
                            'send_organization'=>$order['distributor_id'],
                            'receiver_organization'=>$order['supplier_id'],
                            'organization_name'=>$order['supplier_name'],
                            'receiver_organization_type'=>0,
                        ));
                    }
                }
            }
            $ticketModel->commit();
        } catch (PDOException $e) {
            // 回滚事务
            $ticketModel->rollBack();
            Lang_Msg::error('ERROR_REFUNDAPPLY_8');
        }
        return $refund_apply_id;
    }

    /**
     * 申请并退款 预处理 提示错误
     * author : yinjian
     * @param $params
     */
    public function applycheck($params)
    {
        if (intval($params['nums']) < 1) Lang_Msg::error('ERROR_REFUNDAPPLY_1');
        (!$params['order_id'] && !$params['source_id']) && Lang_Msg::error('ERROR_REFUNDAPPLY_2');
        !$params['user_id'] && Lang_Msg::error('ERROR_REFUNDAPPLY_3');
        // 订单是否存在
        $this->begin();
        try {
            $orderModel = new OrderModel();
            $orderItemModel = new OrderItemModel();
            $where = array('deleted_at' => 0);
            $params['order_id'] && $where['id'] = $params['order_id'];
            $params['source_id'] && $where['source_id'] = $params['source_id'];
            $order = reset($orderModel->search($where));
            !$order && Lang_Msg::error('ERROR_REFUNDAPPLY_4');
            // 是否使用了优惠券
            if ($order['activity_paid'] > 0 && $params['nums'] != $order['nums']) {
                Lang_Msg::error('使用优惠券的订单只能退全部票数');
            }
            // 未支付单不能退款
            (!in_array($order['status'], array('paid', 'finish'))) && Lang_Msg::error('ERROR_REFUNDAPPLY_5');
            // 可退票数=总票数-已使用票数-退款中票数-已退款张数
            $remain_ticket = $order['nums'] - $order['used_nums'] - $order['refunding_nums'] - $order['refunded_nums'];
            ($params['nums'] > $remain_ticket) && Lang_Msg::error('ERROR_REFUNDAPPLY_6');
            // 票模板为可退属性
            $order['refund'] == 0 && Lang_Msg::error('ERROR_REFUNDAPPLY_7');
            // 根据可退产品数来进行筛选order_items
            $order_item = $orderItemModel->getCacheByOrderId($order['id']);
            $order_item_ids = [];
            $i = 0;
            foreach ($order_item as $itemID=>&$status) {
                if ($status == 1) {
                    $i++;
                    $order_item_ids[] = $itemID;
                    $status = 0;
                }
                if ($i >= $params['nums']) {
                    break;
                }
            }
            if (empty($order_item_ids)) {
                Lang_Msg::error('没有可退票！');
            }
            if ($params['nums']>count($order_item_ids)) {
                Lang_Msg::error('ERROR_REFUNDAPPLY_6');
            }
            $clacApply = $this->clacApply($order, $params);
            $clacApply['refund_apply_id'] = Util_Common::payid(2);
            //更新缓存
            $orderItemModel->setCacheByOrderId($order['id'], $order_item);
            // 申请退票操作
            $res = Process_Async::presend([__CLASS__, 'asyncApplyAndCheck'], [$order, $order_item_ids, $params, $clacApply]);
            if ($res == false) {
                Lang_Msg::error('操作失败!');
            }
//            $res = $this->applyAndCheck($order, $order_item_ids, $params, $clacApply);
            $this->commit();
            return $clacApply['refund_apply_id'];
        } catch(Exception $e) {
            $this->rollback();
            Lang_Msg::error('ERROR_REFUNDAPPLY_8');
        }
    }
    
    public static function asyncApplyAndCheck($order, $order_item_ids, $params, $calcApply)
    {
        $result = RefundApplyModel::model()->applyAndCheck($order, $order_item_ids, $params, $calcApply);
        if ($result === true) {
            return true;
        }
        //保存参数并发邮件通知
        $logs = json_encode(func_get_args(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $args = var_export(func_get_args(), true);
        Log_Base::save("asyncApplyAndCheck", $logs);
        $content = 'method: '.__METHOD__."\n message: {$result}\n params: {$args}";
        MailModel::sendSrvGroup("申请并退款异步操作失败", $content);
        //MailModel::sendTo('tuyuwei@ihuilian.com', "申请并退款异步操作失败", $content);
    }

    /**
     * 申请并退款 实际操作
     * author : yinjian
     */
    public function applyAndCheck($order, $order_item_ids, $data, $calcApply)
    {
        $this->begin();
        try {
            $now = time();
            $orderParams = $calcApply['orderParams'];
            $refund_apply_id = $calcApply['refund_apply_id'];
            $money = $calcApply['money'];
            // order
            OrderModel::model()->updateByAttr($orderParams, array('id' => $order['id']));
            OrderItemModel::model()->updateByAttr(array('status' => 0), array('id|in' => $order_item_ids));
            TicketModel::model()->updateByAttr(array('status' => 0), array('order_item_id|in' => $order_item_ids));
            TicketItemsModel::model()->updateByAttr(array('status' => 0), array('order_item_id|in' => $order_item_ids));
            // refund_apply
            $refund_apply = array(
                'id' => $refund_apply_id,
                'order_id' => $order['id'],
                'remark' => $data['remark'],
                'name' => $order['name'],
                'money' => $money,
                'nums' => $data['nums'],
                'u_id' => $data['user_id'],
                'op_id' => $data['user_id'],
                'pay_type' => $order['pay_type'],
                'ip' => Tools::getIp(),
                'allow_status' => 1,
                'status' =>1,
                'pay_app_id' => $order['payment'],
                'order_at' => $order['created_at'],
                'distributor_id' => $order['distributor_id'],
                'supplier_id' => $order['supplier_id'],
                'landscape_id' => $order['landscape_ids'],
                'refund_items' => json_encode($order_item_ids),
                'created_by' => $data['user_id'],
                'audited_by' => $data['user_id'],
                'created_at' => $now,
                'updated_at' => $now,
                'ticket_status' => 3,
            );
            $this->add($refund_apply);
            // 退票操作后需要对日库存更改
            $ticketTemplateInfo = TicketTemplateModel::model()->getInfo($order['product_id'],0,$order['distributor_id'],$order['use_day'],0,0,1);
            if(isset($ticketTemplateInfo['day_reserve']) && $ticketTemplateInfo['day_reserve']>0){
                TicketTemplateModel::model()->updateTicketDayUsedReserve($order['product_id'],$order['rule_id'],$order['use_day'],$order['nums'],1);
            }
            // 流水
            TransactionFlowModel::model()->add(array(
                'id' => Util_Common::payid(),
                'mode' => $refund_apply['pay_app_id'],
                'type' => 2,
                'amount' => $refund_apply['money'],
                'supplier_id' => $refund_apply['supplier_id'],
                'agency_id' => $refund_apply['distributor_id'],
                'ip' => Tools::getIp(),
                'op_id' => intval($order['user_id']),
                'user_name' => strval($order['user_name']),
                'created_at' => $now,
            ));
            // 抵用券
            if($order['activity_paid']>0 && !in_array($order['payment'], $this->online_pay_types)){
                $refund_apply['money'] = $refund_apply['money'] - $order['activity_paid'];
            }

            // 微信的票不需要退款
            if ($order && $order['ota_type'] != 'weixin') {
                // 储值款项退款到账户@TODO
                $refund_type_map = array('credit' => 1, 'advance' => 0);
                if (in_array($refund_apply['pay_type'], array('credit', 'advance'))) {
                    $info['distributor_id'] = $refund_apply['distributor_id'];
                    $info['supplier_id'] = $refund_apply['supplier_id'];
                    $info['type'] = $refund_type_map[$refund_apply['pay_type']];
                    $info['money'] = '+' . $refund_apply['money'];
                    $info['remark'] = $refund_apply['id'];
                    $info['op_id'] = $refund_apply['op_id'];
                    $res = OrganizationModel::model()->addRefund($info);
                    if (!$res) {
                        //$this->rollBack();
                        throw new Lang_Exception("修改额度失败！");
                    }
                }
            }
            // 平台支付的退票处理 && 单独的优惠券退款  平台申请退款
            if (in_array($order['payment'], $this->online_pay_types) || $order['activity_paid']>0) {
                if(!in_array($order['payment'], $this->online_pay_types) && $order['activity_paid']>0){
                    $money = $order['activity_paid'];
                }
                $r = ApiUnionMoneyModel::model()->unionInout(array(
                    'org_id' => $order['distributor_id'],
                    'user_id' => $data['user_id'] ? $data['user_id'] : $this->body['user_id'],
                    'user_account' => $data['user_account'] ? $data['user_account'] : $this->body['user_account'],
                    'user_name' => $data['user_name'] ? $data['user_name'] : $this->body['user_name'],
                    'money' => $money,
                    'in_out' => 1,
                    'trade_type' => 2,
                    'frozen_type' => 1,
                    'remark' => $refund_apply_id,
                ));
                if (!$r || $r['code'] == 'fail') {
                    //$this->rollBack();
                    $r['message'] = $r['message'] ? $r['message'] : "平台支付的退票处理 && 单独的优惠券退款  平台申请退款失败";
                    throw new Lang_Exception($r['message']);
                }
            }
            //平台支付的退票处理  平台运行退款
            if (in_array($order['payment'], $this->online_pay_types) || $order['activity_paid']>0) {
                if(!in_array($order['payment'], $this->online_pay_types)){
                    $refund_apply['money'] = $order['activity_paid'];
                }
                $unionParams = array(
                    'org_id' => $order['distributor_id'],
                    'user_id' => $data['user_id'] ? $data['user_id'] : $order['user_id'],
                    'user_account' => $data['user_account'] ? $data['user_account'] : $order['user_account'],
                    'user_name' => $data['user_name'] ? $data['user_name'] : $order['user_name'],
                    'money' => $refund_apply['money']-$order['activity_paid'],
                    'in_out' => 1,
                    'trade_type' => 2,
                    'activity_money' => $order['activity_paid'],
                    'frozen_type' => 0,
                    'remark' => $refund_apply_id,
                    'allow_refund' => 1,
                    'payment_id' => $order['payment_id'],
                    'payment' => $order['payment'],
                );
                $r = ApiUnionMoneyModel::model()->unionInout($unionParams);
                if (!$r || $r['code'] == 'fail') {
                    //$this->rollBack();
                    $r['message'] = $r['message'] ? $r['message'] : "平台支付的退票处理  平台运行退款 失败";
                    throw new Lang_Exception($r['message']);
                }
            }
            $this->commit();
            return true;
        } catch (Exception $e) {
            // 回滚事务
            $this->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * 审核退款
     * author : yinjian
     * @param $params
     * @return bool
     */
    public function checkApply($params)
    {
        // 获取申请退款的id
        !Validate::isString($params['id']) && Lang_Msg::error("ERROR_CheckRefund_1");
        // 获取操作人id
        !Validate::isUnsignedId($params['user_id']) && Lang_Msg::error("ERROR_CheckRefund_2");
        // 获取审核操作
        !in_array(intval($params['allow_status']), array(1, 3)) && Lang_Msg::error("ERROR_CheckRefund_3");
        // 拒绝理由
        if (intval($params['allow_status']) == 3 && !Validate::isString(trim($params['reject_reason']))) Lang_Msg::error("ERROR_CheckRefund_4");

        $refund_apply = RefundApplyModel::model()->get(array('id' => $params['id'], 'is_del' => 0));
        !$refund_apply && Lang_Msg::error("ERROR_CheckRefund_5");
        // 判断该申请单有无人审核过
        ($refund_apply['audited_by'] != 0) && Lang_Msg::error("ERROR_CheckRefund_6");
        $res = $this->checkRefund($params, $refund_apply);
        !$res && Lang_Msg::error("ERROR_CheckRefund_7");
        return $res;
    }

    /**
     * 审核OTA淘宝之类的退款
     * author : yinjian
     * @param $params
     */
    public function checkotaApply($params)
    {
        // 获取申请退款的id
        !Validate::isString($params['order_id']) && Lang_Msg::error("申请退款订单号不能为空");
        // 获取操作人id
        !Validate::isUnsignedId($params['user_id']) && Lang_Msg::error("ERROR_CheckRefund_2");
        // 获取审核操作
        !in_array(intval($params['allow_status']), array(1, 3)) && Lang_Msg::error("ERROR_CheckRefund_3");
        // 拒绝理由
        if (intval($params['allow_status']) == 3 && !Validate::isString(trim($params['reject_reason']))) Lang_Msg::error("ERROR_CheckRefund_4");

        $this->begin();
        $refund_apply_list = RefundApplyModel::model()->search(array('order_id' => $params['order_id'],'allow_status'=>0, 'is_del' => 0));
        !$refund_apply_list && Lang_Msg::error("ERROR_CheckRefund_5");
        try{
            foreach($refund_apply_list as $k => $v){
                $params['id'] = $v['id'];
                $res = $this->checkRefund($params, $v);
                if(!$res) {
                    Lang_Msg::error("ERROR_CheckRefund_7");
                }
            }
            $this->commit();
            return true;
        } catch (PDOException $e) {
            // 回滚事务
            $this->rollBack();
            return false;
        }
    }

    /**
     * @param $order
     * @param $data
     * @return array
     */
    private function clacApply($order, $data)
    {
        // 金额计算
        $money = $order['price'] * $data['nums'];
        $refunded_nums = $order['refunded_nums'] + $data['nums'];
        $refunded = $order['refunded'] + $money;

        $orderParams = array('refunded_nums' => $refunded_nums, 'refunded' => $refunded);
        if ($order['used_nums'] == $order['nums'] - $order['refunded_nums'] - $data['nums']) {
            $orderParams['status'] = 'finish';
        }
        if ($order['refunding_nums'] > 0) {
            $orderParams['refund_status'] = 1;
        } elseif ($orderParams['refunded_nums'] > 0) {
            $orderParams['refund_status'] = 2;
        }
        if ($refunded_nums > $order['nums']) {
            Lang_Msg::error("没有可退票!");
        }
        return array('money'=>$money, 'orderParams'=>$orderParams);
    }

}