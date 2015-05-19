<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2014/11/21
 * Time: 15:05
 */

class TransflowModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_order';
    protected $url = '/v1/transflow/add';
    protected $method = 'POST';

    public function getList() {
        $this->params = array();
        return $this->request();
    }

    public function addflow($amount,$supplier_id,$agency_id,$op_id,$mode='advance',$type=3,$user_name='',$balance=0,$remark='')
    {
        $this->url = '/v1/transflow/add';
        $this->params = array(
            'mode'=>$mode,
            'type'=>$type,
            'amount'=>$amount,
            'supplier_id'=>$supplier_id,
            'agency_id'=>$agency_id,
            'op_id'=>$op_id,
            'user_name'=>$user_name,
            'balance' => $balance,
            'remark'=>$remark,
        );
        $res = json_decode($this->request(),true);
        return isset($res['code']) && $res['code']=='succ';
    }
}