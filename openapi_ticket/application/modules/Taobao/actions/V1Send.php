<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-26
 * Time: 下午2:21
 */
class SendAction extends Yaf_Action_Abstract{
    /**
     * 当出现余额不足等情况 导致交易中断时，提供给内部api调用的接口
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        $taobao = Taobao_TopClientFactory::create();

        $verifyCodes = $ctrl->body['id'] . ':' . $ctrl->body['nums'];

        $req = new Taobao_Request_VmarketEticketSendRequest;
        $req->setOrderId($ctrl->body['orderId']);
        $req->setVerifyCodes($verifyCodes);
        $req->setToken($ctrl->body['token']);
        $req->setCodemerchantId($taobao->merchantId);
        //        $req->setQrImages($qrImages); //码商需要开通二维码权限
        $resp = $taobao->topc->execute($req, $taobao->sessionKey);

        //日志输出
//        $ctrl->echoLog('body', json_encode($ctrl->body), 'send_taobao.log');
//        $ctrl->echoLog('resp', json_encode($resp), 'send_taobao.log');
        Util_Logger::getLogger('taobao')->info(__METHOD__,
            array('body' => $ctrl->body, 'resp' => $resp), '', '发码请求数据和淘宝回传数据',
            $ctrl->body['orderId']
        );

        if(isset($resp->ret_code) && $resp->ret_code == 1){
            //todo 调用淘宝接口成功后，向内部API返回200，让内部API正式核销
            Lang_Msg::output(array(
                'code' => 200,
                'code_msg' => '发码通知成功',
                'taobao_msg' => json_encode($resp),
            ));
        }else if(isset($resp->sub_code) && $resp->sub_code == 'isp.top-remote-connection-timeout'){
            //todo 如果调用淘宝接口没有响应，或者sub_code=isp.top-remote-connection-timeout，则调用淘宝冲正接口直至成功

        }

        Lang_Msg::error(json_encode($resp));
    }
}