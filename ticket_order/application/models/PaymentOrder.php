<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-25
 * Time: 下午9:04
 */


class PaymentOrderModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'payment_orders';
    protected $basename = 'payment_orders';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|PaymentOrderModel|';
    protected $autoShare = 1;

    public function getTable() {
        return $this->tblname;
    }

    public function setTable($id = 0) {
        if (!$id) $this->tblname = $this->basename . date('Ym');
        else  $this->tblname = $this->basename . Util_Common::payid2date($id);
        return $this;
    }

    public function share($ts = 0) {
        if (!$ts) $ts = time();
        $this->tblname = $this->basename . date('Ym', $ts);
        return $this;
    }

    public function addBatch($paymentInfo,$orders){
        $this->setTable($paymentInfo['id']);
        $data = array(array('id','payment_id','order_id','money','created_at','updated_at'));
        foreach($orders as $v){
            $data[] = array(Util_Common::payid(),  $paymentInfo['id'],  $v['id'], $v['amount'],  $paymentInfo['created_at'], $paymentInfo['updated_at']);
        }
        return $this->replace($data);
    }
}