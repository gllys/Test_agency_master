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

    private $online_pay_types = array('alipay','kuaiqian','union');

    public function getTable() {
        return $this->tblname;
    }

    /**
     *
     * author : yinjian
     * @param $order
     * @param $ota_info
     * @param $return_ticket
     */
    public function refundOrder($order,$order_item,$return_ticket,$data)
    {
        try {
            $now = time();
            $this->begin();
            $money = $order_item['price'] * $data['nums'];
            $refund_apply_id = Util_Common::payid(2);
            // order
            OrderModel::model()->setTable($order['id'])->updateByAttr(array('refunding_nums=refunding_nums+'.$data['nums']),array('id'=>$order['id']));
            OrderItemModel::model()->setTable($order['id'])->updateByAttr(array('refunding_nums=refunding_nums+'.$data['nums']),array('order_id'=>$order['id']));
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
                'pay_app_id' => $order['payment'],
                'order_at' => $order['created_at'],
                'distributor_id' => $order['distributor_id'],
                'supplier_id' => $order['supplier_id'],
                'landscape_id' => $order['landscape_ids'],
                'created_by' => $data['user_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ));
            foreach($return_ticket as $k => $v){
                // refund_apply_items 批量添加
                RefundApplyItemsModel::model()->add(array(
                    'refund_apply_id' => $refund_apply_id,
                    'ticket_id' => $v['id'],
                    'created_at'=> $now,
                    'updated_at' => $now,
                ));
                // tickets 批量更新
                TicketModel::model()->setTable($order['id'])->updateByAttr(array('status'=>0),array('id'=>$v['id']));
            }
            //平台支付的退票处理
            if(in_array($order['payment'],$this->online_pay_types)){
                $r = ApiUnionMoneyModel::model()->unionInout(array(
                    'org_id'=> $order['distributor_id'],
                    'user_id'=> $data['user_id']?$data['user_id']:$this->body['user_id'],
                    'user_account'=> $data['user_account']?$data['user_account']:$this->body['user_account'],
                    'user_name'=> $data['user_name']?$data['user_name']:$this->body['user_name'],
                    'money'=> $money,
                    'in_out'=> 1,
                    'trade_type'=> 2,
                    'frozen_type'=> 1,
                    'remark'=> $refund_apply_id,
                ));
                if(!$r || $r['code']=='fail'){
                    $this->rollBack();
                    !empty($r['message']) && Lang_Msg::error($r['message']);
                    return false;
                }
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
     * 拉手退款并审核
     * author : yinjian
     */
    public function refundOrderForLashou($order,$order_item,$return_ticket,$data)
    {
        try {
            $now = time();
            $this->begin();
            $money = $order_item['price'] * $data['nums'];
            $refund_apply_id = Util_Common::payid(2);
            // order 非在线支付直接打钱，减去订单正在退钱票数，加上订单中已退票数，加上订单中退款额度
            OrderModel::model()->setTable($order['id'])->updateByAttr(
                array(
                    'refunded_nums=refunded_nums+'.$data['nums'],
                    'refunded=refunded+'.$money,
                    'updated_at' => $now,
                ),
                array('id'=>$order['id'])
            );
            OrderItemModel::model()->setTable($order['id'])->updateByAttr(
                array(
                    'refunded_nums=refunded_nums+'.$data['nums'],
                    'updated_at' => $now,
                ),
                array('order_id'=>$order['id'])
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
            foreach($return_ticket as $k => $v){
                // refund_apply_items 批量添加
                RefundApplyItemsModel::model()->add(array(
                    'refund_apply_id' => $refund_apply_id,
                    'ticket_id' => $v['id'],
                    'created_at'=> $now,
                    'updated_at' => $now,
                ));
                // tickets 批量更新
                TicketModel::model()->setTable($order['id'])->updateByAttr(array('status'=>0,'updated_at'=>$now),array('id'=>$v['id']));
            }
            // 储值款项退款到账户
            $refund_type_map = array('credit'=>1,'advance'=>0);
            if(in_array($order['payment'],array('credit','advance'))){
                $info['distributor_id'] = $order['distributor_id'];
                $info['supplier_id'] = $order['supplier_id'];
                $info['type'] = $refund_type_map[$order['pay_type']];
                $info['money'] = '+'.$money;
                $info['remark'] = $data['remark'];
                $info['op_id'] = $data['user_id'];
                $res = OrganizationModel::model()->addRefund($info);
                if(!$res){
                    return false;
                }
            }
            // transflow
            $transflowParam = array(
                'id'=>Util_Common::payid(),
                'mode'=>$order['payment'],
                'type'=>2,
                'amount'=>$money,
                'supplier_id'=>$order['supplier_id'],
                'agency_id'=>$order['distributor_id'],
                'ip'=>Tools::getIp(),
                'op_id'=>$order['user_id'],
                'user_name' => $order['user_name'],
                'created_at'=>$now,
            );
            TransactionFlowModel::model()->add($transflowParam);
            // 退票操作后需要对日库存更改
            $ticketTemplateInfo = TicketTemplateModel::model()->getInfo($order_item['ticket_template_id'],$order_item['price_type'],$order_item['distributor_id'],$order_item['use_day'],0);
            if(isset($ticketTemplateInfo['day_reserve']) && $ticketTemplateInfo['day_reserve']>0){
                TicketTemplateModel::model()->updateTicketDayUsedReserve($order_item['ticket_template_id'],$ticketTemplateInfo['rule_id'],$order_item['use_day'],$data['nums'],1);
            }
            $this->commit();
            return $refund_apply_id;
        } catch (PDOException $e) {
            // 回滚事务
            $this->rollBack();
            return false;
        }
    }

    public function checkRefund($data,$refund_apply)
    {
        try {
            $now = time();
            $ticket = TicketModel::model()->setTable($refund_apply['order_id'])->search(array('order_id'=>$refund_apply['order_id'],'poi_used_num'=>0,'status'=>0,'deleted_at'=>0),'*',null,$refund_apply['nums']);
            $this->begin();
            $refund = array();
            $refund['allow_status'] = $data['allow_status'];
            $refund['audited_by'] = $data['user_id'];
            $refund['updated_at'] = $now;
            $refund['op_id'] = $data['user_id'];

            $orderInfo = OrderModel::model()->getById($refund_apply['order_id']);
            // 订单表判断
            if($data['allow_status']==3){
                // 审核拒绝，将订单中退款中数量减去对应的数目，根据票张数状态修改
                $refund['status'] = 2;
                $refund['reject_reason'] = $data['reject_reason'];
                OrderModel::model()->setTable($refund_apply['order_id'])->updateByAttr(
                    array(
                    'refunding_nums=refunding_nums-'.$refund_apply['nums'],
                    'updated_at'=>$now
                    ),
                    array('id'=>$refund_apply['order_id'])
                );
                OrderItemModel::model()->setTable($refund_apply['order_id'])->updateByAttr(
                    array(
                        'refunding_nums=refunding_nums-'.$refund_apply['nums'],
                        'updated_at'=>$now
                    ),
                    array('order_id'=>$refund_apply['order_id'])
                );
                TicketModel::model()->setTable($refund_apply['order_id'])->updateByAttr(array('status'=>1),array('id|in'=>array_keys($ticket)));

                //平台支付的退票处理
                if(in_array($orderInfo['payment'],$this->online_pay_types)){
                    $unionParams = array(
                        'org_id'=> $orderInfo['distributor_id'],
                        'user_id'=> $data['user_id']?$data['user_id']:$this->body['user_id'],
                        'user_account'=> $data['user_account']?$data['user_account']:$this->body['user_account'],
                        'user_name'=> $data['user_name']?$data['user_name']:$this->body['user_name'],
                        'money'=> $refund_apply['money'],
                        'in_out'=> 1,
                        'trade_type'=> 2,
                        'frozen_type'=> 0,
                        'remark'=> $data['id'],
                        'allow_refund'=>0,
                        'payment_id'=>$orderInfo['payment_id'],
                    );
                    $r = ApiUnionMoneyModel::model()->unionInout($unionParams);
                    if(!$r || $r['code']=='fail'){
                        $this->rollBack();
                        !empty($r['message']) && Lang_Msg::error($r['message']);
                        return false;
                    }
                }

            }else{
                // 审核通过
                // 退票操作后需要对日库存更改
                $order_item = reset(OrderItemModel::model()->setTable( $refund_apply['order_id'] )->search(array('order_id'=>$refund_apply['order_id'])));
                $ticketTemplateInfo = TicketTemplateModel::model()->getInfo($order_item['ticket_template_id'],$order_item['price_type'],$order_item['distributor_id'],$order_item['use_day'],0);
                if(isset($ticketTemplateInfo['day_reserve']) && $ticketTemplateInfo['day_reserve']>0){
                    TicketTemplateModel::model()->updateTicketDayUsedReserve($order_item['ticket_template_id'],$ticketTemplateInfo['rule_id'],$order_item['use_day'],$refund_apply['nums'],1);
                }

                $refund['status'] = 1;
                //减去订单正在退钱票数，加上订单中已退票数，加上订单中退款额度
                $orderParams = array(
                    'refunding_nums=refunding_nums-'.$refund_apply['nums'],
                    'refunded_nums=refunded_nums+'.$refund_apply['nums'],
                    'refunded=refunded+'.$refund_apply['money']
                );
                if($orderInfo['used_nums']==$orderInfo['nums']-$orderInfo['refunded_nums']-$refund_apply['nums']){
                    $orderParams[] = "status='finish'";
                }
                OrderModel::model()->setTable($refund_apply['order_id'])->updateByAttr($orderParams,
                    array('id'=>$refund_apply['order_id'])
                );
                OrderItemModel::model()->setTable($refund_apply['order_id'])->updateByAttr(
                    array(
                        'refunding_nums=refunding_nums-'.$refund_apply['nums'],
                        'refunded_nums=refunded_nums+'.$refund_apply['nums'],
                    ),
                    array('order_id'=>$refund_apply['order_id'])
                );
                $transflowParam = array(
                    'id'=>Util_Common::payid(),
                    'mode'=>$refund_apply['pay_app_id'],
                    'type'=>2,
                    'amount'=>$refund_apply['money'],
                    'supplier_id'=>$refund_apply['supplier_id'],
                    'agency_id'=>$refund_apply['distributor_id'],
                    'ip'=>Tools::getIp(),
                    'op_id'=>$orderInfo['user_id'],
                    'user_name' => $orderInfo['user_name'],
                    'created_at'=>$now,
                );
                TransactionFlowModel::model()->add($transflowParam);

                //微信的票不需要退款
                $tmp = reset( $ticket );
              	if( $orderInfo && $orderInfo[  'ota_type' ] != 'weixin' )
              	{
	                // 储值款项退款到账户@TODO
	                $refund_type_map = array('credit'=>1,'advance'=>0);
	                if(in_array($refund_apply['pay_type'],array('credit','advance'))){
	                    $info['distributor_id'] = $refund_apply['distributor_id'];
	                    $info['supplier_id'] = $refund_apply['supplier_id'];
	                    $info['type'] = $refund_type_map[$refund_apply['pay_type']];
	                    $info['money'] = '+'.$refund_apply['money'];
	                    $info['remark'] = $refund_apply['id'];
	                    $info['op_id'] = $refund_apply['op_id'];
	                    $res = OrganizationModel::model()->addRefund($info);
	                    if(!$res){
                            $this->rollBack();
	                        return false;
	                    }
	                }
              	}

                //平台支付的退票处理
                if(in_array($orderInfo['payment'],$this->online_pay_types)){
                    $unionParams = array(
                        'org_id'=> $orderInfo['distributor_id'],
                        'user_id'=> $data['user_id']?$data['user_id']:$this->body['user_id'],
                        'user_account'=> $data['user_account']?$data['user_account']:$this->body['user_account'],
                        'user_name'=> $data['user_name']?$data['user_name']:$this->body['user_name'],
                        'money'=> $refund_apply['money'],
                        'in_out'=> 1,
                        'trade_type'=> 2,
                        'frozen_type'=> 0,
                        'remark'=> $data['id'],
                        'allow_refund'=>1,
                        'payment_id'=>$orderInfo['payment_id'],
                        'payment'=>$orderInfo['payment'],
                    );
                    $r = ApiUnionMoneyModel::model()->unionInout($unionParams);
                    if(!$r || $r['code']=='fail'){
                        $this->rollBack();
                        !empty($r['message']) && Lang_Msg::error($r['message']);
                        return false;
                    }
                }

            }
            $this->updateByAttr($refund,array('id'=>$data['id']));
            $this->commit();
            return true;
        } catch (PDOException $e) {
            // 回滚事务
            $this->rollBack();
            return false;
        }
    }


    public function applyRefund($params){
        if(intval($params['nums'])<1) Lang_Msg::error('ERROR_REFUNDAPPLY_1');
        !$params['order_id'] && Lang_Msg::error('ERROR_REFUNDAPPLY_2');
        !$params['user_id'] && Lang_Msg::error('ERROR_REFUNDAPPLY_3');
        // 订单是否存在
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $order = $orderModel->setTable($params['order_id'])->get(array('id'=>$params['order_id'],'deleted_at'=>0));
        !$order && Lang_Msg::error('ERROR_REFUNDAPPLY_4');
        // 未支付单不能退款
        (!in_array($order['status'],array('paid','finish'))) && Lang_Msg::error('ERROR_REFUNDAPPLY_5');
        // 可退票数=总票数-已使用票数-退款中票数-已退款张数
        $remain_ticket = $order['nums'] - $order['used_nums'] -$order['refunding_nums'] -$order['refunded_nums'];
        ($params['nums']>$remain_ticket) && Lang_Msg::error('ERROR_REFUNDAPPLY_6');
        // 票模板为可退属性
        $order_item = $orderItemModel->setTable($params['order_id'])->get(array('order_id'=>$params['order_id']));
        $order_item['refund']==0 && Lang_Msg::error('ERROR_REFUNDAPPLY_7');
        // 获取退票
        $ticketModel = new TicketModel();
        $return_ticket = $ticketModel->setTable($params['order_id'])->search(array('order_id'=>$params['order_id'],'status'=>1,'poi_used_num'=>0,'deleted_at'=>0),'*',null,$params['nums']);
        // 申请退票操作
        $res = $this->refundOrder($order,$order_item,$return_ticket,$params);
        !$res && Lang_Msg::error('ERROR_REFUNDAPPLY_8');
        return $res;
    }

    public function checkApply($params){
        // 获取申请退款的id
        !Validate::isString($params['id']) && Lang_Msg::error("ERROR_CheckRefund_1");
        // 获取操作人id
        !Validate::isUnsignedId($params['user_id']) && Lang_Msg::error("ERROR_CheckRefund_2");
        // 获取审核操作
        !in_array(intval($params['allow_status']),array(1,3)) && Lang_Msg::error("ERROR_CheckRefund_3");
        // 拒绝理由
        if(intval($params['allow_status'])==3 && !Validate::isString(trim($params['reject_reason']))) Lang_Msg::error("ERROR_CheckRefund_4");

        $refund_apply = RefundApplyModel::model()->get(array('id'=>$params['id'],'is_del'=>0));
        !$refund_apply && Lang_Msg::error("ERROR_CheckRefund_5");
        // 判断该申请单有无人审核过
        ($refund_apply['audited_by']!=0) && Lang_Msg::error("ERROR_CheckRefund_6");
        $res = $this->checkRefund($params,$refund_apply);
        !$res && Lang_Msg::error("ERROR_CheckRefund_7");
        return $res;
    }

}