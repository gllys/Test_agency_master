<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-27
 * Time: 下午2:50
 */

class ApiOrganizationModel extends Base_Model_Api{
    protected $srvKey = 'ticket_organization';
    protected $url = '';
    protected $method = 'POST';

    /**
     * 取列表信息
     */
    public function orgList($params){
        $this->url = '/v1/organizations/orgList';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    /**
     * 取单一的详情信息
     */
    public function orgDetail($params){
        $this->url = '/v1/organizations/orgDetail';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

}