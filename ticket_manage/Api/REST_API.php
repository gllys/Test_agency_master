<?php

/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/18/14
 * Time: 8:00 AM
 */
defined('JSON_UNESCAPED_UNICODE') || define('JSON_UNESCAPED_UNICODE', 256);
class REST_API {

    private $rest;
    private $sign;
    private $redis;
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
        $params = unserialize(PARAMS);
        $params = $params['params'];
        $url = $params[$this->param_key]['url'];
        $this->sign = $params[$this->param_key]['sign'];
        $this->rest->initialize(array('server' => $url));
        $redis = new RedisCache();
        $config = unserialize(PI_REDIS);
        $this->redis = $redis->init($config['cache']);
    }

    private function __clone() {
        
    }

    function __call($method, $arguments) {
        $arguments = !empty($arguments) ? $arguments : array(array());
        $arguments[0]['sign'] = $this->signature($arguments[0]);
        $verb = strtolower(get_class($this)) . '/' . $method;
        array_unshift($arguments, $verb);
        return call_user_func_array(array($this, 'post'), $arguments);
    }

    private function signature($params) {
        if ($this->sign == 'debug')
            return 'debug';
        ksort($params);
        return md5(http_build_query($params) . $this->sign);
    }

	public function get($uri, $params, $hours = 24, $format = 'json') {
		$result = $this->rest->get($uri, $params, $format);
		return json_decode(json_encode($result, JSON_UNESCAPED_UNICODE), true);
	}


	public function post($uri, $params, $hours = 24, $format = 'json') {
		$result = $this->rest->post($uri, $params, $format);
		return json_decode(json_encode($result, JSON_UNESCAPED_UNICODE), true);
	}
    /**
     * 缓存版POST
     * @param $uri
     * @param $params
     * @param int $hours 为0时强制更新缓存
     * @param string $format
     * @return bool|mixed|string
     * @author grg
     */
    private function c_post($uri, $params, $hours = 24, $format = 'json') {
        $key = crc32($uri . json_encode($params));
        $result = $this->redis->get('rest:post:' . $key);
        if (!$result || $result == "null" || $hours == 0) {
            $hours = $hours == 0 ? 8 : $hours;
            $result = $this->rest->post($uri, $params, $format);
            $this->redis->set('rest:post:' . $key, json_encode($result, JSON_UNESCAPED_UNICODE));
            $this->redis->expire('rest:post:' . $key, 3600 * $hours);
        } else {
            $result = json_decode($result, true);
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
