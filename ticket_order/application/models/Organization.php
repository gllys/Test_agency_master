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
        if(!$org_id) {
            return false;
        }
        $this->method = 'POST';
        $this-> url= '/v1/organizations/show';
        $this->params = array('id'=>$org_id);
        $cacheKey = 'organizationInfo_'.$org_id;
        $mc = Cache_Memcache::factory();
        $orgInfo = $mc->get($cacheKey);
        if(empty($orgInfo)) {
            $orgInfo = json_decode($this->request(),true);
            if(!empty($orgInfo) || !empty($orgInfo['body'])) {
                $mc->set($cacheKey,$orgInfo,10);
            }
        }
        if(empty($orgInfo) || empty($orgInfo['body'])) {
            return false;
        }
        return $orgInfo['body'];
    }

    public function getOrgInfoByAttr($attr)
    {
        $this->url = '/v1/organizations/list';
        $this->params = $attr;
        $orgKey = 'orgList_'.md5(json_encode($this->params));
        $mc = Cache_Memcache::factory();
        $orgList = $mc->get($orgKey);
        if(!$orgList) {
            $orgInfo = json_decode($this->request(), true);
            if(!$orgInfo || empty($orgInfo['body']))
                return false;
            $orgList = $orgInfo['body']['data'];
            $mc->set($orgKey,$orgList,300); //缓存5分钟
        }
        return $orgList;
    }

    public function getList($org_ids,$fields=""){
        if(!$org_ids)
            return false;
        if(is_array($org_ids))
            $org_ids = implode(',',$org_ids);

        $orgKey = 'orgList_'.$org_ids.'_'.$fields;
        $mc = Cache_Memcache::factory();
        $orgList = $mc->get($orgKey);
        if(!$orgList){
            $this->method = 'POST';
            $this->url = '/v1/organizations/list';
            $this->params = array('id'=>$org_ids);
            if($fields)
                $this->params['fields']=$fields;
            $orgInfo = json_decode($this->request(),true);
            if(!$orgInfo || empty($orgInfo['body']))
                return false;
            $orgList = $orgInfo['body'];
            $mc->set($orgKey,$orgList,300); //缓存5分钟
        }
        return $orgList;
    }

    public function addRefund($info,$action_type=2){
        $this->method = 'POST';
        $this->url = '/v1/Credit/update';
        $this->params = array(
            'action_type'=>$action_type,
            'distributor_id'=>intval($info['distributor_id']),
            'supplier_id' => intval($info['supplier_id']),
            'num'=>$info['money'],
            'type'=>$info['type'],
            'remark'=>$info['remark'],
            'user_id'=>intval($info['op_id'])
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

    public  function bySupplier($supplier_ids,$fields=''){
        $this->method = 'POST';
        $this->url = '/v1/organizations/bysupplier';
        $this->params = array('supplier_ids'=>$supplier_ids);
        if($fields!='' && $fields!='*') {
            $this->params['fields'] = $fields;
        }
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

    public function getIdsByName($name,$type='supply'){
        if(!$name)
            return false;
        $this->url = '/v1/organizations/listByName';
        $this->params = array('type'=>$type,'name'=>$name,'fields'=>'id');
        $r = $this->request();
        $r = json_decode($r, true);
        if(!$r || empty($r['body']))
            return false;
        return array_keys($r['body']);
    }
}
