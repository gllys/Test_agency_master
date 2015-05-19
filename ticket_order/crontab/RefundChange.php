<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/28
 * Time: 15:09
 */

date_default_timezone_set('PRC');
require dirname(__FILE__) . '/Base.php';

class Crontab_RefundChange extends Process_Base
{
    protected $limit = 100;
    protected $interval = 1200; //循环间隔（秒）20分钟
    protected $orders;
    protected $online_pay_types = array('alipay', 'kuaiqian', 'union');

    public function run()
    {
        while (true) {
            $this->now = time();
            echo 'start_now:'.date("Y-m-d H:i:s",$this->now)."\n";
            // model引用
            $this->expiredTime = $this->now - (86400 * 3);
            $this->RefundApplyModel = RefundApplyModel::model();
            $this->OrderModel = OrderModel::model();
            $this->OrderItemModel = OrderItemModel::model();
            $this->TicketModel = TicketModel::model();

            //取消24小时后未支付订单
            $this->cancelOrder();

            //查询过期3天未处理退款申请
            $this->checkRefund();
            //查询过期3天已支付未完成的可退的订单，并执行退款
            $this->refundExpired();
            echo 'waiting...'.$this->interval."s \n";
            sleep($this->interval);
        }
    }

    /**
     * 取消未支付订单
     * author : yinjian
     */
    public function cancelOrder()
    {
        $otaConf = OtaAccountModel::model()->config;
        $otaSource = array();
        foreach($otaConf as $sc=>$v){
            if($v['autoCancelOrderExpire']>0){
                $otaSource[] = '(source='.$sc.' AND created_at<'.($this->now-$v['autoCancelOrderExpire']).')';
            }
        }
        $this->OrderModel->updateByAttr(
            array('status' => 'cancel', 'updated_at' => $this->now),
            array(
                'status|in' => array('unpaid','reject','unaudited'),
                'OR'=>array(
                    'created_at|<'=>$this->now-86400,
                    'AND'=>implode(' OR ',$otaSource),
                ),
            )
        );
    }

    /*
     * 查询过期3天未处理退款申请
     */
    public function checkRefund()
    {
        echo 'checkRefund'."\n";
        $this->startNums = 0;
        while (1) {
            $refundApplys = $this->RefundApplyModel->search(array(
                'status' => 0,
                'audited_by' => 0,
                'created_at|<' => $this->expiredTime,
                'is_del' => 0,
            ), "*", "created_at asc", $this->startNums . "," . $this->limit);
            if (!$refundApplys) return true;
            $refundApplyIds = array();
            foreach ($refundApplys as $refundInfo) {
                $row = array(
                    'id' => $refundInfo['id'], 'allow_status' => 1,
                    'user_id' => 1, 'user_account' => 'system', 'user_name' => 'system',
                );
                !$row['user_name'] && $row['user_name'] = $row['user_account'];
                $r = $this->checkRefundApply($row, $refundInfo);
                if (!$r) continue;
                $refundApplyIds[] = $refundInfo['id'];
            }
            $this->startNums += $this->limit - count($refundApplyIds);
            echo "Checked Refund Applications: " . implode(' , ', $refundApplyIds) . "\n\n";
        }
        return true;
    }

