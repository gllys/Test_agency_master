<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 10/25/14
 * Time: 2:40 PM
 */
class SmsController extends Base_Controller_Api {
    /**
     * 转发短信
     * author : yinjian
     */
    public function sendAction()
    {
        !Validate::isMobilePhone($this->body['mobile']) && Lang_Msg::error('ERROR_SMSSEND_1');
        !Validate::isString($this->body['content']) && Lang_Msg::error('ERROR_SMSSEND_2');
        $res = Sms::sendSMS($this->body['mobile'],urlencode('【景旅通票台】'.$this->body['content']));
        !$res && Lang_Msg::error('ERROR_SMSSEND_3');
        Tools::lsJson(true,'ok');
    }
}