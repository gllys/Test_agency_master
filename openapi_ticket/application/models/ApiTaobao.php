<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-27
 * Time: 下午6:28
 */
class ApiTaobaoModel extends Base_Model_Api{

    /**
     * 回调淘宝send
     * @param $params
     */
    public static function sendByAsync($params){
        $topc = self::_getTopClient();

        echo "you had run sendByAsync!!\n";
        var_dump($params);

        //todo 发码处理 获取响应验证码及二维码 将码发至用户手机
        $api = self::_getModelApi();
        $api->params = self::_getOrderInfo($params);;
        $api->srvKey = 'ticket_order';
        $api->url = '/v1/order/addPay';
        $r = json_decode($api->request(),true);

        echo "r::::!!\n";
        var_dump($r);

        if($r['code'] == 'succ'){
            $order = $r['body'];
            $verifyCodes = $order['id'] . ':' . $order['nums'];
            $qrImages = 'image1.png,image2.png';

            //todo 若法码成功，则调用淘宝send接口
            $req = new Taobao_Request_VmarketEticketSendRequest;
            $req->setOrderId($params['orderId']);
            $req->setVerifyCodes($verifyCodes);
            $req->setToken($params['token']);
            $req->setCodemerchantId($params['codemerchantId']);
            //        $req->setQrImages($qrImages); //码商需要开通二维码权限
            $resp = $topc->execute($req, $params['sessionKey']);

            echo "resp\n";
            var_dump($resp);
        }
    }

    /**
     * 回调淘宝resend
     * @param $params
     */
    public static function resendByAsync($params){
        $topc = self::_getTopClient();

        echo "you had run resendByAsync!!\n";
        var_dump($params);

        //todo 重新发码，准备好重发的码
        //todo 回调成功后，将码发至用户手机
        $api = self::_getModelApi();
        $api->params = array(
            'source_id' => $params['orderId'],
        );
        $api->url = '/v1/order/sms';
        $r = json_decode($api->request(),true);
        echo "r::::!!\n";
        var_dump($r);
        //示例
        $str = 'array(3) {
                  ["code"]=>
                  string(4) "succ"
                  ["message"]=>
                  string(0) ""
                  ["body"]=>
                  array(2) {
                    ["order_id"]=>
                    string(15) "166337811313613"
                    ["unuse_num"]=>
                    string(1) "8"
                    ["url"]=>
                    string(41) "http://www.piaotai.com/qr/166337811313613"
                  }
                }';
        $verifyCodes = $r['body']['order_id'] . ':' . $r['body']['unuse_num'];
        $qrImages = 'image1.png,image2.png';

