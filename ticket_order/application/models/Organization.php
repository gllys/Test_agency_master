<?php

/**
 * Class OrganizationModel
 */
class OrganizationModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_organization';
    protected $url = '/v1/organizations/show';
    protected $method = 'POST';

    public function getInfo($org_id){
        if(!$org_id)
            return false;
        $this->method = 'GET';
        $this->params = array('id'=>$org_id);
        $orgInfo = json_decode($this->request(),true);
        if(!$orgInfo || empty($orgInfo['body']))
            return false;
        return $orgInfo['body'];
    }

    public function getOrgInfoByAttr($attr)
    {
        $this->url = '/v1/organizations/list';
        $this->params = $attr;
        $orgInfo = json_decode($this->request(),true);
        if(!$orgInfo || empty($orgInfo['body']))
            return false;
        return $orgInfo['body']['data'];
    }

    public function getList($org_ids){
        if(!$org_ids)
            return false;
        if(is_array($org_ids))
            $org_ids = implode(',',$org_ids);
        $this->method = 'GET';
        $this->url = '/v1/organizations/list';
        $this->params = array('id'=>$org_ids);
        $orgInfo = json_decode($this->request(),true);
        if(!$orgInfo || empty($orgInfo['body']))
            return false;
        return $orgInfo['body'];
    }

    public function addRefund($info,$action_type=2){
        $this->method = 'POST';
        $this->url = '/v1/Credit/update';
        $this->params = array(
            'action_type'=>$action_type,
            'distributor_id'=>$info['distributor_id'],
            'supplier_id' => $info['supplier_id'],
            'num'=>$info['money'],
            'type'=>$info['type'],
            'remark'=>$info['remark'],
            'user_id'=>$info['op_id']
        );
        $r = $this->request();
        $res = json_decode($r,true);
        return isset($res['code']) && $res['code']=='succ';
    }

    public function pay_credit($info)
    {
        $this->method = 'POST';
        $this->url = '/v1/credit/pay';
        $this->params = array(
            'distributor_id'=>$info['distributor_id'],
            'supplier_id' => $info['supplier_id'],
            'money'=>$info['money'],
            'type'=>$info['type'],
            'serial_id'=>$info['serial_id'],
        );
        $r = $this->request();
        $res = json_decode($r,true);
        return isset($res['code']) && $res['code']=='succ';
    }

    public  function bySupplier($supplier_ids){
        $this->method = 'POST';
        $this->url = '/v1/organizations/bysupplier';
        $this->params = array('supplier_ids'=>$supplier_ids);
        $res = json_decode($this->request(),true);
        return (isset($res['code']) && $res['code']=='succ') ? $res['body'] : false;
    }

    public function creditPay($info){
        $this->method = 'POST';
        $this->url = '/v1/Credit/pay';
        $this->params = $info;
        $r = $this->request();
        $res = json_decode($r,true);
        return $res;
    }

    public function getSupplyAgency($info)
    {
        $this->method = 'POST';
        $this->url = '/v1/Credit/lists';
        $this->params = $info;
        $r = $this->request();
        $res = json_decode($r,true);
        return $res;
    }
}
