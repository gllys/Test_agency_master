<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-1-9
 * Time: 下午2:17
 */

class ApiOrderModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_order';
    protected $url = '/v1/order/detail';
    protected $method = 'POST';

    public function create($params){
        $this->url = '/v1/order/addPay';
        $this->params = $params;
        $data = $this->request();
        $r = json_decode($data,true);
        return $r;
    }

    public function update($params){
        $this->url = '/v1/order/update';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function check($params){
        $this->url = '/v1/order/checkTicket';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function cancel($params){
        $this->url = '/v1/refund/apply';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function detail($params){
        $this->url = '/v1/order/detail';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }
    public function eticketSent($params){
        $this->url = '/v1/smslog/lists';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }
    public function cancelAndRefund($params) {
        $this->url = '/v1/refund/applycheck';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function infos($params) {
        $this->url = '/v1/order/infos';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function scenicUsed($params){
        $this->url = '/v1/order/scenicUsed';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function ticketUsed($params){
        $this->url = '/v1/order/ticketUsed';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

    public function sendTicket($params){
        $this->url = '/v1/order/sms';
        $this->params = $params;
        $r = json_decode($this->request(),true);
        return $r;
    }

}