    /*
    * 查询过期已支付未完成的可退订单，并执行退款
    */
    public function refundExpired()
    {
        echo 'refundExpired'."\n";
        $this->startNums = 0;
        while (1) {
            $fields = " SELECT *";
            $from = " FROM `orders` o";
            $where = " WHERE o.deleted_at=0 AND o.ota_type='system' AND o.status='paid' AND o.refund=1 ";
            $where .= " AND ((o.nums>o.used_nums+o.refunding_nums+o.refunded_nums AND o.activity_paid = 0) OR (o.used_nums = 0 AND o.refunding_nums = 0 AND o.refunded_nums = 0 AND o.activity_paid > 0)) AND o.expire_end<'" . $this->now . "' ";
            $orderBy = " ORDER BY o.id ASC ";
            $limit = " LIMIT " . $this->startNums . "," . $this->limit;
            $this->orders = $this->OrderModel->db->selectBySql($fields . $from . $where . $orderBy . $limit);
            if (!$this->orders) return true;
            $handlerApplyRefundOrderIds = array();
            foreach ($this->orders as $order) {
                $row = array(
                    'order_id' => $order['id'],
                    'nums' => $order['nums'] - $order['used_nums'] - $order['refunding_nums'] - $order['refunded_nums'],
                    'user_id' => 1,
                    'user_account' => 'system',
                    'user_name' => 'system',
                    'remark' => '订单过期系统自动退票',
                );
                // 根据可退产品数来进行筛选order_items
                $order_item = $this->OrderItemModel->search(array('order_id' => $order['id'], 'use_time' => 0, 'status' => 1), '*', null, $row['nums']);
                //$return_ticket = $this->TicketModel->search(array('order_item_id|in' => array_keys($order_item), 'status' => 1, 'poi_used_num' => 0, 'deleted_at' => 0));
                $refund_apply_id = $this->refundOrder($order, $order_item, $row);
//                var_dump($order['id']);
                if (!$refund_apply_id) {
                    echo 'Something happend lead to fail!'."\n";
                    echo $order['id']."\n";
                    sleep(10);
                    continue;
                } else {
                    $handlerApplyRefundOrderIds[] = $order['id'];
                    if($order['partner_type']>0 && $order['partner_product_code']!='' && $order['partner_order_id']!='') { //合作伙伴的订单先在合作伙伴退票
                        $r = OpenApiPartnerModel::model()->partnerRefundOrder($order,$row['nums']);
                        if($r===false){
                            $this->RefundApplyModel->rollback();
                            echo "该订单[{$order['id']}]是合作伙伴订单，在合作伙伴退票失败，无法继续操作！\n\n";
                        } else { //合作伙伴退票成功后，自动审核
                            $r = $this->RefundApplyModel->checkApply(array(
                                'id'=>$refund_apply_id,
                                'allow_status'=>1,
                                'user_id'=>$row['user_id'],
                                'user_account'=>$row['user_account'],
                                'user_name'=>$row['user_name'],
                            ));
                            if(empty($r)){
                                MessageModel::model()->addBase(array(
                                    'content'=>'订单['.$order['id'].']是合作伙伴订单，在合作伙伴退票成功，但退票申请自动审核失败，请处理！',
                                    'sms_type'=>1,
                                    'sys_type'=>5,
                                    'send_source'=>2,
                                    'send_status'=>1,
                                    'send_user'=>$row['user_id']>0?$row['user_id']:$order['user_id'],
                                    'send_organization'=>$order['distributor_id'],
                                    'receiver_organization'=>$order['supplier_id'],
                                    'organization_name'=>$order['supplier_name'],
                                    'receiver_organization_type'=>0,
                                ));
                            }
                        }
                    }
                }
            }
            $this->startNums += $this->limit - count($handlerApplyRefundOrderIds);
            echo "Handle Expired Orders: " . implode(' , ', $handlerApplyRefundOrderIds) . "\n\n";
        }
        return true;
    }

