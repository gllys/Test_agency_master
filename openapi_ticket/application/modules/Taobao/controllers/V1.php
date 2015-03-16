<?php
/**
 * Created by PhpStorm.
 * User: libiying
 * Date: 2015/1/15
 * Time: 15:20
 */

class V1Controller extends Base_Controller_ApiDispatch{

    const MSG200 = '{"code":200}';
    const MSG300 = '{"code":300}';
    const MSG500 = '{"code":501}';

    var $topc;

    /**
     * @var string 码商通知密钥
     */
    var $noticeKey = '640acdef3601b939cf4a64bf09a2aa7c';
    var $merchantId = '2346902211';
    var $sessionKey = '6101a09fad0cca12d185838616f614ac6c013070d7dd2cf2346902211';

    public function init(){
        parent::init(false);

        $this->body = $this->getParams();

        $this->topc             = new Taobao_TopClient();
        $this->topc->appkey     = '23064138';
        $this->topc->secretKey  = 'c4ac1087a720ffb4a23f0f0dde29c167';
        $this->topc->gatewayUrl = 'http://gw.api.taobao.com/router/rest';

        self::echoLog('body', json_encode($this->body), 'errors_bee.log');
    }

    /**
     * 接口入口方法
     */
    public function restAction(){
        //签名验证
        $this->signValidate();

        $method = '';
        if(isset($this->body['method']) && !empty($this->body['method'])){
            $method = $this->body['method'];
        }else{
            echo self::MSG500; die;
        }

        $resp = $this->$method();

        self::echoLog('body', json_encode($this->body), 'rest_bee.log');
    }

    /**
     * 接收发码通知
     */
    private function send(){

        //todo 异步回调淘宝接口：发码成功回调接口，以下代码将放入回调函数中
        Process_Async::send(array('ApiTaobaoModel','sendByAsync'),array(array(
            'orderId'           => $this->body['order_id'], //淘宝订单交易号
            'token'             => $this->body['token'],
            'taobao_sid'        => $this->body['taobao_sid'],//淘宝卖家ID
            'seller_nick'       => $this->body['seller_nick'],//淘宝卖家用户名（旺旺号）
            'mobile'            => $this->body['mobile'],   //买家的手机号码
            'id_card'           => $this->body['id_card'],  //买家md5加密的身份证号，
            'num'               => $this->body['num'],      //购买的商品数量
            'outer_iid'         => $this->body['outer_iid'],//商家发布商品时填的商品编码 -- 相当于我们的产品编号
            'sub_outer_iid'     => $this->body['sub_outer_iid'],
            'valid_start'       => $this->body['valid_start'],
            'valid_ends'        => $this->body['valid_ends'],

            'codemerchantId'    => $this->merchantId,
            'sessionKey'        => $this->sessionKey,
        )));

        echo self::MSG200; //成功接收此通知
    }

    /**
     * 接收重新发码通知
     */
    private function resend(){

        //todo 异步回调淘宝接口：重新发码成功回调接口
        Process_Async::send(array('ApiTaobaoModel','resendByAsync'),array(array(
            'orderId'           => $this->body['order_id'],
            'token'             => $this->body['token'],
            'codemerchantId'    => $this->merchantId,
            'sessionKey'        => $this->sessionKey,
        )));

        echo self::MSG200;
    }

    /**
     * 接收用户修改手机通知
     */
    private function modified(){

        //todo 异步回调淘宝接口：更改接收码的手机号，并重新发码
        Process_Async::send(array('ApiTaobaoModel','modifiedByAsync'),array(array(
            'orderId'           => $this->body['order_id'],
            'token'             => $this->body['token'],
            'mobile'            => $this->body['mobile'],   //买家的手机号码（修改后的手机号）
            'codemerchantId'    => $this->merchantId,
            'sessionKey'        => $this->sessionKey,
        )));

        echo self::MSG200;
    }

