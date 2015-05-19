<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-15
 * Time: 下午6:09
 */


class UnionMoneyRechargeModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'union_money_recharge';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|UnionMoneyRechargeModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addNew($params){
        if(!$params) return false;
        $data = array(
            'org_id'=>$params['org_id'],
            'money'=>$params['money'],
            'pay_type'=>$params['pay_type'],
            'is_credit'=>$params['is_credit']?1:0,
            'op_uid'=>$params['op_uid'],
            'op_account'=>$params['op_account'],
            'op_username'=>$params['op_username'],
            'created_at'=> time(),
            'activity_money' => $params['activity_money'],
            'activity_charge_log_id' => $params['activity_charge_log_id'],
        );
        $r = $this->add($data);
        $r && $data['id'] = $this->getInsertId();
        return $r?$data:false;
    }
}