        //todo 回调淘宝接口：重新发码回调接口
        if($r['code'] == 'succ'){
            $req = new Taobao_Request_VmarketEticketResendRequest();
            $req->setOrderId($params['orderId']);
            $req->setVerifyCodes($verifyCodes);
            $req->setToken($params['token']);
            $req->setCodemerchantId($params['codemerchantId']);
            //        $req->setQrImages($qrImages);
            $resp = $topc->execute($req, $params['sessionKey']);

            echo "resp\n";
            var_dump($resp);
        }
    }

    public static function modifiedByAsync($params){
        $topc = self::_getTopClient();

        //todo 更改用户接收码的手机号
        $api = self::_getModelApi();
        $api->params = array(
            'source_id'     => $params['orderId'],
            'owner_mobile'  => $params['mobile'],
        );
        $api->url = '/v1/order/update';
        $update_r = json_decode($api->request(),true);

        //todo 回调淘宝接口：重新发码回调接口
        $resp = NULL;
        if($update_r['code'] == 'succ'){
            $unuse_num = $update_r['body']['nums']-$update_r['body']['used_nums']-$update_r['body']['refunded_nums']-$update_r['body']['refunding_nums'];
            $verifyCodes = $update_r['body']['id'] . ':' .$unuse_num;

            $req = new Taobao_Request_VmarketEticketResendRequest();
            $req->setOrderId($params['orderId']);
            $req->setVerifyCodes($verifyCodes);
            $req->setToken($params['token']);
            $req->setCodemerchantId($params['codemerchantId']);
            //        $req->setQrImages($qrImages);
            $resp = $topc->execute($req, $params['sessionKey']);
        }

        //todo 回调成功后，将码发至用户新手机
        if(isset($resp->ret_code) && $resp->ret_code == 1){
            $api->params = array(
                'source_id' => $params['orderId'],
            );
            $api->url = '/v1/order/sms';
            $sms_r = json_decode($api->request(),true);
        }


        echo "update r::::!!\n";
        var_dump($update_r);

        echo "sms r::::!!\n";
        var_dump($sms_r);

        echo "you had run modifiedByAsync!!\n";
        var_dump($params);
        echo "resp\n";
        var_dump($resp);
    }

    /**
     * 退款
     * @param $params
     * @return bool
     */
    public static function cancel($params){
        $api = self::_getModelApi();

        //todo 先申请退款
        $api->params = array(
            'source_id' => $params['source_id'],
            'nums'      => $params['nums'],
            'user_id'   => $params['user_id'],
            'user_account'   => $params['user_account'],
            'user_name'   => $params['user_account'],
            'remark'    => '淘宝退款',
        );
        $api->url = '/v1/refund/applycheck';
        $r = json_decode($api->request(),true);

        return $r;
    }


    /**
     * 获取淘宝SDK对象
     * @return Taobao_TopClient
     */
    private static function  _getTopClient(){

        $taobao = Taobao_TopClientFactory::create();

        return $taobao->topc;
//        $topc             = new Taobao_TopClient();
//        $topc->appkey     = '23064138';
//        $topc->secretKey  = 'c4ac1087a720ffb4a23f0f0dde29c167';
//        $topc->gatewayUrl = 'http://gw.api.taobao.com/router/rest';
//
//        return $topc;
    }

    private static function _getModelApi($srvKey = 'ticket_order'){
        $api = Base_Model_Api::model();
        $api->srvKey = $srvKey;
        $api->method = 'POST';
        return $api;
    }

    /**
     * 构造订单参数
     * @param array $params 淘宝订单参数
     * @return array 我们的订单参数
     */
    private static function _getOrderInfo($params = array()){

        //取淘宝卖家对应的分销商信息
        $api = self::_getModelApi('ticket_organization');
        $api->params = array(
            'account_taobao' => $params['seller_nick'],
        );
        $api->url = '/v1/organizations/show';
        $distributor = json_decode($api->request(),true);

        echo "distributor:\n";
        var_dump($distributor);

        //取sub_outer_iid（在我们的库中是ota_code）对应的product_id
        $api = self::_getModelApi('ticket_info');
        $api->params = array(
            'code' => $params['sub_outer_iid'],
        );
//        $api->url = '/v1/TicketTemplate/ticketinfo';
        $api->url = '/v1/AgencyProduct/detail';
        $pro = json_decode($api->request(),true);

        $payment = array(
            1=> 'alipay',
            2=> 'credit',
            3=> 'advance',
            4=> 'union',
        );
        echo "pro:\n";
        var_dump($pro);

        $res['product_id']      = $pro['body']['product_id'];
        $res['source']          = 1;
        $res['local_source']    = 1;
        $res['source_id']       = $params['orderId'];
        $res['source_token']    = $params['token'];
        $res['price_type']      = 0;
        $res['distributor_id']  = $distributor['body']['id'];//分销商需要有购票权限才行,在 ticket_organization -> organization -> id
        $res['use_day']         = date('Y-m-d', strtotime($params['valid_start'])); //2015-01-11
        $res['expire_end']      = date('Y-m-d', strtotime($params['valid_ends'])); //2015-01-11
        $res['nums']            = $params['num']; //1
        $res['owner_name']      = 'taobao';//trim($this->body['traveler_name']);
        $res['owner_mobile']    = $params['mobile'];
        $res['owner_card']      = isset($params['id_card']) ? $params['id_card'] : '';
//        $res['remark']        = 'OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']';
        $res['user_id']         = $params['taobao_sid'];//$this->userinfo['id'];
        $res['user_account']    = $params['seller_nick'];//$this->userinfo['id'];
//        $res['user_name'] = $this->userinfo['name'];
        $res['payment']         =  $payment[$pro['body']['payment']];
//        $res['price'] = $this->body['price'];
        $res['ota_type']        = 'ota';
        $res['ota_account']     = 1;//$this->userinfo['id'];
        $res['ota_name']        = 'test';//$this->userinfo['name'];
        $res['ota_code']        = $params['sub_outer_iid'];

        echo "params:\n";
        var_dump($res);

        return $res;
    }
}