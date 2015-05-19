<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-16
 * Time: 下午7:33
 */

class Taobao_TopClientFactory {

    //是否实例化
    private static $fact = null;

    public static function create(){

        if(self::$fact) return self::$fact;

        self::$fact = new stdClass();
        self::$fact->noticeKey = '640acdef3601b939cf4a64bf09a2aa7c';//af378982472ec11ced24a4bc1d18497f
        self::$fact->merchantId = '2346902211';//2053067000
        self::$fact->sessionKey = '6101a09fad0cca12d185838616f614ac6c013070d7dd2cf2346902211';//6100c26202f4a68bc40a445a291ea2135d1b6c51f9939ea2054718218

        self::$fact->topc            = new Taobao_TopClient();
        self::$fact->topc->appkey     = '23064138';//1023064138
        self::$fact->topc->secretKey  = 'c4ac1087a720ffb4a23f0f0dde29c167';//sandbox7a720ffb4a23f0f0dde29c167
        self::$fact->topc->gatewayUrl = 'http://gw.api.taobao.com/router/rest';//http://gw.api.tbsandbox.com/router/rest

        return self::$fact;
    }
}