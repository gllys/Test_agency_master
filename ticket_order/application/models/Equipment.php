<?php

/**
 * Class OrganizationModel
 */
class EquipmentModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_scenic';
    protected $url = '/v1/device/lists';
    protected $method = 'POST';

    public function getInfo($org_id){
        if(!$org_id)
            return false;
        $this->params = array('id'=>$org_id);
        $orgInfo = $this->request();
        $orgInfo = json_decode($orgInfo, true);
        if(!$orgInfo || empty($orgInfo['body']))
            return false;
        return $orgInfo['body'];
    }

    public function getDevice($code){
        if(!$code) return false;
        $this->url = '/v1/device/detail';
        $this->params = array('code'=>$code);
        $info = $this->request();
        $info = json_decode($info, true);
        if(!$info || empty($info['body'])) return false;
        return $info['body'];
    }
}
