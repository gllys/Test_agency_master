<?php

/**
 * Class OrganizationModel
 */
class OrganizationModel extends Base_Model_Api
{
	
	protected $srvKey = 'ticket_organization';
    protected $url = '/v1/organizations/show';
    protected $method = 'POST';
    
    public function getTable() {
        return $this->tblname;
    }

    public function getListByIds(){
        //调用结构接口获取数据
        //...
        return array();
    }
    
	public function getInfo($org_id){
        if(!$org_id) {
            return false;
        }
        $this->params = array('id'=>$org_id);
        $orgInfo = $this->request();
        if(!empty($orgInfo)) {
            $orgInfo = json_decode($orgInfo, true);
            if(!empty($orgInfo['body'])) {
                return $orgInfo['body'];
            }
        }
        return false;
    }

    public function getOrgIds($params) {
        if(!$params)
            return false;
        $this->url = '/v1/organizations/list';
        $this->params = $params;
        $cacheKey = 'OrgIds_'.md5($this->url.json_encode($this->params));
        $mc = Cache_Memcache::factory();
        $orgIds = $mc->get($cacheKey);
        if(!$orgIds){
            $orglist = $this->request();
            $orglist = json_decode($orglist,true);
            if(!$orglist || empty($orglist['body']['data']))
                return false;
            $orgIds = array_keys($orglist['body']['data']);
            $mc->set($cacheKey,$orgIds,30);
        }
        return $orgIds;
    }
}
