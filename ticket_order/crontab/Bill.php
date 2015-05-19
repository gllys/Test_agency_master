<?php
require dirname(__FILE__) . '/Base.php';

class Crontab_Bill extends Process_Base
{
    protected $limit = 100;
    protected $interval = 28800; //循环间隔（3600秒*8）
    //private $rdsCachePre = "Crontab_Bill_Rds_";

    public function run() {
        while (true) {
            $this->now = time();
            $this->hexiao_at = $this->now;
            $this->cycle = 'month';

            $this->SmsModel = SmsModel::model();
            $this->BillModel = BillModel::model();
            $this->BillitemModel = BillitemModel::model();
            $this->OrderModel = OrderModel::model();
            $this->OrderItemModel = OrderItemModel::model();
            $this->online_pay_types = array('alipay','kuaiqian','union');
            $this->billConf = ConfigModel::model()->getConfig(array('conf_bill_type', 'conf_bill_value'));

            //-------------- 按月结算明细 start--------------
            $this->setOrderTable('month');
            // 创建在线支付账单，包含平台支付
            $this->genOnlineBill();
            $this->BillModel->rollBack();
            // 创建信用账单
            $this->genBill();
            $this->BillModel->rollBack();
            // 创建储值账单
            //$this->genBill('advance',3);
            //$this->BillModel->rollBack();
            // -------------- 按月结算明细 end--------------


            // -------------- 按周结算明细 start--------------
            $this->setOrderTable('week');
            // 创建在线支付账单，包含平台支付
            $this->genOnlineBill();
            $this->BillModel->rollBack();
            // 创建信用账单
            $this->genBill();
            $this->BillModel->rollBack();
            // 创建储值账单
            //$this->genBill('advance',3);
            //$this->BillModel->rollBack();
            // -------------- 按周结算明细 end--------------
            $this->sleep($this->interval);
        }
    }

    //按月、周设置订单表
    private function setOrderTable($cycle='month'){
        $this->cycle = $cycle;
        if($cycle=='month'){
            $ym2Time = strtotime(date("Y-m")); //当月1日00:00:00的时间戳
            $frontYm = $ym2Time-1; //上月最后一天23:59:59时间戳
            //$this->OrderModel->share($frontYm);
            //$this->OrderItemModel->share($frontYm);
            $this->hexiao_at = $frontYm;
        }
        else if($cycle=='week'){
            $today2Time = strtotime(date("Y-m-d"));//当天00:00:00的时间戳
            $w = date("w"); //当天星期 0周日～6周六
            $frontW = $today2Time-$w*86400-1; //上星期六23:59:59时间戳
            //$this->OrderModel->share($frontW);
            //$this->OrderItemModel->share($frontW);
            $this->hexiao_at = $frontW;
        }
    }

