<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-26
 * Time: 下午2:21
 */
class BeforeconsumeAction extends Yaf_Action_Abstract{
    /**
     * 核销码验证查询接口，提供给内部系统调用
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();
        //var_dump($ctrl->topc);

        //todo 在核销前可调用此接口，查询验证码状态，非必须

        $verifyCodes = '';
        $posid = '';

        $req = new Taobao_Request_VmarketEticketBeforeconsumeRequest();
        $req->setOrderId($ctrl->body['order_id']);
        $req->setVerifyCode($verifyCodes);
        $req->setToken($ctrl->body['token']);
        $req->setCodemerchantId($ctrl->merchantId);
        $req->setPosid($posid);
        $resp = $ctrl->topc->execute($req, $ctrl->sessionKey);

        var_dump($resp);
    }
}