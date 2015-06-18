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

    /**
     * 设置预警
     * author : yinjian
     */
    public function editWarningMoneyAction()
    {
        if(isset($this->body['sms_warning_money'])) {
            $data['config_value'] = doubleval($this->body['sms_warning_money']);
            $data['config_value']<0 && Lang_Msg::error('余额不能小于零');
            ConfigModel::model()->updateByAttr($data,array('config_key'=>'sms_warning_money'));
        }
        if(isset($this->body['sms_warning_send_email'])) {
            $data['config_value'] = trim($this->body['sms_warning_send_email']);
            ConfigModel::model()->updateByAttr($data,array('config_key'=>'sms_warning_send_email'));
        }
        Lang_Msg::output();
    }

    /**
     * 获取预警
     * author : yinjian
     */
    public function showWarningMoneyAction()
    {
        $sms_warning_money = ConfigModel::model()->get(array('config_key'=>'sms_warning_money'));
        $sms_warning_send_email = ConfigModel::model()->get(array('config_key'=>'sms_warning_send_email'));
        Lang_Msg::output(array(
            'sms_warning_money' => $sms_warning_money['config_value'],
            'sms_warning_send_email' => $sms_warning_send_email['config_value'],
        ));
    }
}