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

    private function runInit($supplier_id,$distributor_id,$body=array()){
        $this->supplier_id = $supplier_id;
        $this->distributor_id = $distributor_id;
        $this->body = $body;
        $this->now = time();
        $this->BillitemModel = BillitemModel::model();
        $this->OrderModel = OrderModel::model();
        $this->OrderItemModel = OrderItemModel::model();
        $this->OrderModel->setAutoShare(0);
        $this->OrderModel->share($this->now);
        $this->OrderItemModel->setAutoShare(0);
        $this->OrderItemModel->share($this->now);
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
        $r = $this->genBill('advance',3);
        if(is_array($r)) $bill_ids = $bill_ids+$r;
        return $bill_ids?$bill_ids:false;
    }

    private function genOnlineBill() {
        try {
            $this->orders = $this->OrderModel->search(array('supplier_id'=>$this->supplier_id,'ota_type'=>'system','status|in'=>array('paid','finish'),'payment|in'=>$this->online_pay_types));
            if (!$this->orders) return true;
            $orderIds = array_keys($this->orders);
            $where = array();
            $where['order_id|in'] = $orderIds;
            $where['or'] = array(
                'nums|exp' => '=used_nums+refunded_nums',
                'expire_end|<' => $this->now,
            );
            $finishs = $this->OrderItemModel->search($where, '*', 'id asc', '', 'order_id');
            if (!$finishs) return true;
            $orderIds = array_keys($finishs);
            // 更新finish
            $this->OrderModel->updateByAttr(array('status'=>'finish'), array('id|in'=>$orderIds));
            // 清除REDIS相关数据
            $this->rmRdsCache();
            // 创建在线支付BILL
            $tblname = $this->OrderModel->getTable();
            $sql = "select id,payment,supplier_id,supplier_name,distributor_id,distributor_name,sum(payed-refunded) as amount,count(id) as num, group_concat(id) as order_id from $tblname where id in(".implode(',', $orderIds).") group by supplier_id,distributor_id,payment";
            $list = $this->OrderModel->db->selectBySql($sql);
            $bill_ids = array();
            foreach($list as $row) {
                $row['bill_type'] = $row['payment']=='union'?4:1;
                $billId = $this->createBill($row);
                if(!$billId) return false;
                $bill_ids[] = $billId;
                $r = $this->createBillItems($billId, $row['order_id'],$row['payment']);
                if(!$r) return false;
            }
            // 更新billed
            $r = $this->OrderModel->updateByAttr(array('status'=>'billed'), array('id|in'=>$orderIds));
            if(!$r) return false;
            return $bill_ids;
        } catch (Exception $e) {
            return false;
        }
    }

    private function genBill($payment='credit',$billType=2) {
        try {
            $status = $payment=="advance" ? array('paid') : array('paid','finish');
            $this->orders = $this->OrderModel->search(array('supplier_id'=>$this->supplier_id,'distributor_id'=>$this->distributor_id,'ota_type'=>'system','status|in'=>$status,'payment'=>$payment));
            if (!$this->orders) return true;
            $orderIds = array_keys($this->orders);
            $where = array();
            $where['order_id|in'] = $orderIds;
            $where['or'] = array(
                'nums|exp' => '=used_nums+refunded_nums',
                'expire_end|<' => $this->now,
            );
            $finishs = $this->OrderItemModel->search($where, '*', 'id asc', '', 'order_id');
            if (!$finishs) return true;
            $orderIds = array_keys($finishs);
            // 更新finish
            $this->OrderModel->updateByAttr(array('status'=>'finish'), array('id|in'=>$orderIds));
            // 清除REDIS相关数据
            $this->rmRdsCache();
            if($payment=="advance") {
                $this->BillModel->commit();
                return true;
            }
            // 创建在线支付BILL
            $tblname = $this->OrderModel->getTable();
            $sql = "select id,payment,supplier_id,supplier_name,distributor_id,distributor_name,sum(payed-refunded) as amount,count(id) as num, group_concat(id) as order_id from $tblname where id in(".implode(',', $orderIds).") group by supplier_id,distributor_id";
            $list = $this->OrderModel->db->selectBySql($sql);
            $bill_ids = array();
            foreach($list as $row) {
                $row['bill_type'] = $billType;
                $billId = $this->createBill($row);
                if(!$billId) return false;
                $bill_ids[] = $billId;
                $r = $this->createBillItems($billId, $row['order_id'],$row['payment']);
                if(!$r) return false;
            }
            // 更新billed
            $r = $this->OrderModel->updateByAttr(array('status'=>'billed'), array('id|in'=>$orderIds));

            if(!$r) return false;
            return $bill_ids;
        } catch (Exception $e) {
            return false;
        }
    }

    private function createBill($row) {
        $billId = Util_Common::payid(2);
        $item = array();
        $item['id'] = $billId;
        $item['agency_id'] = in_array($row['payment'],$this->online_pay_types)?'-':$row['distributor_id'];
        $item['agency_name'] = in_array($row['payment'],$this->online_pay_types)?'-':$row['distributor_name'];
        $item['supply_id'] = $row['supplier_id'];
        $item['supply_name'] = $row['supplier_name'];
        $item['bill_type'] = $row['bill_type'];
        $item['bill_amount'] = $row['payment']=='advance'?0:$row['amount'];
        $item['bill_num'] = $row['num'];
        $item['created_at'] = $this->now;
        $item['updated_at'] = $this->now;
        $r = $this->add($item);
        if($r) {
            $r = $this->addLog($billId,$row);
            if(!$r) return false;
        }
        return $r ? $billId : false;
    }

    private function createBillItems($billId, $orderIds,$payment) {
        $where['order_id|in'] = explode(',', $orderIds);
        $rows = $this->OrderItemModel->search($where);
        foreach($rows as $row) {
            $order = $this->orders[$row['order_id']];
            $item = array();
            $item['id'] = Util_Common::payid(3);
            $item['bill_id'] = $billId;
            $item['order_id'] = $row['order_id'];
            $item['ticket_name'] = $row['name'];
            $item['owner_name'] = $order['owner_name'];
            $item['owner_mobile'] = $order['owner_mobile'];
            $item['payed'] = $payment=='advance'?0:$order['payed'];
            $item['refunded'] = $payment=='advance'?0:$order['refunded'];
            $item['bill_amount'] = $payment=='advance'?0:($order['payed']- $order['refunded']);
            $item['created_at'] = $this->now;
            $item['ordered_at'] = $order['created_at'];
            $item['use_day'] = $row['use_day'];
            $item['agency_id'] = $order['distributor_id'];
            $item['agency_name'] = $order['distributor_name'];
            $item['supply_id'] = $order['supplier_id'];
            $item['supply_name'] = $order['supplier_name'];
            $r = $this->BillitemModel->add($item);
            if(!$r) return false;
            if(TransactionFlowModel::model()->search(array('order_id' => $row['order_id'], 'type' => 1))) {
                //echo "transflow\n";
                $res = TransactionFlowModel::model()->updateByAttr(array('bill_id' => $billId), array('order_id' => $row['order_id'], 'type' => 1));
                if (!$res) return false;
            }
        }
        return true;
    }

    private function rmRdsCache() {
        foreach($this->orders as $value) {
            $this->OrderModel->delPhoneCardMap($value['owner_mobile'], $value['owner_card'], $value['id']);
        }
    }

    //结算记录日志
    private function addLog($billId,$row){
        $res = TransactionFlowModel::model()->add(array(
            'id'=>Util_Common::payid(),
            'mode'=>$row['payment'],
            'type'=>5,
            'amount'=>$row['payment']=='advance'?0:$row['amount'],
            'supplier_id'=>$row['supplier_id'],
            'agency_id'=>in_array($row['payment'],$this->online_pay_types)?'-':$row['distributor_id'],
            'ip'=>Tools::getIp(),
            'op_id'=>$this->body['user_id']?$this->body['user_id']:1,
            'created_at'=>time(),
            'bill_id'=>$billId,
            'user_name'=>$this->body['user_name']?$this->body['user_name']:'system',
            'balance'=>0,
            'remark'=>$row['order_id'],
        ));
        if(!$res) return false;
        return true;
    }

}
