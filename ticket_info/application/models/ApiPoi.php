<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-3-5
 * Time: 下午6:00
 */


class ApiPoiModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_scenic';
    protected $url = '';
    protected $method = 'POST';

    //获取景点ID列表
    public function GetPoiIds($params){
        if(!$params) return array();
        $this->url = '/v1/poi/lists';
        $this->method = 'POST';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return ($r && $r['body']) ? array_keys($r['body']['data']):array();
    }

}