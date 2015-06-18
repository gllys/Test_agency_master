<?php
/**
 * 核销通知
 * @author 崔林 <cuilin@ihuilian.com>
 * @date 2015.06.09
 * @package openapi 1.1
 */

class ConsumeAction extends Yaf_Action_Abstract{
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        $params = $ctrl->body;
        Util_Logger::getLogger('openapi')->info(__METHOD__, $params,'','核销通知-接收参数');
        $user_model = OtaAccountModel::model()->getUserInfoByAttributes(array('source' => $params['source']));
        if(!$user_model) {
            Util_Logger::getLogger('openapi')->error(__METHOD__, array(
                'action' => 'notice_consume',
                'source' => $params['source'],
                'message' => '无法获取该OTA的账户信息',
            ),'','核销通知-获取OTA账户信息',$params['source']);
            Lang_Msg::output(array(
                'code' => 400,
                'code_msg' => '通知失败[无法获知OTA账户信息]', 
            ),200,JSON_UNESCAPED_UNICODE);            
        }
        if(!$user_model['notify_url']) {
            Util_Logger::getLogger('openapi')->error(__METHOD__, array(
                'action' => 'notice_consume',
                'source' => $params['source'],
                'message' => '无法获取该OTA的回调地址',
            ),'','核销通知-获取OTA回调地址',$params['source']);
            Lang_Msg::output(array(
                'code' => 400,
                'code_msg' => '通知失败[无法获知OTA地址]', 
            ),200,JSON_UNESCAPED_UNICODE);            
        }

        $final_params = array(
            'action'       => 'consume', //通知OTA这是核销操作
            'order_id'     => $params['verify_code'],
            'ota_order_id' => $params['order_id'],
            'consume_num'  => $params['consume_num'],
            'posid'        => $params['posid'],
            'serial_num'   => $params['serial_num'],
        );
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'notice_consume',
            'final_params' => $final_params,
        ),'','核销通知-通知OTA核销信息',$params['verify_code']);
        $notify_url = $user_model['notify_url'];
        $notify_result = Tools::curl($notify_url,'POST',http_build_query($final_params));

        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'notice_consume',
            'source' => $params['source'],
            'notify_result' => $notify_result,
        ),'','核销通知-通知回调',$params['verify_code']);
        $code = 200;
        $message = '';
        if(isset($notify_result['code'])) {
           $code = $notify_result['code']; 
           $message = $notify_result['message'];
        }
        Lang_Msg::output(array(
            'code' => $code,
            'code_msg' => $message,
        ),200,JSON_UNESCAPED_UNICODE);
    }
}