    //结算在线支付账单
    private function genOnlineBill() {
        $this->BillModel->begin();
        try { //份成两部分统计：订单全部票，订单部分票
            $status = array('paid','finish');
            $this->startNums = 0;
            while(1) {
                $limit = " LIMIT ".$this->startNums.",".$this->limit;
                //可退，未用抵用券，结算已使用的产品数的金额(产品数*单价)
                $sql1 = " SELECT oi.order_id,o.payment,o.supplier_id,o.supplier_name,o.distributor_id,o.distributor_name,o.nums,o.use_day";
                $sql1 .= " ,o.owner_name,owner_mobile,o.name,o.payed,o.refunded,o.activity_paid,o.created_at, sum(oi.price) as amount,0 as whole_order";
                $sql1 .= " FROM " . $this->OrderItemModel->getTable() . " oi left join " . $this->OrderModel->getTable() . " o ON(oi.order_id=o.id)";
                $sql1 .= " WHERE o.ota_type='system' AND o.payment IN ('" . implode("','", $this->online_pay_types) . "')";
                $sql1 .= " AND o.status in ('" . implode("','", $status) . "') AND oi.bill_time=0 AND oi.price>0";
                $sql1 .= " AND o.refund=1 AND (o.activity_paid=0 OR o.activity_paid IS NULL) AND oi.use_time>0 AND oi.use_time<{$this->hexiao_at}";
                $sql1 .= " GROUP BY oi.order_id";
                $sql1 .= $limit;
                $orders1 = $this->OrderModel->db->selectBySql($sql1);
                //$sql .= " UNION ";
                //1.不可退，2.可退、使用抵用券、已有使用。结算整个订单金额(金额不用考虑抵用券)
                $sql2 = " SELECT id as order_id,payment,supplier_id,supplier_name,distributor_id,distributor_name,nums,use_day";
                $sql2 .= ",owner_name,owner_mobile,name,payed,refunded,activity_paid,created_at,amount,1 as whole_order";
                $sql2 .= " FROM " . $this->OrderModel->getTable();
                $sql2 .= " WHERE ota_type='system' AND payment IN ('" . implode("','", $this->online_pay_types) . "')";
                $sql2 .= " AND status in ('" . implode("','", $status) . "') AND amount>0";
                $sql2 .= " AND (refund=0 OR (activity_paid>0 AND used_nums>0)) AND updated_at<{$this->hexiao_at}";
                $sql2 .= $limit;
                $orders2 = $this->OrderModel->db->selectBySql($sql2);

                if(!$orders1 && !$orders2) break;
                $orders = array_merge($orders1, $orders2);

                echo "\n[{$this->cycle}]Online Orders: ".count($orders1)."|".count($orders2)." ||{$limit}\n";

                $supplier_ids = array();
                foreach ($orders as $row) {
                    $supplier_ids[] = $row['supplier_id'];
                }
                $supplier_ids = array_unique($supplier_ids);
                $orgUnionMoney = $this->orgUnionMoney($supplier_ids);

                // 更新billed
                foreach ($orders as $v) {
                    $key = $v['supplier_id'];
                    if ($this->cycle == 'month') {
                        if (isset($orgUnionMoney[$key]) && is_numeric($orgUnionMoney[$key]['balance_type'])
                            && $orgUnionMoney[$key]['balance_type']>0
                        ) {
                            if($orgUnionMoney[$key]['balance_type'] == 2 && $orgUnionMoney[$key]['balance_cycle'] == date("j")){
                                $r = $this->createBillItems($v);
                                if (!$r) return false;
                            }
                        } else if ($this->billConf['conf_bill_type'] == 0 && $this->billConf['conf_bill_value'] == date("j")) {
                            $r = $this->createBillItems($v);
                            if (!$r) return false;
                        }
                    } else if ($this->cycle == 'week') {
                        if (isset($orgUnionMoney[$key]) && is_numeric($orgUnionMoney[$key]['balance_type'])
                            && $orgUnionMoney[$key]['balance_type']>0
                        ) {
                            if($orgUnionMoney[$key]['balance_type'] == 1 && $orgUnionMoney[$key]['balance_cycle'] == date("w")){
                                $r = $this->createBillItems($v);
                                if (!$r) return false;
                            }
                        } else if ($this->billConf['conf_bill_type'] == 1 && $this->billConf['conf_bill_value'] == date("w")) {
                            $r = $this->createBillItems($v);
                            if (!$r) return false;
                        }
                    }
                }
                $this->startNums += $this->limit;
            } //---while end---
            $r = $this->createBill('online');
            if($r)
                $this->BillModel->commit();
            else
                $this->BillModel->rollBack();
        } catch (Exception $e) {
            $this->BillModel->rollBack();
        }
    }

