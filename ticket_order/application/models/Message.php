<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/20
 * Time: 10:48
 */
class MessageModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_organization';
    protected $url = '/v1/message/add';
    protected $method = 'POST';

    public function addMessage($data,$id,$order){
        $this->method = 'POST';
        $this->params = array(
            'content'=>'订单号：<a data-target=".bs-example-modal-static"  onclick="point(\''.$order['id'].'\',\''.$id.'\')"data-toggle="modal">'.$data['order_id'].'</a>申请退款，请确认！',
            'sms_type'=>0,
            'sys_type'=>5,
            'send_source'=>2,
            'send_status'=>1,
            'send_user'=>$data['user_id'],
            'send_organization'=>$order['distributor_id'],
            'receiver_organization'=>$order['supplier_id'],
            'organization_name'=>$order['distributor_name']
        );
        $r = $this->request();
        $res = json_decode($r,true);
        return isset($res['code']) && $res['code']=='succ';
    }

    /**
     * 发送站内信通用
     * author : yinjian
     */
    public function addBase($arr = array())
    {
        $this->method = 'POST';
        $this->params = $arr;
        $r = $this->request();
        $res = json_decode($r,true);
        return isset($res['code']) && $res['code']=='succ';
    }
}