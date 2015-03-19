<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-26
 * Time: 下午2:21
 */
class ReverseAction extends Yaf_Action_Abstract{

    const REVERSE_TIME = 1;

    /**
     * 冲正申请回调接口，提供给内部系统调用
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        $taobao = Taobao_TopClientFactory::create();
        //var_dump($ctrl->topc);

        $req = new Taobao_Request_VmarketEticketReverseRequest;
        $req->setOrderId($ctrl->body['order_id']);
        $req->setReverseCode($ctrl->body['reverse_code']);
        $req->setReverseNum($ctrl->body['reverse_num']);
        $req->setConsumeSecialNum($ctrl->body['consume_serial_num']);
//        $req->setVerifyCodes($verifyCodes);
//        $req->setQrImages($qrImages);
        $req->setToken($ctrl->body['token']);
        $req->setCodemerchantId($taobao->merchantId);
        $req->setPosid($ctrl->body['posid']);

        //日志输出
        $ctrl->echoLog('body', json_encode($ctrl->body), 'reverse_bee.log');

        for($i=0; $i<self::REVERSE_TIME; $i++){
            $resp = $taobao->topc->execute($req, $taobao->sessionKey);

            //日志输出
            $ctrl->echoLog('resp'.$i, json_encode($resp), 'reverse_bee.log');

            //todo 如果成功，则执行自己的冲正，否则重试多次
            if(isset($resp->ret_code) && $resp->ret_code == 1){
                //todo 返回成功结果，让内部API进行冲正
                Lang_Msg::output(array(
                    'code' => 200,
                    'code_msg' => '冲正成功'
                ));
                break;
            }
        }

        Lang_Msg::error(json_encode($resp));
    }
}