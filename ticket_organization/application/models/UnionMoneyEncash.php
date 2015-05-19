<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-12
 * Time: 下午3:35
 */

class UnionMoneyEncashModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'union_money_encash';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|UnionMoneyEncashModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addNew($params){
        if(!$params) return false;
        $data = array(
            'created_at'=> time(),
            'org_id'=>$params['org_id'],
            'org_role'=>$params['org_role'],
            'money'=>$params['money'],
            'union_money'=>$params['union_money'],
            'apply_uid'=>$params['apply_uid'],
            'apply_account'=>$params['apply_account'],
            'apply_username'=>$params['apply_username'],
            'apply_phone'=>$params['apply_phone'],
            'bank_id'=>$params['bank_id'],
            'bank_name'=>$params['bank_name'],
            'open_bank'=>$params['open_bank'],
            'account'=>$params['account'],
            'account_name'=>$params['account_name'],
            'check_uid'=>0,
            'status'=>0,
            'remark'=>$params['remark'],
        );
        $r = $this->add($data);
        $r && $data['id'] = $this->getInsertId();
        return $r?$data:false;
    }
}