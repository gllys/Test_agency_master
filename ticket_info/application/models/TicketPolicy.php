<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-30
 * Time: 下午3:59
 */

class TicketPolicyModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_policy';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|TicketPolicyModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addNew($data){
        $nowTime = time();
        $data['created_at'] = $nowTime;
        $data['updated_at'] = $nowTime;
        $r = $this->add($data);
        $data['id'] =  $this->getInsertId();
        return $r ? $data : false ;
    }

    public function getDetail($id,$distributor_id=0){
        if(!$id) return false;
        $data = $this->search(array('id'=>$id));
        $detail = empty($data)? false:reset($data);
        if(!$detail) return false;
        $where = array('policy_id'=>$id);
        $distributor_id && $where['distributor_id']= $distributor_id;
        $detail['items'] = TicketPolicyItemModel::model()->search($where);
        $distributor_id && $detail['items'] = reset($detail['items']);
        return $detail;
    }

}
