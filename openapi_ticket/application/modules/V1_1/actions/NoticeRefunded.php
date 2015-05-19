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
        Util_Logger::getLogger('openapi')->info(__METHOD__, $params);
        
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

        $params['action'] = 'refund'; //通知OTA这是退款操作
        $notify_url = $user_model['notify_url'];
        $notify_result = Tools::curl($notify_url,'POST',http_build_query($params));

        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'noticeRefund',
            'source' => $params['source'],
            'notify_result' => $notify_result,
        ));
        Lang_Msg::output(array(
            'code' => 200,
            'code_msg' => '退款通知成功',
        ));
    }
}