    //申请退票
    private function refundOrder($order, $order_item, $data)
    {
        try {
            //$now = time();
            $this->RefundApplyModel->begin();
            $money = ($order['amount']/$order['nums']) * $data['nums'];
            $refunding_nums = $order['refunding_nums'] + $data['nums'];
            $refund_apply_id = Util_Common::payid(2);
            $order_item_ids = array_keys($order_item);
            // order
            $this->OrderModel->updateByAttr(array('refunding_nums' => $refunding_nums,'updated_at'=>$this->now,'refund_status'=>1), array('id' => $order['id']));
            // order_items
            //$order_item_ids && $this->OrderItemModel->updateByAttr(array('refunding_nums' => $refunding_nums,'updated_at'=>$now), array('order_id' => $order['id']));
            //foreach ($order_item_ids as $k => $v) {
            $this->OrderItemModel->updateByAttr(array('status' => 0,'updated_at'=>$this->now), array('id|in' => $order_item_ids));
            //}
            // ticket_items
            $order_item_ids && TicketItemsModel::model()->updateByAttr(array('status' => 0,'updated_at'=>$this->now), array('order_item_id|in' => $order_item_ids));
            // refund_apply
            $this->RefundApplyModel->add(array(
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
                'created_at' => $this->now,
                'updated_at' => $this->now,
                'ticket_status' => 3,
            ));
            //foreach ($return_ticket as $k => $v) {
                // refund_apply_items 批量添加
                //$r = RefundApplyItemsModel::model()->add(array(
                //    'refund_apply_id' => $refund_apply_id,
                //    'ticket_id' => $v['id'],
                //    'created_at' => $this->now,
                //    'updated_at' => $this->now,
                //));
                // tickets 批量更新
            $this->TicketModel->updateByAttr(array('status' => 0, 'updated_at' => $this->now), array('order_item_id|in' => $order_item_ids));
            //}
            // 平台支付的退票处理 && 单独的优惠券退款
            if (in_array($order['payment'], $this->online_pay_types) || $order['activity_paid'] > 0) {
                if (!in_array($order['payment'], $this->online_pay_types) && $order['activity_paid'] > 0) {
                    $money = $order['activity_paid'];
                }
                $r = ApiUnionMoneyModel::model()->unionInout(array(
                    'org_id' => $order['distributor_id'],
                    'user_id' => $data['user_id'] ? $data['user_id'] : 1,
                    'user_account' => $data['user_account'] ? $data['user_account'] : 'system',
                    'user_name' => $data['user_name'] ? $data['user_name'] : 'system',
                    'money' => $money,
                    'in_out' => 1,
                    'trade_type' => 2,
                    'frozen_type' => 1,
                    'remark' => $refund_apply_id,
                ));
                if (!$r || $r['code'] == 'fail') {
                    return false;
                }
            }
            $this->RefundApplyModel->commit();
            return $refund_apply_id;
        } catch (PDOException $e) {
            // 回滚事务
            $this->RefundApplyModel->rollBack();
            var_dump($e->errorInfo);
            return false;
        }
    }

    /**
     * 审核退款
     * author : yinjian
     * @param $data
     * @param $refund_apply
     */
    private function checkRefundApply($data, $refund_apply)
    {
        try {
            //$now = time();
            // 获取需审核的退款order_items
            $this->RefundApplyModel->begin();
            $tmp_refund_info = $this->RefundApplyModel->search(array('id'=>$data['id']));
            $tmp_refund_info = reset($tmp_refund_info);
            if($tmp_refund_info['allow_status'] == 1){
                // 这边可能被手动审核掉了，需要重新检测
                return false;
            }
            $refund = array();
            $refund['allow_status'] = $data['allow_status'];
            $refund['audited_by'] = $data['user_id'];
            $refund['updated_at'] = $this->now;
            $refund['op_id'] = intval($data['user_id']);
            // order_items
            if ($refund_apply['refund_items']) {
                $order_items_ids = json_decode($refund_apply['refund_items'], true);
            } else {
                $order_items = OrderItemModel::model()->search(array('status' => 0, 'use_time' => 0, 'order_id' => $refund_apply['order_id']), '*', null, $refund_apply['nums']);
                $order_items_ids = array_keys($order_items);
            }
            $orderInfo = $this->OrderModel->search(array('id' => $refund_apply['order_id']));
            $orderInfo = reset($orderInfo);
            $refunding_nums = $orderInfo['refunding_nums'] - $refund_apply['nums'];
            // 允许
            $refund['status'] = 1;
            $r = $this->allow($orderInfo, $refund_apply, $refunding_nums, $data, $order_items_ids);
            if(!$r){
                echo 'order'.$refund_apply['order_id'].'fail'."\n";
                return false;
            }
            if($orderInfo['local_source'] == 1) { //实际针对所有ota
                if(!OtaCallbackModel::model()->refund($orderInfo, $refund_apply, true)) {
                    return false;
                }
            }
            $this->RefundApplyModel->updateByAttr($refund, array('id' => $data['id']));
            $this->RefundApplyModel->commit();
            return true;
        } catch (PDOException $e) {
            // 回滚事务
            $this->RefundApplyModel->rollBack();
            print_r($e->errorInfo);
            return false;
        }
    }

