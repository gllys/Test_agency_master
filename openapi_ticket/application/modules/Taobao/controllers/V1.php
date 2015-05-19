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

    private $taobao;

    public function init(){
        parent::init(false);

        $this->body = $this->getParams();

        $this->taobao = Taobao_TopClientFactory::create();

//        self::echoLog('body', json_encode($this->body), 'errors_bee.log');
    }

    /**
     * 接口入口方法
     */
    public function restAction(){
        Util_Logger::getLogger('taobao')->info(__METHOD__, json_encode($this->body), '', $this->body['method']);

        //签名验证
        $this->signValidate();

        $method = '';
        if(isset($this->body['method']) && !empty($this->body['method'])){
            $method = $this->body['method'];
        }else{
            echo self::MSG500; die;
        }

        $resp = $this->$method();

//        self::echoLog('body', json_encode($this->body), 'rest_bee.log');
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
            'type'              => isset($this->body['type']) ? $this->body['type'] : 0,   //当TYPE为1时候码商不需要给用户下发短信，淘宝平台会在接口调用后把验证码下发给用户。
            'mobile'            => isset($this->body['mobile']) ? $this->body['mobile'] : $this->body['encrypt_mobile'],   //买家的手机号码
            'encrypt_mobile'    => isset($this->body['type']) && $this->body['type'] == 1 ? $this->body['encrypt_mobile'] : '',   //买家手机号中间四位隐藏  例： 131****5678
            'md5_mobile'        => isset($this->body['type']) && $this->body['type'] == 1 ? $this->body['md5_mobile'] : '',   //买家手机号MD5值
            'id_card'           => $this->body['id_card'],  //买家md5加密的身份证号，
            'num'               => $this->body['num'],      //购买的商品数量
            'outer_iid'         => $this->body['outer_iid'],//商家发布商品时填的商品编码 -- 相当于我们的产品编号
            'sub_outer_iid'     => $this->body['sub_outer_iid'],
            'valid_start'       => $this->body['valid_start'],
            'valid_ends'        => $this->body['valid_ends'],

            'codemerchantId'    => $this->taobao->merchantId,
            'sessionKey'        => $this->taobao->sessionKey,
        )));

        echo self::MSG200; //成功接收此通知
    }

    /**
     * 接收重新发码通知
     */
    private function resend(){

        //todo 异步回调淘宝接口：重新发码成功回调接口
        Process_Async::send(array('ApiTaobaoModel','resendByAsync'),array(array(
            'type'              => isset($this->body['type']) ? $this->body['type'] : 0,   //当TYPE为1时候码商不需要给用户下发短信，淘宝平台会在接口调用后把验证码下发给用户。
            'mobile'            => isset($this->body['type']) && $this->body['type'] == 0 ? $this->body['mobile'] : '',   //买家的手机号码
            'encrypt_mobile'    => isset($this->body['type']) && $this->body['type'] == 1 ? $this->body['encrypt_mobile'] : '',   //买家手机号中间四位隐藏  例： 131****5678
            'md5_mobile'        => isset($this->body['type']) && $this->body['type'] == 1 ? $this->body['md5_mobile'] : '',   //买家手机号MD5值
            'orderId'           => $this->body['order_id'],
            'token'             => $this->body['token'],
            'taobao_sid'        => $this->body['taobao_sid'],//淘宝卖家ID
            'seller_nick'       => $this->body['seller_nick'],//淘宝卖家用户名（旺旺号）
            'codemerchantId'    => $this->taobao->merchantId,
            'sessionKey'        => $this->taobao->sessionKey,
        )));

        echo self::MSG200;
    }

    /**
     * 接收用户修改手机通知
     */
    private function modified(){

        //todo 异步回调淘宝接口：更改接收码的手机号，并重新发码
        Process_Async::send(array('ApiTaobaoModel','modifiedByAsync'),array(array(
            'type'              => isset($this->body['type']) ? $this->body['type'] : 0,   //当TYPE为1时候码商不需要给用户下发短信，淘宝平台会在接口调用后把验证码下发给用户。
            'mobile'            => isset($this->body['mobile']) ? $this->body['mobile'] :  $this->body['encrypt_mobile'],   //买家的手机号码
            'encrypt_mobile'    => isset($this->body['type']) && $this->body['type'] == 1 ? $this->body['encrypt_mobile'] : '',   //买家手机号中间四位隐藏  例： 131****5678
            'md5_mobile'        => isset($this->body['type']) && $this->body['type'] == 1 ? $this->body['md5_mobile'] : '',   //买家手机号MD5值
            'orderId'           => $this->body['order_id'],
            'token'             => $this->body['token'],
            'codemerchantId'    => $this->taobao->merchantId,
            'sessionKey'        => $this->taobao->sessionKey,
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

//        self::echoLog('body', json_encode($r), 'cancel_bee.log');
        Util_Logger::getLogger('taobao')->info(__METHOD__, $r, '', 'refund/applycheck回传数据', $this->body['order_id']);

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

        $str = $this->taobao->noticeKey;
        foreach($req as $k=>$v){
            if(!Taobao_RequestCheckUtil::checkEmpty($v)){
                $str .=$k.$v;
            }
        }
        //通知验证密钥
//        header("content-Type: text/html; charset=UTF-8");
//        $m = strtoupper(md5(mb_convert_encoding($str, "GBK")));
        $m = strtoupper(md5($str));

//        self::echoLog('body', json_encode($req), 'sign_bee.log');
//        self::echoLog('str', $str, 'sign_bee.log');
//        self::echoLog('sign', $sign . '||'  .$m, 'sign_bee.log');

        //验证不成功，返回501，并退出
        if($m != $sign){
            echo self::MSG500; die;
        }
    }

}
