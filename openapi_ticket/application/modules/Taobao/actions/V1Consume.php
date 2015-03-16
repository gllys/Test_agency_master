<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-26
 * Time: 下午2:21
 */
class ConsumeAction extends Yaf_Action_Abstract{

    /**
     * 核销申请回调接口，提供给内部系统调用
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();

        //todo 调用此接口通知淘宝核销，
        $req = new Taobao_Request_VmarketEticketConsumeRequest();
        $req->setOrderId($ctrl->body['order_id']);
        $req->setVerifyCode($ctrl->body['verify_code']);
        $req->setConsumeNum($ctrl->body['consume_num']);
        $req->setToken($ctrl->body['token']);
        $req->setCodemerchantId($ctrl->merchantId);
        $req->setPosid($ctrl->body['posid']);
        $req->setSerialNum($ctrl->body['serial_num']);
//        $req->setQrImages($qrImages);
        $resp = $ctrl->topc->execute($req, $ctrl->sessionKey);

        //日志输出
        $ctrl->echoLog('body', json_encode($ctrl->body), 'consume_bee.log');
        $ctrl->echoLog('resp', json_encode($resp), 'consume_bee.log');

        //正确示例：
//        $r = '{
//                "code_left_num": 2,
//                "consume_secial_num": "111",
//                "item_title": "欢乐谷 -- 935",
//                "left_num": 1,
//                "print_tpl": "商品名称：欢乐谷 -- 935  数量：2  合计：0.02元  本次提取的数量：1.",
//                "ret_code": 1,
//                "sms_tpl": "您在ihuilian111购买的欢乐谷 -- 935,验证码$code于2015-02-02 14:08已验证成功.如有疑问,请联系卖家."
//            }';

        if(isset($resp->ret_code) && $resp->ret_code == 1){
            //todo 调用淘宝接口成功后，向内部API返回200，让内部API正式核销
            Lang_Msg::output(array(
                'code' => 200,
                'code_msg' => '核销成功',
                'taobao_msg' => json_encode($resp),
            ));
        }else if(isset($resp->sub_code) && $resp->sub_code == 'isp.top-remote-connection-timeout'){
            //todo 如果调用淘宝接口没有响应，或者sub_code=isp.top-remote-connection-timeout，则调用淘宝冲正接口直至成功

        }

        Lang_Msg::error(json_encode($resp));
    }
}