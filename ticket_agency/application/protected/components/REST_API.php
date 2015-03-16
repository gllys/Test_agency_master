<?php

/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/15/14
 * Time: 9:58 AM
 */
class REST_API {

    private $rest;
    private $sign;
    protected $param_key;
    private static $instances = array(); #多例

    public static function api() {
        $key = get_called_class() . serialize(func_get_args());
        if (!isset(self::$instances[$key])) {
            $rc = new ReflectionClass(get_called_class());
            self::$instances[$key] = $rc->newInstanceArgs(func_get_args());
        }

        return self::$instances[$key];
    }

    function __construct() {
        $this->rest = new RESTClient();
        $url = Yii::app()->params[$this->param_key]['url'];
        $this->sign = Yii::app()->params[$this->param_key]['sign'];
        $this->rest->initialize(array('server' => $url));
    }

    private function __clone() {
        
    }

    function __call($method, $arguments) {
        $arguments = !empty($arguments) ? $arguments : array(array());
        $arguments[0] = isset($arguments[0]) ? $arguments[0] : array();
        if (!Yii::app()->user->isGuest) {
            $arguments[0]['user_type'] = 1;
            $arguments[0]['user_id'] = Yii::app()->user->uid;
            $arguments[0]['user_name'] = trim(Yii::app()->user->display_name)?Yii::app()->user->display_name:Yii::app()->user->id ;
            $arguments[0]['user_account'] = Yii::app()->user->id;
        }
        $arguments[0]['sign'] = $this->signature($arguments[0]);
        $verb = strtolower(get_class($this)) . '/' . $method;
        array_unshift($arguments, $verb);
        ApiModel::$inParam = $arguments;
        ApiModel::$outResult = $result = call_user_func_array(array($this, 'post'), $arguments);
        //LogCollect::add();
        return $result == "" ? array('code' => 'fail') : json_decode(trim($result), true);
    }

    public function signature($params) {
        if ($this->sign == 'debug')
            return 'debug';
        ksort($params);
        return md5(http_build_query($params) . $this->sign);
    }

    public function get($uri, $params, $hours = 24) {
        return $this->rest->get($uri, $params);
    }

    public function post($uri, $params, $hours = 24) {
        return $this->rest->post($uri, $params);
    }

    /**
     * 缓存版POST
     * @param $uri
     * @param $params
     * @param double $hours 为0时强制更新缓存
     * @return bool|mixed|string
     * @author grg
     */
    private function c_post($uri, $params, $hours = 0.05) {
        $key = crc32($uri . json_encode($params));
        $result = $hours == 0 ? false : Yii::app()->redis->get('rest_post_' . $key);
        if (!$result) {
            $result = $this->rest->post($uri, $params);
            if ($hours != 0) {
                Yii::app()->redis->setEx('rest_post_' . $key, 3600 * $hours, $result);
            }
        }
        return $result;
    }

    public function status() {
        return $this->rest->status();
    }

    public function debug() {
        $this->rest->debug();
    }

}
