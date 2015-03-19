<?php
require dirname(__FILE__) . '/Base.php';

class Crontab_Bill extends Process_Base
{
    protected $limit = 100;
    protected $interval = 600; //循环间隔（秒）
    public function run() {
        while (true) {
            $this->now = time();

            $ym = date("Y-m");
            $ym2Time = strtotime($ym);

            $frontYm = $ym2Time-$this->interval;

            $this->BillModel = BillModel::model();
            $this->BillitemModel = BillitemModel::model();
            $this->OrderModel = OrderModel::model();
            $this->OrderItemModel = OrderItemModel::model();
            $this->OrderModel->setAutoShare(0);
            $this->OrderModel->share($frontYm);
            $this->OrderItemModel->setAutoShare(0);
            $this->OrderItemModel->share($frontYm);
            $this->BillitemModel->share();
            $this->online_pay_types = array('alipay','kuaiqian','union');

            // 创建在线支付账单，包含平台支付
        	$this->genOnlineBill();
            $this->BillModel->rollBack();
            // 创建信用账单
            $this->genBill();
            $this->BillModel->rollBack();
            // 创建储值账单
            $this->genBill('advance',3);
            $this->BillModel->rollBack();

            $this->sleep($this->interval);
        }
    }

    public function genOnlineBill() {
        try {
            $this->BillModel->begin();
            $fields = " SELECT o.* ";
            $from = " FROM ".$this->OrderModel->getTable()." o JOIN ".$this->OrderItemModel->getTable()." i ON(i.order_id=o.id)";
            $where = " WHERE o.ota_type='system' AND o.status IN ('paid','finish') AND o.payment IN ('".implode("','",$this->online_pay_types)."')";
            $where .= " AND (o.nums=o.used_nums+o.refunded_nums OR i.expire_end<'".$this->now."') ";
            $orderBy = " ORDER BY o.id ASC ";
            $limit = " LIMIT 0,".$this->limit;
            $this->orders = $this->OrderModel->getDb()->selectBySql($fields.$from.$where.$orderBy.$limit);
            if (!$this->orders) return true;
            $orderIds = $tmpArr = array();
            foreach($this->orders as $k=>$v){
                array_push($orderIds,$v['id']);
                $tmpArr[$v['id']] = $v;
            }
            $this->orders = $tmpArr;
            // 更新finish
            $this->OrderModel->updateByAttr(array('status'=>'finish'), array('id|in'=>$orderIds));
            // 清除REDIS相关数据
            $this->rmRdsCache();
            // 创建在线支付BILL
            $tblname = $this->OrderModel->getTable();
            $sql = "select id,payment,supplier_id,supplier_name,distributor_id,distributor_name,sum(payed-refunded) as amount,count(id) as num, group_concat(id) as order_id from $tblname where id in(".implode(',', $orderIds).") group by supplier_id,distributor_id,payment";
            $list = $this->OrderModel->db->selectBySql($sql);
            $supplier_ids = array();
            foreach($list as $row) {
                !in_array($row['supplier_id'],$supplier_ids) && $supplier_ids[] = $row['supplier_id'];
            }
            $orgUnionMoney = $this->orgUnionMoney($supplier_ids);
            $billConf = ConfigModel::model()->getConfig(array('conf_bill_type', 'conf_bill_value'));
            foreach($list as $row) {
                $row['bill_type'] = $row['payment']=='union'?4:1;
                $key = $row['supplier_id'];
                if(isset($orgUnionMoney[$key]) && preg_match("/^\d+$/",$orgUnionMoney[$key]['balance_type'])
                    && ( ($orgUnionMoney[$key]['balance_type']==1 && $orgUnionMoney[$key]['balance_cycle']==date("w"))
                        ||  ($orgUnionMoney[$key]['balance_type']==2 && $orgUnionMoney[$key]['balance_cycle']==date("d"))
                    )
                ) {
                    $this->doCreateBill($row);
                }
                else if(($billConf['conf_bill_type']==1 && $billConf['conf_bill_value']==date("w"))
                    || ($billConf['conf_bill_type']==0 && $billConf['conf_bill_value']==date("d"))
                ){
                    $this->doCreateBill($row);
                }
            }
            $this->BillModel->commit();
        } catch (Exception $e) {
            $this->BillModel->rollBack();
            return false;
        }
    }

