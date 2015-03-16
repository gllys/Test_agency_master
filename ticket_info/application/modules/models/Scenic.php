<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-20
 * Time: 下午2:56
 */

class ScenicModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_scenic';
    protected $url = '/v1/landscape/lists';
    protected $method = 'POST';

    public function getScenicList($params) {
        $this->params = $params;
        return json_decode($this->request(),true);
    }

    public function getPoiList($params) {
        $this->params = $params;
        $this->url = '/v1/poi/lists';
        return json_decode($this->request(),true);
    }

    public function getScenicInfo($params){
        $this->params = $params;
        $this->url = '/v1/landscape/detail';
        return json_decode($this->request(),true);
    }
}
