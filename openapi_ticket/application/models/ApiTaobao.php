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

//        echo "you had run sendByAsync!!\n";
//        var_dump($params);

        //todo 发码处理 获取响应验证码及二维码 将码发至用户手机
        $api = self::_getModelApi();
        $api->params = self::_getOrderInfo($params);;
        $api->srvKey = 'ticket_order';
        $api->url = '/v1/order/addPay';
        $r = json_decode($api->request(),true);

//        echo "r::::!!\n";
//        var_dump($r);
        Util_Logger::getLogger('taobao')->info(__METHOD__,
            array('params' => json_encode($params), 'resp' => $r), '', 'order/addPay的请求数据和回传数据',
            $params['orderId']
        );

        if($r['code'] == 'succ'){
            $order = $r['body'];
            $verifyCodes = $order['id'] . ':' . $order['nums'];
//            $verifyCodes = time().'100:'.$params['num'];

            //todo 若法码成功，则调用淘宝send接口
            $req = new Taobao_Request_VmarketEticketSendRequest;
            $req->setOrderId($params['orderId']);
            $req->setVerifyCodes($verifyCodes);
            $req->setToken($params['token']);
            $req->setCodemerchantId($params['codemerchantId']);
            //        $req->setQrImages($qrImages); //码商需要开通二维码权限
            $resp = $topc->execute($req, $params['sessionKey']);

//            echo "resp\n";
//            var_dump($resp);
            Util_Logger::getLogger('taobao')->info(__METHOD__, $resp, '', '淘宝发码通知回传数据', $params['orderId'] . ',' . $verifyCodes);
        }
    }

    /**
     * 回调淘宝resend
     * @param $params
     */
    public static function resendByAsync($params){
        $topc = self::_getTopClient();

//        echo "you had run resendByAsync!!\n";
//        var_dump($params);

        //todo 重新发码，准备好重发的码
        //todo 回调成功后，将码发至用户手机
        if($params['type'] == 0){
            $api = self::_getModelApi();
            $api->params = array(
                'source_id' => $params['orderId'],
            );
            $api->url = '/v1/order/sms';
            $r = json_decode($api->request(),true);
        }else {
            $api = self::_getModelApi('ticket_organization');
            $api->params = array(
                'source'    =>1,
                'account'   => $params['seller_nick'],
            );
            $api->url = '/v1/organizations/orgDetail';
            $distributor = json_decode($api->request(),true);

            $api = self::_getModelApi();
            $api->params = array(
                'source_id' => $params['orderId'],
                'source'    => 1,
                'distributor_id'    => $distributor['body']['organization_id'],
                'show_order_items'  => 1,
                'show_tickets'      => 0,
                'show_ticket_items' => 0,
            );
            $api->url = '/v1/order/detail';
            $r = json_decode($api->request(),true);
            $r['body']['order_id'] =  $r['body']['id'];
            $r['body']['unuse_num'] = $r['body']['order_items']['unuse_num'];
        }
//        echo "r::::!!\n";
//        var_dump($r);
        Util_Logger::getLogger('taobao')->info(__METHOD__,
            array('params' => $params, 'resp' => $r), '', '重发码数据',
            $params['orderId']
        );

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

//            echo "resp\n";
//            var_dump($resp);
            Util_Logger::getLogger('taobao')->info(__METHOD__, $resp, '', '重发码淘宝回传数据', $params['orderId'] . ',' . $r['body']['order_id']);
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
            if($params['type'] == 0){
                $api->params = array(
                    'source_id' => $params['orderId'],
                );
                $api->url = '/v1/order/sms';
                $sms_r = json_decode($api->request(),true);
            }
        }

        Util_Logger::getLogger('taobao')->info(__METHOD__,
            array('update_r' => $update_r, 'sms_r' => $sms_r, 'params' => $params, 'resp' => $resp), '', '修改手机号各种数据',
            $params['orderId']
        );

//        echo "update r::::!!\n";
//        var_dump($update_r);
//
//        echo "sms r::::!!\n";
//        var_dump($sms_r);
//
//        echo "you had run modifiedByAsync!!\n";
//        var_dump($params);
//        echo "resp\n";
//        var_dump($resp);
    }

    /**
     * 退款
     * @param $params
     * @return bool
     */
    public static function cancel($params){
        $api = self::_getModelApi();

        //由于帐号可能是中文字符，会有编码问题
        $params['seller_nick'] = iconv('GB2312', 'UTF-8', $params['seller_nick']);

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
        
        //由于帐号可能是中文字符，会有编码问题
        $params['seller_nick'] = iconv('GB2312', 'UTF-8', $params['seller_nick']);

        //取淘宝卖家对应的分销商信息
        $api = self::_getModelApi('ticket_organization');
        $api->params = array(
            'source'=>1,
            'account' => $params['seller_nick'],
        );
        $api->url = '/v1/organizations/orgDetail';
        $distributor = json_decode($api->request(),true);

//        echo "distributor:\n";
//        var_dump($distributor);

        //取sub_outer_iid（在我们的库中是ota_code）对应的product_id
        $api = self::_getModelApi('ticket_info');
        $api->params = array(
            'code' => $params['sub_outer_iid'],
        );
        $api->url = '/v1/AgencyProduct/detail';
        $pro = json_decode($api->request(),true);
        //如果获取产品不成功，则给分销商发送站内信
        if($pro['code'] != 'succ'){
            $content = "您在淘宝上的订单：".$params['orderId']." 没有找到对应的产品，请确认是否绑定淘宝产品！（编码：". $params['sub_outer_iid']."）谢谢！！";
            $api = self::_getModelApi('ticket_organization');
            $api->params = array(
                'content' => $content,
                'sms_type'=> 1,
                'sys_type'=> 3,
                'send_source'=> 1,
                'send_status'=> 1,
                'send_user'=> 1,
                'send_organization' => 1,
//                'receiver_organization'=> $distributor['body']['id'],
//                'organization_name' => $distributor['body']['name'],
                'receiver_organization'=> $distributor['body']['organization_id'],
            );
            $api->url = '/v1/message/add';
            $sms = json_decode($api->request(),true);
            echo "sms:\n";
            var_dump($sms);
        }

        $payment = array(
            1=> 'alipay',
            2=> 'credit',
            3=> 'advance',
            4=> 'union',
        );
//        echo "pro:\n";
//        var_dump($pro);

        $res['product_id']      = $pro['body']['product_id'];
        $res['source']          = 1;
        $res['local_source']    = 1;
        $res['source_id']       = $params['orderId'];
        $res['source_token']    = $params['token'];
        $res['price_type']      = 0;
//        $res['distributor_id']  = $distributor['body']['id'];//分销商需要有购票权限才行,在 ticket_organization -> organization -> id
        $res['distributor_id']  = $distributor['body']['organization_id'];
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

        //当淘宝type字段为1，内部api不发码
        if($params['type'] == 1) $res['is_sms'] = 0;

//        echo "params:\n";
//        var_dump($res);

        Util_Logger::getLogger('taobao')->info(__METHOD__,
            array('distributor' => $distributor, 'pro' => $pro, 'params' => $res), '', '构造order/addPay数据',
            $params['orderId']
        );

        return $res;
    }
}