<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-1-9
 * Time: 下午2:20
 */

class ApiScenicModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_scenic';
    protected $url = '/v1/landscape/lists';
    protected $method = 'POST';

    //申请提现
    public function lists($params){
        $this->url = '/v1/landscape/lists';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function detail($params){
        $this->url = '/v1/landscape/detail';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }
}