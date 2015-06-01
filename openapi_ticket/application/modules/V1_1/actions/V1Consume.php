<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-11
 * Time: 上午10:38
 */

class ConsumeAction extends Yaf_Action_Abstract{
    /**
     * 用户消费(核销)通知（去哪儿）
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        $params = $ctrl->body;
        Util_Logger::getLogger('openapi')->info(__METHOD__, $params);
        $user_model = OtaAccountModel::model()->getUserInfoByAttributes(array('source' => $params['source']));
        if(!$user_model) {
            Util_Logger::getLogger('openapi')->error(__METHOD__, array(
                'action' => 'noticeRefund',
                'source' => $params['source'],
                'message' => '无法获取该OTA的账户信息',
            ));
            Lang_Msg::output(array(
                'code' => 400,
                'code_msg' => '通知失败[无法获知OTA账户信息]', 
            ));            
        }
        if(!$user_model['notify_url']) {
            Util_Logger::getLogger('openapi')->error(__METHOD__, array(
                'action' => 'noticeRefund',
                'source' => $params['source'],
                'message' => '无法获取该OTA的回调地址',
            ));
            Lang_Msg::output(array(
                'code' => 400,
                'code_msg' => '通知失败[无法获知OTA地址]', 
            ));            
        }

        $final_params = array(
            'action' => 'consume', //通知OTA这是核销操作
            'order_id' => $params['order_id'],
            'verify_code' => $params['verify_code'],
            'consume_num' => $params['consume_num'],
            'posid' => $params['posid'],
            'serial_num' => $params['serial_num'],
        );
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'noticeRefund',
            'final_params' => $final_params,
        ));
        $notify_url = $user_model['notify_url'];
        $notify_result = Tools::curl($notify_url,'POST',http_build_query($final_params));

        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'noticeRefund',
            'source' => $params['source'],
            'notify_result' => $notify_result,
        ));
        $code = 200;
        $message = '';
        if(isset($notify_result['code'])) {
           $code = $notify_result['code']; 
           $message = $notify_result['message'];
        }
        Lang_Msg::output(array(
            'code' => $code,
            'code_msg' => $message,
        ));
    }




}
