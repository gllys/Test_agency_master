<?php

/**
 * Class BillModel
 */
class BillModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'bills';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|BillModel|';

    public function getTable() {
        return $this->tblname;
    }

    /*
     * 可退、未用抵用券，结算已使用的产品数，不需判断是否过期
     * 不能退的订单可以直接全额结算，不需满足使用条件和过期条件
     * 对于使用抵用券的，如有使用就不能退，可直接全额结算，不需满足使用条件和过期条件
     * */
    private function runInit($supplier_id,$distributor_id,$body=array()){
        $this->supplier_id = $supplier_id;
        $this->distributor_id = $distributor_id;
        $this->body = $body;
        $this->now = time();
        $this->cancelTime = $this->now - 600; //扣除10分钟内可撤销核销时间
        $this->BillModel = BillModel::model();
        $this->BillitemModel = BillitemModel::model();
        $this->OrderModel = OrderModel::model();
        $this->OrderItemModel = OrderItemModel::model();
        //$this->OrderModel->setAutoShare(0);
        //$this->OrderModel->share($this->now);
        //$this->OrderItemModel->setAutoShare(0);
        //$this->OrderItemModel->share($this->now);
        $this->BillitemModel->share();
        $this->online_pay_types = array('alipay','kuaiqian','union');
    }

    public function runOnlineBill($supplier_id,$body=array()){
        if(!$supplier_id) return false;
        $this->runInit($supplier_id,0,$body);
        $bill_ids = array();
        // 创建在线支付账单，包含平台支付
        $r = $this->genOnlineBill();
        if(is_array($r)) $bill_ids = $bill_ids+$r;
        return $bill_ids?$bill_ids:false;
    }

    public function runBill($supplier_id,$distributor_id,$body=array()){
        if(!$supplier_id || !$distributor_id) return false;
        $this->runInit($supplier_id,$distributor_id,$body);

        $bill_ids = array();
        // 创建信用账单
        $r = $this->genBill();
        if(is_array($r)) $bill_ids = $bill_ids+$r;
        // 创建储值账单
        //$r = $this->genBill('advance',3);
        //if(is_array($r)) $bill_ids = $bill_ids+$r;
        return $bill_ids?$bill_ids:false;
    }

    //结算在线支付账单
    private function genOnlineBill() {
        try { //份成两部分统计：订单全部票，订单部分票
            $status = array('paid','finish');
            //可退，未用抵用券，结算已使用的产品数的金额(产品数*单价)
            $sql1 = " SELECT oi.order_id,o.payment,o.supplier_id,o.supplier_name,o.distributor_id,o.distributor_name,o.nums,o.use_day";
            $sql1 .= " ,o.owner_name,owner_mobile,o.name,o.payed,o.refunded,o.activity_paid,o.created_at, sum(oi.price) as amount,0 as whole_order";
            $sql1 .= " FROM ".$this->OrderItemModel->getTable()." oi left join ".$this->OrderModel->getTable()." o ON(oi.order_id=o.id)";
            $sql1 .= " WHERE o.supplier_id={$this->supplier_id} AND o.ota_type='system' AND o.payment IN ('".implode("','",$this->online_pay_types)."')";
            $sql1 .= " AND o.status in ('".implode("','",$status)."') AND oi.bill_time=0 AND oi.price>0";
            $sql1 .= " AND o.refund=1 AND (o.activity_paid=0 OR o.activity_paid IS NULL) AND oi.use_time>0 AND oi.use_time<{$this->cancelTime}";
            $sql1 .= " GROUP BY oi.order_id";
            $orders1 = $this->OrderModel->db->selectBySql($sql1);
            //$sql .= " UNION ";
            //1.不可退，2.可退、使用抵用券、已有使用。结算整个订单金额(金额不用考虑抵用券)
            $sql2 = " SELECT id as order_id,payment,supplier_id,supplier_name,distributor_id,distributor_name,nums,use_day";
            $sql2 .= ",owner_name,owner_mobile,name,payed,refunded,activity_paid,created_at,amount,1 as whole_order";
            $sql2 .= " FROM ".$this->OrderModel->getTable();
            $sql2 .= " WHERE supplier_id={$this->supplier_id} AND ota_type='system' AND payment IN ('".implode("','",$this->online_pay_types)."')";
            $sql2 .= " AND status in ('".implode("','",$status)."') AND amount>0";
            $sql2 .= " AND (refund=0 OR (activity_paid>0 AND used_nums>0)) AND updated_at<{$this->cancelTime}";
            $orders2 = $this->OrderModel->db->selectBySql($sql2);
            $orders = array_merge($orders1,$orders2);
            // 更新billed
            foreach($orders as $v){
                $r = $this->createBillItems($v);
                if(!$r) return false;
            }
            return $this->createBill('online');
        } catch (Exception $e) {
            return false;
        }
    }

    //结算信用账单
    private function genBill($payment='credit',$billType=2) {
        try { //份成两部分统计：订单全部票，订单部分票
            $status = $payment=="advance" ? array('paid') : array('paid','finish');
            //可退，未用抵用券，结算已使用的产品数的金额(产品数*单价)
            $sql1 = " SELECT oi.order_id,o.payment,o.supplier_id,o.supplier_name,o.distributor_id,o.distributor_name,o.nums,o.use_day";
            $sql1 .= " ,o.owner_name,owner_mobile,o.name,o.payed,o.refunded,o.activity_paid,o.created_at, sum(oi.price) as amount,0 as whole_order";
            $sql1 .= " FROM ".$this->OrderItemModel->getTable()." oi left join ".$this->OrderModel->getTable()." o ON(oi.order_id=o.id)";
            $sql1 .= " WHERE o.supplier_id={$this->supplier_id} AND o.distributor_id={$this->distributor_id} AND o.ota_type='system' AND o.payment='{$payment}'";
            $sql1 .= " AND o.status in ('".implode("','",$status)."') AND oi.bill_time=0 AND oi.price>0";
            $sql1 .= " AND o.refund=1 AND (o.activity_paid=0 OR o.activity_paid IS NULL) AND oi.use_time>0 AND oi.use_time<{$this->cancelTime}";
            $sql1 .= " GROUP BY oi.order_id";
            $orders1 = $this->OrderModel->db->selectBySql($sql1);
            //$sql .= " UNION ";
            //1.不可退，2.可退、使用抵用券、已有使用。结算整个订单金额(金额不用考虑抵用券)
            $sql2 = " SELECT id as order_id,payment,supplier_id,supplier_name,distributor_id,distributor_name,nums,use_day";
            $sql2 .= ",owner_name,owner_mobile,name,payed,refunded,activity_paid,created_at,amount,1 as whole_order";
            $sql2 .= " FROM ".$this->OrderModel->getTable();
            $sql2 .= " WHERE supplier_id={$this->supplier_id} AND distributor_id={$this->distributor_id} AND ota_type='system' AND payment='{$payment}'";
            $sql2 .= " AND status in ('".implode("','",$status)."') AND amount>0";
            $sql2 .= " AND (refund=0 OR (activity_paid>0 AND used_nums>0)) AND updated_at<{$this->cancelTime}";
            $orders2 = $this->OrderModel->db->selectBySql($sql2);
            $orders = array_merge($orders1,$orders2);

            $orderIds = array();
            foreach($orders as $row) {
                $orderIds[] = $row['order_id'];
            }
            if($payment=="advance") {
                $finishOrders = $this->OrderModel->seach(array('id|in'=>$orderIds,'nums|EXP'=>'=used_nums+refunded_nums'));
                $this->OrderModel->updateByAttr(array('status'=>'finish'), array('id|in'=>array_keys($finishOrders),'nums|EXP'=>'=used_nums+refunded_nums'));
                // 清除REDIS相关数据
                $this->rmRdsCache($finishOrders);
                return true;
            }
            // 更新billed
            foreach($orders as $v){
                $r = $this->createBillItems($v);
                if(!$r) return false;
            }
            return $this->createBill($payment);
        } catch (Exception $e) {
            return false;
        }
    }

    //创建结算明细
    private function createBillItems($order) {
        $item = array();
        $item['id'] = Util_Common::payid(3);
        $item['bill_id'] = 0;
        $item['order_id'] = $order['order_id'];
        $item['ticket_name'] = $order['name'];
        $item['owner_name'] = $order['owner_name'];
        $item['owner_mobile'] = $order['owner_mobile'];
        $item['payment'] = $order['payment'];
        $item['payed'] = $order['payed'];
        $item['refunded'] = $order['refunded'];
        $item['bill_amount'] = $order['amount'];
        $item['created_at'] = $this->now;
        $item['ordered_at'] = $order['created_at'];
        $item['use_day'] = $order['use_day'];
        $item['agency_id'] = $order['distributor_id'];
        $item['agency_name'] = $order['distributor_name'];
        $item['supply_id'] = $order['supplier_id'];
        $item['supply_name'] = $order['supplier_name'];
        $r = $this->BillitemModel->add($item);
        if(!$r) return false;
        return true;
    }

    //生成结算单
    public function createBill($pay_type='online') {
        $tblname = $this->BillitemModel->getTable();
        $where = " bill_id=0 ";
        if($pay_type=='online'){
            $where .= " AND payment IN ('".implode("','",$this->online_pay_types)."') ";
            $groupBy = "supply_id";
        }
        else if($pay_type=='credit'){
            $where .= " AND payment='credit' ";
            $groupBy = "supply_id,agency_id";
        }

        $sql = "select supply_id,supply_name,agency_id,agency_name,payment,sum(bill_amount) as amount,count(id) as num from $tblname where {$where} group by {$groupBy}";
        $lists = $this->BillitemModel->db->selectBySql($sql);
        $billIds = array();
        foreach($lists as $row){
            $billId = Util_Common::payid(2);
            $item = array();
            $item['id'] = $billId;
            $item['agency_id'] = $pay_type=='online'?'-':$row['agency_id'];
            $item['agency_name'] = $pay_type=='online'?'-':$row['agency_name'];
            $item['supply_id'] = $row['supply_id'];
            $item['supply_name'] = $row['supply_name'];
            $item['bill_type'] = $pay_type=='online'?($row['payment']=='union'?4:1):2;
            $item['bill_amount'] = $row['amount'];
            $item['bill_num'] = $row['num'];
            $item['created_at'] = $this->now;
            $item['updated_at'] = $this->now;
            $r = $this->BillModel->add($item);
            if($r) {
                $orderIds = array();
                $whOrder=" AND supply_id=".$row['supply_id'];
                if($pay_type=='credit'){
                    $whOrder.=" AND agency_id=".$row['agency_id'];
                }
                $orders = $this->BillitemModel->db->selectBySql("select order_id from $tblname where {$where} {$whOrder}");
                foreach($orders as $v){
                    $orderIds[] = $v['order_id'];
                }
                $this->chgOrderStatus($orderIds); //更改订单状态
                $r = $this->BillitemModel->updateByAttr(array('bill_id'=>$billId),array('order_id|in'=>$orderIds,'bill_id'=>0));
                if(!$r) {
                    return false;
                }

                $r = $this->addLog($billId,$row,$orderIds);
                if(!$r) {
                    return false;
                }
            }
            $billIds[] = $billId;
        }
        return $billIds;
    }

    private function chgOrderStatus($orderIds){ //更改订单状态
        if(!$orderIds) return false;
        /*找出可更改状态为billed的订单，并更改订单状态*/
        $whereBill1 = array('id|in' => $orderIds, 'nums|EXP' => '=used_nums+refunded_nums','use_time|<' => $this->cancelTime);
        $finishOrders1 = $this->OrderModel->search($whereBill1);
        $finishOrderIds1 = $whereBill['id|in'] = array_keys($finishOrders1);
        if($finishOrderIds1){
            $this->OrderModel->updateByAttr(array('status' => 'billed','bill_status'=>1,"billed_nums=used_nums"), $whereBill1); /*更改订单的状态*/
            $this->OrderItemModel->updateByAttr(array('bill_time' => $this->now), array('order_id|in' => $finishOrderIds1,'use_time|>'=>0));/*更改订单明细的状态*/
            // 清除REDIS相关数据
            $this->rmRdsCache($finishOrders1);
        }
        $whereBill2 = array(
            'id|in' => $orderIds,
            'or' => array(
                'refund' => 0,
                'and' => array(
                    'activity_paid|>' => 0,
                    'used_nums|>' => 0,
                    'use_time|<' => $this->cancelTime, //过了撤销核销时间
                ),
            )
        );
        $finishOrders2 = $this->OrderModel->search($whereBill2);
        $finishOrderIds2 = $whereBill['id|in'] = array_keys($finishOrders2);
        if($finishOrderIds2){
            $this->OrderModel->updateByAttr(array('status' => 'billed','bill_status'=>1,"billed_nums=nums"), $whereBill2); /*更改订单的状态*/
            $this->OrderItemModel->updateByAttr(array('bill_time' => $this->now), array('order_id|in' => $finishOrderIds2));/*更改订单明细的状态*/
            // 清除REDIS相关数据
            $this->rmRdsCache($finishOrders2);
        }
        $finishOrderIds = array_merge($finishOrderIds1,$finishOrderIds2);

        /*更改部分使用的订单的订单明细状态*/
        $itemOrderIds = array_diff($orderIds, $finishOrderIds);
        if($itemOrderIds){
            $this->OrderModel->updateByAttr(
                array("billed_nums=used_nums"),
                array('id|in' => $itemOrderIds, 'use_time|>' => 0, 'use_time|<' => $this->cancelTime)
            ); /*更改订单的结算数据*/
            $this->OrderItemModel->updateByAttr(
                array('bill_time' => $this->now),
                array('order_id|in' => $itemOrderIds, 'use_time|>' => 0, 'use_time|<' => $this->cancelTime, 'bill_time' => 0)
            );
        }
        return true;
    }

    private function rmRdsCache($orders) {
        foreach($orders as $value) {
            $this->OrderModel->delPhoneCardMap($value['owner_mobile'], $value['owner_card'], $value['code']);
        }
    }

    //结算记录日志
    private function addLog($billId,$row,$orderIds){
        $res = TransactionFlowModel::model()->add(array(
            'id'=>Util_Common::payid(),
            'mode'=>$row['payment'],
            'type'=>5,
            'amount'=>$row['amount'],
            'supplier_id'=>$row['supply_id'],
            'agency_id'=>in_array($row['payment'],$this->online_pay_types)?'-':$row['agency_id'],
            'ip'=>Tools::getIp(),
            'op_id'=>$this->body['user_id']?$this->body['user_id']:1,
            'created_at'=>time(),
            'bill_id'=>$billId,
            'user_name'=>$this->body['user_name']?$this->body['user_name']:'system',
            'balance'=>0,
            'remark'=>implode(',',$orderIds),
        ));
        if(!$res) return false;
        if(TransactionFlowModel::model()->search(array('order_id|in'=>explode(',',$row['order_ids']), 'type' => 1))) {
            $res = TransactionFlowModel::model()->updateByAttr(array('bill_id' => $billId), array('order_id|in' =>explode(',',$row['order_ids']), 'type' => 1));
            if (!$res) return false;
        }
        return true;
    }

}