    //结算信用账单
    private function genBill($payment='credit') {
        $this->BillModel->begin();
        try { //份成两部分统计：订单全部票，订单部分票
            $status = $payment=="advance" ? array('paid') : array('paid','finish');
            $this->startNums = 0;
            while(1) {
                $limit = " LIMIT ".$this->startNums.",".$this->limit;
                if ($payment == "advance") {
                    $finishOrders = $this->OrderModel->seach(array(
                        'ota_type'=>'system',
                        'payment'=>$payment,
                        'updated_at|<' => $this->hexiao_at,
                        'nums|EXP' => '=used_nums+refunded_nums'
                    ),"id,code",null,$this->startNums.",".$this->limit);
                    if($finishOrders){
                        $this->OrderModel->updateByAttr(array('status' => 'finish'), array('id|in' => array_keys($finishOrders), 'nums|EXP' => '=used_nums+refunded_nums'));
                        // 清除REDIS相关数据
                        $this->rmRdsCache($finishOrders);
                        $this->startNums += $this->limit;
                    }
                    else {
                        break;
                    }
                    continue;

                } else {
                    //可退，未用抵用券，结算已使用的产品数的金额(产品数*单价)
                    $sql1 = " SELECT oi.order_id,o.payment,o.supplier_id,o.supplier_name,o.distributor_id,o.distributor_name,o.nums,o.use_day";
                    $sql1 .= " ,o.owner_name,owner_mobile,o.name,o.payed,o.refunded,o.activity_paid,o.created_at, sum(oi.price) as amount,0 as whole_order";
                    $sql1 .= " FROM " . $this->OrderItemModel->getTable() . " oi left join " . $this->OrderModel->getTable() . " o ON(oi.order_id=o.id)";
                    $sql1 .= " WHERE o.ota_type='system' AND o.payment='{$payment}'";
                    $sql1 .= " AND o.status in ('" . implode("','", $status) . "') AND oi.bill_time=0 AND oi.price>0";
                    $sql1 .= " AND o.refund=1 AND (o.activity_paid=0 OR o.activity_paid IS NULL) AND oi.use_time>0 AND oi.use_time<{$this->hexiao_at}";
                    $sql1 .= " GROUP BY oi.order_id";
                    $sql1 .= $limit;
                    $orders1 = $this->OrderModel->db->selectBySql($sql1);
                    //$sql .= " UNION ";
                    //1.不可退，2.可退、使用抵用券、已有使用。结算整个订单金额(金额不用考虑抵用券)
                    $sql2 = " SELECT id as order_id,payment,supplier_id,supplier_name,distributor_id,distributor_name,nums,use_day";
                    $sql2 .= ",owner_name,owner_mobile,name,payed,refunded,activity_paid,created_at,amount,1 as whole_order";
                    $sql2 .= " FROM " . $this->OrderModel->getTable();
                    $sql2 .= " WHERE ota_type='system' AND payment='{$payment}'";
                    $sql2 .= " AND status in ('" . implode("','", $status) . "') AND amount>0";
                    $sql2 .= " AND (refund=0 OR (activity_paid>0 AND used_nums>0)) AND updated_at<{$this->hexiao_at}";
                    $sql2 .= $limit;
                    $orders2 = $this->OrderModel->db->selectBySql($sql2);

                    if(!$orders1 && !$orders2) break;
                    $orders = array_merge($orders1, $orders2);

                    echo "\n[{$this->cycle}]{$payment} Orders: ".count($orders1)."|".count($orders2)." ||{$limit}\n";

                    $supplier_ids = $orderIds = array();
                    foreach ($orders as $row) {
                        $orderIds[] = $row['order_id'];
                        $supplier_ids[] = $row['supplier_id'];
                    }

                    $supplier_ids = array_unique($supplier_ids);
                    $suppliers = OrganizationModel::model()->bySupplier(implode(',', $supplier_ids));

                    // 更新billed
                    foreach ($orders as $v) {
                        $key = $v['supplier_id'] . "_" . $v['distributor_id'];
                        if ($this->cycle == 'month') {
                            if (isset($suppliers[$key]) && is_numeric($suppliers[$key]['checkout_type'])
                                && $suppliers[$key]['checkout_type']>=0
                            ) {
                                if($suppliers[$key]['checkout_type'] == 1 && $suppliers[$key]['checkout_date'] == date("j")){
                                    $r = $this->createBillItems($v);
                                    if (!$r) return false;
                                }
                            } else if ($this->billConf['conf_bill_type'] == 0 && $this->billConf['conf_bill_value'] == date("j")) {
                                $r = $this->createBillItems($v);
                                if (!$r) return false;
                            }
                        } else if ($this->cycle == 'week') {
                            if (isset($suppliers[$key]) && is_numeric($suppliers[$key]['checkout_type'])
                                && $suppliers[$key]['checkout_type']>=0
                            ) {
                                if($suppliers[$key]['checkout_type'] == 0 && $suppliers[$key]['checkout_date'] == date("w")){
                                    $r = $this->createBillItems($v);
                                    if (!$r) return false;
                                }
                            } else if ($this->billConf['conf_bill_type'] == 1 && $this->billConf['conf_bill_value'] == date("w")) {
                                $r = $this->createBillItems($v);
                                if (!$r) return false;
                            }
                        }
                    }
                    $this->startNums += $this->limit;
                }
            } //end while

            if($payment!='advance') {
                $r = $this->createBill($payment);
                if($r)
                    $this->BillModel->commit();
                else
                    $this->BillModel->rollBack();
            } else{
                $this->BillModel->commit();
            }
        } catch (Exception $e) {
            $this->BillModel->rollBack();
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
        echo "\ncreateBillItems\n";
        echo json_encode($item);
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
                $r = $this->chgOrderStatus($orderIds); //更改订单状态
                if(!$r) return false;
                $r = $this->BillitemModel->updateByAttr(array('bill_id'=>$billId),array('order_id|in'=>$orderIds,'bill_id'=>0));
                if(!$r) return false;
                $r = $this->addLog($billId,$row,$orderIds);
                if(!$r) return false;
            }
            $billIds[] = $billId;
            echo "\ncreateBill\n";
            echo json_encode($item);
        }
        return $billIds;
    }

    private function chgOrderStatus($orderIds){ //更改订单状态
        if (!$orderIds) return false;
        /*找出可更改状态为billed的订单，并更改订单状态*/
        $whereBill1 = array('id|in' => $orderIds, 'nums|EXP' => '=used_nums+refunded_nums','use_time|<' => $this->hexiao_at); //可退的订单
        $finishOrders1 = $this->OrderModel->search($whereBill1);
        $finishOrderIds1 = $whereBill1['id|in'] = array_keys($finishOrders1);
        if ($finishOrderIds1) {
            $r = $this->OrderModel->updateByAttr(array('status' => 'billed','bill_status'=>1,"billed_nums=used_nums"), $whereBill1); /*更改订单的状态*/
            if (!$r) return false;
            $r = $this->OrderItemModel->updateByAttr(array('bill_time' => $this->now), array('order_id|in' => $finishOrderIds1,'use_time|>'=>0));/*更改订单明细的状态*/
            if (!$r) return false;
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
                    'use_time|<' => $this->hexiao_at, //过了撤销核销时间
                ),
            )
        );
        $finishOrders2 = $this->OrderModel->search($whereBill2);
        $finishOrderIds2 = $whereBill2['id|in'] = array_keys($finishOrders2);
        if ($finishOrderIds2) {
            $r = $this->OrderModel->updateByAttr(array('status' => 'billed','bill_status'=>1,"billed_nums=nums"), $whereBill2); /*更改订单的状态*/
            if (!$r) return false;
            $r = $this->OrderItemModel->updateByAttr(array('bill_time' => $this->now), array('order_id|in' => $finishOrderIds2));/*更改订单明细的状态*/
            if (!$r) return false;
            // 清除REDIS相关数据
            $this->rmRdsCache($finishOrders2);
        }
        $finishOrderIds = array_merge($finishOrderIds1,$finishOrderIds2);

        /*更改部分使用的订单的订单明细状态*/
        $itemOrderIds = array_diff($orderIds, $finishOrderIds);
        if ($itemOrderIds) {
            $this->OrderModel->updateByAttr(
                array("billed_nums=used_nums"),
                array('id|in' => $itemOrderIds, 'use_time|>' => 0, 'use_time|<' => $this->hexiao_at)
            ); /*更改订单的结算数据*/
            $r = $this->OrderItemModel->updateByAttr(
                array('bill_time' => $this->now),
                array('order_id|in' => $itemOrderIds, 'use_time|>' => 0, 'use_time|<' => $this->hexiao_at, 'bill_time' => 0)
            );
            if (!$r) return false;
        }
        return true;
    }

    //结算记录日志
    private function addLog($billId,$row,$orderIds){
        try {
            $res = TransactionFlowModel::model()->add(array(
                'id' => Util_Common::payid(),
                'mode' => $row['payment'],
                'type' => 5,
                'amount' => $row['amount'],
                'supplier_id' => $row['supply_id'],
                'agency_id' => in_array($row['payment'], $this->online_pay_types) ? '-' : $row['agency_id'],
                'ip' => Tools::getIp(),
                'op_id' => 1,
                'created_at' => $this->now,
                'bill_id' => $billId,
                'user_name' => 'system',
                'balance' => 0,
                'remark' => implode(',',$orderIds),
            ));
            if (!$res) return false;
            if (TransactionFlowModel::model()->search(array('order_id|in' => explode(',', $row['order_ids']), 'type' => 1))) {
                $res = TransactionFlowModel::model()->updateByAttr(array('bill_id' => $billId), array('order_id|in' => explode(',', $row['order_ids']), 'type' => 1));
                if (!$res) return false;
            }
            return true;
        } catch(Exception $e){
            return false;
        }
    }

    //获取机构平台设置
    private function orgUnionMoney($org_ids){
        $r = ApiUnionMoneyModel::model()->unionMoneyLists(array('org_ids'=>implode(',',$org_ids),'items'=>count($org_ids)));
        if($r && $r['code']=='succ' && $r['body']['data']){
            $tmp = array();
            foreach($r['body']['data'] as $v){  $tmp[$v['org_id']] = $v;  }
            return $tmp;
        }
        return array();
    }

    private function rmRdsCache($orders) {
        foreach($orders as $value) {
            $this->OrderModel->delPhoneCardMap($value['owner_mobile'], $value['owner_card'], $value['code']);
            $this->SmsModel->delOrderSmsContentMap($value['id']);
        }
    }

}

$test = new Crontab_Bill();