    public function genBill($payment='credit',$billType=2) {
        try {
            $this->BillModel->begin();
            $fields = " SELECT o.* ";
            $from = " FROM ".$this->OrderModel->getTable()." o JOIN ".$this->OrderItemModel->getTable()." i ON(i.order_id=o.id)";
            $where = " WHERE o.ota_type='system' AND o.status IN (".($payment=='advance'?"'paid'":"'paid','finish'").") AND o.payment='".$payment."'";
            $where .= " AND (o.nums=o.used_nums+o.refunded_nums OR i.expire_end<'".$this->now."') ";
            $orderBy = " ORDER BY o.id ASC ";
            $limit = " LIMIT 0,".$this->limit;
            $this->orders = $this->OrderModel->getDb()->selectBySql($fields.$from.$where.$orderBy.$limit);
            if (!$this->orders) return true;
            $orderIds = $tmpArr = array();
            foreach($this->orders as $k=>$v){
                array_push($orderIds,$v['id']);
                $tmpArr[$v['id']] = $v;
            }
            $this->orders = $tmpArr;
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
            $supplier_ids = array();
            foreach($list as $row) {
                !in_array($row['supplier_id'],$supplier_ids) && $supplier_ids[] = $row['supplier_id'];
            }
            $suppliers = OrganizationModel::model()->bySupplier(implode(',',$supplier_ids));
            $billConf = ConfigModel::model()->getConfig(array('conf_bill_type', 'conf_bill_value'));
            foreach($list as $row) {
                $row['bill_type'] = $billType;
                $key = $row['supplier_id']."_".$row['distributor_id'];
                if(isset($suppliers[$key]) && preg_match("/^\d+$/",$suppliers[$key]['checkout_type'])
                    && ( ($suppliers[$key]['checkout_type']==0 && $suppliers[$key]['checkout_date']==date("w"))
                        ||  ($suppliers[$key]['checkout_type']==1 && $suppliers[$key]['checkout_date']==date("d"))
                    )
                ) {
                    $this->doCreateBill($row);
                }
                else if(($billConf['conf_bill_type']==1 && $billConf['conf_bill_value']==date("w"))
                    || ($billConf['conf_bill_type']==0 && $billConf['conf_bill_value']==date("d"))
                ){
                    $this->doCreateBill($row);
                }
            }
            $this->BillModel->commit();
        } catch (Exception $e) {
            $this->BillModel->rollBack();
            return false;
        }
    }

    public function doCreateBill($row){
        $billId = $this->createBill($row);
        if($billId){
            $r = $this->createBillItems($billId, $row['order_id'],$row['payment']);
            if($r){ // 更新billed
                $r = $this->OrderModel->updateByAttr(array('status'=>'billed'), array('id|in'=>explode(',',$row['order_id'])) );
                if(!$r) $this->BillModel->rollBack();
            }
            else  $this->BillModel->rollBack();
        }
        else $this->BillModel->rollBack();
        return true;
    }

    public function createBill($row) {
        echo "createBill\n";
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
        $r = $this->BillModel->add($item);
        if($r) {
            $r = $this->addLog($billId,$row);
            if(!$r) return false;
        }
        echo json_encode($item);
        return $r?$billId:false;
    }

    /**
     *
     * 新增结算单号到流水
     */
    public function createBillItems($billId, $orderIds,$payment) {
        $where['order_id|in'] = explode(',', $orderIds);
        $rows = $this->OrderItemModel->search($where);
        foreach($rows as $row) {
            echo "createBillItems\n";
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
            echo json_encode($item);
            if(TransactionFlowModel::model()->search(array('order_id' => $row['order_id'], 'type' => 1))) {
                echo "transflow\n";
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

    private function orgUnionMoney($org_ids){
        $r = ApiUnionMoneyModel::model()->unionMoneyLists(array('org_ids'=>implode(',',$org_ids),'items'=>count($org_ids)));
        if($r && $r['code']=='succ' && $r['body']['data']){
            $tmp = array();
            foreach($r['body']['data'] as $v){
                $tmp[$v['org_id']] = $v;
            }
            return $tmp;
        }
        return array();
    }

}

$test = new Crontab_Bill();
