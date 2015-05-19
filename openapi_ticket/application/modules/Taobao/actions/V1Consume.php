<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-26
 * Time: 下午2:21
 */
class ConsumeAction extends Yaf_Action_Abstract{

    const CONSUME_TIME = 2;

    /**
     * 核销申请回调接口，提供给内部系统调用
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        $taobao = Taobao_TopClientFactory::create();

        //todo 调用此接口通知淘宝核销，
        $req = new Taobao_Request_VmarketEticketConsumeRequest();
        $req->setOrderId($ctrl->body['order_id']);
        $req->setVerifyCode($ctrl->body['verify_code']);
        $req->setConsumeNum($ctrl->body['consume_num']);
        $req->setToken($ctrl->body['token']);
        $req->setCodemerchantId($taobao->merchantId);
        $req->setPosid($ctrl->body['posid']); //landscape_id
        $req->setSerialNum($ctrl->body['serial_num']);
//        $req->setQrImages($qrImages);

        for($i = 0; $i < self::CONSUME_TIME; $i++){
            $resp = $taobao->topc->execute($req, $taobao->sessionKey);

            //日志输出
    //        $ctrl->echoLog('body', json_encode($ctrl->body), 'consume_bee.log');
    //        $ctrl->echoLog('resp', json_encode($resp), 'consume_bee.log');
            Util_Logger::getLogger('taobao')->info(__METHOD__,
                array('body' => $ctrl->body, 'resp' => $resp), '', '核销请求数据和淘宝回传数据',
                $ctrl->body['order_id']
            );

            if(isset($resp->ret_code) && $resp->ret_code == 1){
                //todo 调用淘宝接口成功后，向内部API返回200，让内部API正式核销
                Lang_Msg::output(array(
                    'code' => 200,
                    'code_msg' => '核销成功',
                    'taobao_msg' => json_encode($resp),
                ));

            }else if(isset($resp->sub_code)
                && ($resp->sub_code == 'isv.eticket-invalid-parameter:invalid-posid'
                    || $resp->sub_code == 'isv.eticket-invalid-posid:invalid-pos-for-codemerchant')){
                //todo 如果核销机具id错误，则使用统一机具id
                $req->setPosid('common');
            }
        }
        Util_Logger::getLogger('taobao')->error(__METHOD__,
            array('body' => $ctrl->body, 'resp' => $resp), '', '淘宝核销错误',
            $ctrl->body['order_id'] . ',' . $ctrl->body['verify_code']
        );
        Lang_Msg::error(json_encode($resp));
    }
}