<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-16
 * Time: 上午11:16
 */

class ApiUnionMoneyModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_organization';
    protected $url = '/v1/unionmoney/inout';
    protected $method = 'POST';

    //申请提现
    public function unionInout($params){
        $this->url = '/v1/unionmoney/inout';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function unionMoneyLists($params){
        $this->url = '/v1/unionmoney/lists';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }
}