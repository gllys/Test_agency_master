<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-28
 * Time: 上午10:35
 */


class OrderGroupModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'order_group';
    protected $basename = 'order_group';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|OrderGroupModel|';
    protected $autoShare = 1;

    public function getTable() {
        return $this->tblname;
    }

    public function setTable($id = 0) {
        if (!$id) $this->tblname = $this->basename . date('Ym');
        else  $this->tblname = $this->basename . Util_Common::uniqid2date($id);
        return $this;
    }

    public function share($ts = 0) {
        if (!$ts) $ts = time();
        $this->tblname = $this->basename . date('Ym', $ts);
        return $this;
    }

    //创建组合票订单
    public function addNew($params){
        $nowTime = time();
        $data = array();
        $data['id'] = Util_Common::uniqid(4); //参数4，组合票订单
        $data['distributor_id'] = $params['distributor_id'];
        $data['ticket_code_id'] = $params['ticket_code_id'];
        $data['ticket_template_ids'] = $params['ticket_template_ids'];
        $data['order_ids'] = $params['order_ids'];
        $data['ota_account'] = $params['ota_account'];
        $data['ota_name'] = $params['ota_name'];
        $data['op_id'] = $params['user_id'];
        $data['created_at'] = $nowTime;
        $data['updated_at'] = $nowTime;

        return $this->add($data) ? $data :false;
    }



}