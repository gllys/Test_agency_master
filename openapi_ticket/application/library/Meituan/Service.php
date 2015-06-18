<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-5-15
 * Time: 下午4:26
 */
class Meituan_Service {

    public static $partnerId = '';
    private static $clientId = '';
    private static $clientSecret = '';
    private static $url = '';

    private static $instance = null;

    /**
     * @param bool $is_valid            是否需要美团验证
     * @return Meituan_Service|null
     */
    public static function create($is_valid = false){
        if(!isset(self::$instance)){
            $config = Yaf_Registry::get('config');
            self::$partnerId    = $config['meituan']['partnerId'];
            self::$clientId     = $config['meituan']['clientId'];
            self::$clientSecret = $config['meituan']['clientSecret'];
            self::$url          = $config['meituan']['url'];

            if(!$is_valid) self::validSign();

            self::$instance = new Meituan_Service();
        }
        return self::$instance;
    }

    public function request($data, $uri){
        date_default_timezone_set('GMT');
        $date = date('D, d M Y H:i:s e', time());
        date_default_timezone_set('PRC');
        $authorization = $this->buildSign('POST', $uri, $date);

        $header = array(
            "Content-Type: application/json; charset=utf-8",
            "Date: " . $date,
            "PartnerId: " . self::$partnerId,
            "Authorization: " . $authorization,
        );
        $data = array_merge(array(
            'code'      => 200,
            'describe'  => 'success',
            'partnerId' => self::$partnerId,
        ), $data);

        Util_Logger::getLogger('meituan')->info(__METHOD__, array('header' => $header, 'data' => $data), '', '通知美团参数');

        $data = Pack_Json::encode($data);
        $res_json = Tools::curl(self::$url . $uri, 'POST', $data, $header);
        $res = Pack_Json::decode($res_json);
        if(!$res){
            Util_Logger::getLogger('meituan')->info(__METHOD__, $res_json, '', '返回数据json解析错误');
        }
        return $res;
    }

    public function outputError($res = '', $method = __METHOD__, $searchKey = '', $logData = NULL){
        if(is_array($res)){
            $data = array_merge(array(
                'code'      => 300,
                'partnerId' => self::$partnerId,
            ),$res);
        }else{
            $data = array(
                'code'      => 200,
                'describe'  => $res,
                'partnerId' => self::$partnerId,
            );
        }
        if($logData === NULL) {
            $logData = $res;
        }
        Util_Logger::getLogger('meituan')->error($method, $logData, '', '错误输出', $searchKey);
        Lang_Msg::output($data, 200, JSON_UNESCAPED_UNICODE);
    }
    public function outputSucc($res = array(), $desc = 'success'){
        $data = array_merge(array(
            'code'      => 200,
            'describe'  => $desc,
            'partnerId' => self::$partnerId,
        ),$res);
        Lang_Msg::output($data, 200, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function validSign(){
        //todo sign 验证

        //测试特殊入口
        if($_SERVER['HTTP_SIGN'] == 'debug') return true;

        if($_SERVER['HTTP_AUTHORIZATION'] != self::buildSign( $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['HTTP_DATE'])){
            self::outputError('BA验证错误');
        }
        return true;
    }

    private function buildSign($method, $uri, $date){

//        $string_to_sign = "POST /rhone/lv/deal/change/notice" . "\n" . "Wed, 06 May 2015 10:34:20 GMT";
        $string_to_sign = $method . ' ' . $uri . "\n" . $date;

        $client_secret = self::$clientSecret;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $client_secret, true));
        $authorization = 'MWS ' . self::$clientId . ':' . $signature;

        return $authorization;
    }

}