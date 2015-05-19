<?php

/**
 * Class SmsModel
 */
class SmsModel extends Db_Base
{
    const PI_APP_DOMAIN = "bt-fx-agency.demo.org.cn";
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 短信信息发送
     * @author yinjian
     * @date   2014-08-25
     * @return [type]     [description]
     */
    public function sendSms($order)
    {  
        $str = '【景旅通票台】您已成功预订'.$order['name'].$order['nums'].'张，订单号：'.$order['id'].'，您可在使用有效期内游玩，通过以下二维码链接进行手机二维码：http://'.self::PI_APP_DOMAIN.'/qrcode_index_'.$order['id'].'.html 入园。';
        return Sms::sendSMS($order['mobile'],urlencode($str));
    }

    public function sendRefunds($order)
    {
        $str = '您申请的【拉手网】'.$order['name'].$order['nums'].'张退款，已经为您办理，退款金额会在7个工作日退还到您的支付账户。';
        return Sms::sendSMS($order['mobile'],urlencode($str));
    }
    
    public function getCodeUrl( $id )
    {
    	return 'http://'.self::PI_APP_DOMAIN.'/qrcode_index_'.$id.'.html ';
    }
}