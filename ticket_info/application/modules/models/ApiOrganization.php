<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-30
 * Time: 下午7:59
 */


class ApiOrganizationModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_organization';
    protected $url = '';
    protected $method = 'POST';

    public function listByName($name,$type='',$fields = ''){
        $this->url = '/v1/organizations/listByName/';
        $this->params = array('name'=>$name,'type'=>$type,'fields'=>$fields);
        $r = json_decode($this->request(),true);
        if(!$r || empty($r['body']))
            return false;
        return $r['body'];
    }

    public function orgInfo($org_id){
        $this->url = '/v1/organizations/show/';
        $this->params = array('id'=>$org_id);
        $r = json_decode($this->request(),true);
        return ($r && $r['body']) ? $r['body']:false;
    }

    //按分销商id获取供应商绑定记录
    public function supplierIdsOfAgency($agency_id){
        $this->url = '/v1/organizations/bydistributor/';
        $this->params = array('distributor_id'=>$agency_id);
        $r = json_decode($this->request(),true);
        if(!$r || !$r['body']) return false;
        $supplierIds = array();
        foreach($r['body'] as $v) {
            $supplierIds[] = $v['supplier_id'];
        }
        return $supplierIds;
    }

    //按供应商id获取分销商绑定记录
    public function agencyIdsOfSupplier($supplier_id,$fields=""){
        $cachekey = 'bysupplier_'.$supplier_id.'_'.$fields;
        $mc = Cache_Memcache::factory();
        $agencyIds = $mc->get($cachekey);
        if(!$agencyIds){
            $this->url = '/v1/organizations/bysupplier/';
            $this->params = array('supplier_ids'=>$supplier_id);
            if($fields)
                $this->params['fields'] = $fields;
            $r = json_decode($this->request(),true);
            if(!$r || !$r['body']) return false;
            $agencyIds = array();
            foreach($r['body'] as $v) {
                $agencyIds[] = $v['distributor_id'];
            }
            if($agencyIds)
                $mc->set($cachekey,$agencyIds,10); //缓存10秒
        }
        return $agencyIds;
    }

    public function getSupplyAgency($supplier_id,$distributor_id)
    {
        $this->method = 'POST';
        $this->url = '/v1/Credit/lists';
        $this->params =array('supplier_id'=>$supplier_id,'distributor_id'=>$distributor_id);
        $r = $this->request();
        $res = json_decode($r,true);
        return $res;
    }

}