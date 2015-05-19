<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-11
 * Time: 下午4:57
 */

class UnionMoneyLogModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'union_money_log';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|UnionMoneyLogModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addNew($params){
        if(!$params) return false;
        $data = array(
            'created_at'=> time(),
            'org_id'=>$params['org_id'],
            'org_role'=>$params['org_role'],
            'op_uid'=>$params['op_uid'],
            'op_account'=>$params['op_account'],
            'op_username'=>$params['op_username'],
            'money'=>$params['money'],
            'in_out'=>$params['in_out'],
            'trade_type'=>$params['trade_type'],
            'pay_type'=>$params['pay_type'],
            'used_credit'=>$params['used_credit'],
            'union_money'=>$params['union_money'],
            'frozen_money'=>$params['frozen_money'],
            'remark'=>$params['remark'],
        );
        $r = $this->add($data);
        $r && $data['id'] = $this->getInsertId();
        return $r?$data:false;
    }
}