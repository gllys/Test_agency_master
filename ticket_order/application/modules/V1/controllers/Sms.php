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
        $type = intval($this->body['type']); //类型：0默认，1订单支付成功，2注册验证码，3重置密码，4提现验证码
        $order_id = trim(Tools::safeOutput($this->body['order_id']));
        $res = Sms::sendSMS($this->body['mobile'],urlencode('【景旅通票台】'.$this->body['content']),$type,$order_id);
        !$res && Lang_Msg::error('ERROR_SMSSEND_3');
        Tools::lsJson(true,'ok');
    }
}