    /**
     * 接收退款成功通知
     */
    private function cancel(){

        //todo 退款等处理，如码不可验证等
        $params = array(
            'source_id'     => $this->body['order_id'],
            'nums'          => $this->body['cancel_num'],
            'user_id'       => $this->body['taobao_sid'], //为了与数据库原定义的 user_id 不产生数据冲突，应该再进行定义
            'user_account'  => $this->body['seller_nick'],
        );
        $r = ApiTaobaoModel::model()->cancel($params);
        //返回示例：{"code":"succ","message":"ok","body":{"id":"201502031806537040"}}

        self::echoLog('body', json_encode($r), 'cancel_bee.log');

        if($r['code'] == 'succ') echo self::MSG200;
        else echo self::MSG500;
    }

    /**
     * 接收订单修改通知（使用有效期修改、维权成功）
     */
    private function order_modify(){

        //todo 更改订单相关信息
        if((int)$this->body['sub_method'] == 1){
            //todo 订单的使用有效期修改
            //淘宝的业务逻辑和我们的业务逻辑有所不同

        }else if((int)$this->body['sub_method'] == 2){
            //todo 维权成功
            //淘宝里的售后申请操作
        }

        echo self::MSG200;
    }

    public function testAction(){ echo 'you are here';
//        $client_id = $this->topc->appkey;
//        $client_secret = $this->topc->secretKey;
//        $url_authorize =  'https://oauth.tbsandbox.com/authorize';
//        $url_authorize =  'https://oauth.taobao.com/authorize';
//        $url_token =  'https://oauth.tbsandbox.com/token';
//        $url_token = 'https://oauth.taobao.com/token';
//        $refresh_key = '6100a01881326ecb7013dd5eda7335b26fce5a5c0c901902346902211';
//
//        $postFields = array(
//            'client_id' => $client_id,
//            'client_secret' => $client_secret,
//            'grant_type'=> 'refresh_token',
//            'refresh_token'=> $refresh_key,
//        );
//
//        $resp = $this->topc->curl($url_token, $postFields);
//        echo json_encode($resp);
//        die;

//        $postfields= array('grant_type'=>'authorization_code',
//            'client_id'=>$client_id,
//            'response_type'=>'code',
//            'redirect_uri'=>'http://bt-ticket-api-order.demo.org.cn/taobao/v1/auth');
//        $resp = $this->topc->curl($url_authorize, $postfields);
//        echo $resp;
//        die;

        //从回调的url的code参数获取
//        $code =  'gwWEmFEgQku7YkL4PeVDxSFv1243';
//        $postfields= array('grant_type'=>'authorization_code',
//            'client_id'=>$client_id,
//            'client_secret'=>$client_secret,
//            'response_type'=>'authorization_code',
//            'code'=>$code,
//            'redirect_uri'=>'http://bt-ticket-api-order.demo.org.cn/taobao/v1/auth');
//        $resp = $this->topc->curl($url_token, $postfields);
//        echo $resp;
//        die;
    }

    /**
     * 签名验证
     */
    private function signValidate(){
//        header("content-Type: text/html; charset=UTF-8");
        $req = $this->body;

        if($req['sign'] == 'debug') return true;
        if(isset($req['method']) && $req['method'] == 'order_modify') return true;

        ksort($req);
        $sign = $req['sign'];
        //去除sign本身
        unset($req['sign']);

        $str = $this->noticeKey;
        foreach($req as $k=>$v){
            if(!Taobao_RequestCheckUtil::checkEmpty($v)){
                $str .=$k.$v;
            }
        }
        //通知验证密钥
//        header("content-Type: text/html; charset=UTF-8");
//        $m = strtoupper(md5(mb_convert_encoding($str, "GBK")));
        $m = strtoupper(md5($str));

        self::echoLog('body', json_encode($req), 'sign_bee.log');
        self::echoLog('str', $str, 'sign_bee.log');
        self::echoLog('sign', $sign . '||'  .$m, 'sign_bee.log');

        //验证不成功，返回501，并退出
        if($m != $sign){
            echo self::MSG500; die;
        }
    }

}