    /**
     * 允许退款
     * author : yinjian
     */
    public function allow($orderInfo, $refund_apply, $refunding_nums, $data, $order_items_ids)
    {
        // 退票操作后需要对日库存更改
        $ticketTemplateInfo = TicketTemplateModel::model()->getInfo($orderInfo['product_id'], 0, $orderInfo['distributor_id'], $orderInfo['use_day'], 0 ,0);
        if (isset($ticketTemplateInfo['day_reserve']) && $ticketTemplateInfo['day_reserve'] > 0) {
            TicketTemplateModel::model()->updateTicketDayUsedReserve($orderInfo['product_id'], $orderInfo['rule_id'], $orderInfo['use_day'], $refund_apply['nums'], 1);
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
        $this->OrderModel->updateByAttr($orderParams, array('id' => $refund_apply['order_id']));
        // 产品数
//        $this->OrderItemModel->updateByAttr(array('refunding_nums' => $refunding_nums, 'refunded_nums' => $refunded_nums), array('order_id' => $refund_apply['order_id']));
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
            'user_name' => $orderInfo['user_name'],
            'created_at' => $this->now,
        ));
        // 抵用券
        if ($orderInfo['activity_paid'] > 0 && !in_array($orderInfo['payment'], $this->online_pay_types)) {
            $refund_apply['money'] = $refund_apply['money'] - $orderInfo['activity_paid'];
        }
        // 微信的票不需要退款
        if ($orderInfo && $orderInfo['ota_type'] != 'weixin') {
            // 储值款项退款到账户
            $refund_type_map = array('credit' => 1, 'advance' => 0);
            if (in_array($refund_apply['pay_type'], array('credit', 'advance'))) {
                $info['distributor_id'] = $refund_apply['distributor_id'];
                $info['supplier_id'] = $refund_apply['supplier_id'];
                $info['type'] = $refund_type_map[$refund_apply['pay_type']];
                $info['money'] = '+' . $refund_apply['money'];
                $info['remark'] = $refund_apply['id'];
                $info['op_id'] = intval($refund_apply['op_id']);
                $res = OrganizationModel::model()->addRefund($info);
                if (!$res) {
                    return false;
                }
            }
        }
        //平台支付的退票处理
        if (in_array($orderInfo['payment'], $this->online_pay_types) || $orderInfo['activity_paid'] > 0) {
            if (!in_array($orderInfo['payment'], $this->online_pay_types)) {
                $refund_apply['money'] = $orderInfo['activity_paid'];
            }
            $unionParams = array(
                'org_id' => intval($orderInfo['distributor_id']),
                'user_id' => $data['user_id'] ? intval($data['user_id']) : intval($this->body['user_id']),
                'user_account' => $data['user_account'] ? $data['user_account'] : strval($this->body['user_account']),
                'user_name' => $data['user_name'] ? $data['user_name'] : strval($this->body['user_name']),
                'money' => $refund_apply['money'],
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
                !empty($r['message']) && Lang_Msg::error($r['message']);
                return false;
            }
        }
        return true;
    }
}

$test = new Crontab_RefundChange();