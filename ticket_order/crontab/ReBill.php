<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-2-11
 * Time: 上午11:11
 * 对2015-01-29～2015-02-12 未打款的结算单清理，并更改对应明细的bill_id为0，然后执行生成结算单
 */


require dirname(__FILE__) . '/Base.php';

class Crontab_ReBill extends Process_Base
{
    private $startDay = "2015-01-29";
    private $endDay = "2015-02-12 23:59:59";
    private $pay_status = 0;//未打款
    private $receipt_status = 0;//未收款

    public function run()
    {
        $this->startDay = strtotime($this->startDay);
        $this->endDay = strtotime($this->endDay);
        $this->BillModel = BillModel::model();
        $this->BillitemModel = BillitemModel::model();
        $this->now = time();
        $this->OrderModel = OrderModel::model();
        $this->OrderItemModel = OrderItemModel::model();
        $this->online_pay_types = array('alipay','kuaiqian','union');

        $where = array(
            'created_at|>='=>$this->startDay,
            'created_at|<='=>$this->endDay,
            'pay_status'=>$this->pay_status, //未打款
            'receipt_status'=>$this->receipt_status, //未收款
        );
        $bills = $this->BillModel->search($where,"id");
        $this->BillModel->begin();
        if($bills){
            $billIds = array_keys($bills);
            $r = $this->BillitemModel->update(array('bill_id'=>0),array('bill_id|in'=>$billIds));
            if(!$r){
                $this->BillModel->rollback();
                exit("\nHandle BillItem Faild!\n");
            }
            $r = $this->BillModel->delete(array('id|in'=>$billIds));
            if(!$r){
                $this->BillModel->rollback();
                exit("\nHandle Bill Faild!\n");
            }

            echo "\nHandled Bills: ".implode(', ',$billIds)."\n";
        }

        $billIds = array();
        $billIds1  = $this->createBill('online');
        $billIds2 = $this->createBill('credit');
        if($billIds1) $billIds = array_merge($billIds,$billIds1);
        if($billIds2) $billIds = array_merge($billIds,$billIds2);
        echo "\nGen Bills: ".implode(', ',$billIds)."\n";

        $this->BillModel->commit();
        if(!$bills && !$billIds) exit("\nNo Data Handled!\n");
        exit;
    }

    //生成结算单
    public function createBill($pay_type='online') {
        $tblname = $this->BillitemModel->getTable();
        $where = " bill_id=0 ";
        if($pay_type=='online'){
            $where .= " AND payment IN ('".implode("','",$this->online_pay_types)."') ";
            $groupBy = "supply_id,created_at";
        }
        else if($pay_type=='credit'){
            $where .= " AND payment='credit' ";
            $groupBy = "supply_id,agency_id,created_at";
        }

        $sql = "select supply_id,supply_name,agency_id,agency_name,payment,sum(bill_amount) as amount,count(id) as num,created_at from $tblname where {$where} group by {$groupBy}";
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
            $item['created_at'] = $row['created_at'];
            $item['updated_at'] = $row['created_at'];
            $r = $this->BillModel->add($item);
            if($r) {
                $orderIds = array();
                $sql = "select order_id,created_at from $tblname where {$where} AND supply_id=".$row['supply_id'].($pay_type=='online'?"":" AND agency_id=".$row['agency_id'])." AND created_at=".$row['created_at'];
                $orders = $this->BillitemModel->db->selectBySql($sql);
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
        $whereBill = array(
            'id|in' => $orderIds,
            'or' => array(
                'nums|EXP' => '=used_nums+refunded_nums',
                'refund' => 0,
                'and' => array(
                    'activity_paid|>' => 0,
                    'used_nums|>' => 0,
                    'updated_at|<' => $this->cancelTime, //过了撤销核销时间
                ),
            )
        );
        $finishOrders = $this->OrderModel->search($whereBill);
        $finishOrderIds = $whereBill['id|in'] = array_keys($finishOrders);
        if($finishOrderIds){
            $this->OrderModel->updateByAttr(array('status' => 'billed'), $whereBill); /*更改订单的状态*/
            $this->OrderItemModel->updateByAttr(array('bill_time' => $this->now), array('order_id|in' => $finishOrderIds));/*更改订单明细的状态*/
            $this->OrderItemModel->updateByAttr(array('bill_time' => $this->now), array('order_id|in' => $finishOrderIds));
            // 清除REDIS相关数据
            $this->rmRdsCache($finishOrders);
        }
        /*更改部分使用的订单的订单明细状态*/
        $itemOrderIds = array_diff($orderIds, $finishOrderIds);
        if($itemOrderIds){
            $this->OrderItemModel->updateByAttr(
                array('bill_time' => $this->now),
                array('order_id|in' => $itemOrderIds, 'use_time|>' => 0, 'use_time|<' => $this->cancelTime, 'bill_time' => 0)
            );
        }
        return true;
    }

    private function rmRdsCache($orders) {
        foreach($orders as $value) {
            $this->OrderModel->delPhoneCardMap($value['owner_mobile'], $value['owner_card'], $value['order_id']);
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
            'op_id'=>1,
            'created_at'=>time(),
            'bill_id'=>$billId,
            'user_name'=>'system',
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

$test = new Crontab_ReBill;