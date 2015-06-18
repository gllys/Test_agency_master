<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-26
 * Time: 下午2:21
 */
class RefundedAction extends Yaf_Action_Abstract{
    /**
     * 当出现余额不足等情况 导致交易中断时，提供给内部api调用的接口
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();

        //异步调用ota退款接口
        $params = $ctrl->body;
        Util_Logger::getLogger('openapi')->info(__METHOD__, $params,'','退款通知-接收参数');
        
        //id: order_id
        //user_id
        //status: success | failed
        //remark:

        $user_model = OtaAccountModel::model()->getUserInfoByAttributes(array('source' => $params['source']));
        if(!$user_model) {
            Util_Logger::getLogger('openapi')->error(__METHOD__, array(
                'action' => 'noticeRefund',
                'source' => $params['source'],
                'message' => '无法获取该OTA的账户信息',
            ),'','退款通知-获取OTA账户信息',$params['source']);
            Lang_Msg::output(array(
                'code' => 400,
                'code_msg' => '通知失败[无法获知OTA账户信息]', 
            ),200,JSON_UNESCAPED_UNICODE);            
        }
        if(!$user_model['notify_url']) {
            Util_Logger::getLogger('openapi')->error(__METHOD__, array(
                'action' => 'noticeRefund',
                'source' => $params['source'],
                'message' => '无法获取该OTA的回调地址',
            ),'','退款通知-获取OTA回调地址',$params['source']);
            Lang_Msg::output(array(
                'code' => 400,
                'code_msg' => '通知失败[无法获知OTA地址]', 
            ),200,JSON_UNESCAPED_UNICODE);            
        }

        $final_params = array(
            'action' => 'refund', //通知OTA时，通过action来说明这是退款通知 
            'refund_id' => $params['refund_id'],
            'order_id' => $params['order_id'],
            'status' => $params['status'] == 'success' ? 'SUCCESS' : 'FAIL',
            'remark' => $params['remark'],
            'count' => isset($params['nums']) ? $params['nums'] : '',
        );
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'noticeRefund',
            'final_params' => $final_params,
        ),'','退款通知-通知OTA退款信息',$params['refund_id']);
        $notify_url = $user_model['notify_url'];
        $notify_result = Tools::curl($notify_url,'POST',http_build_query($final_params));

        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'noticeRefund',
            'source' => $params['source'],
            'notify_result' => $notify_result,
        ),'','退款通知-通知回调',$params['refund_id']);
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
