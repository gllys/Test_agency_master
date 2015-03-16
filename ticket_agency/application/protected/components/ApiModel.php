<?php

class ApiModel {

    public $debug = false;
    public $debugs = array();
    protected $c = null; #请求contoller 
    protected $apiUrl = null; #请求api地址，对应config main里面的 param 的 url
    protected $sign = null; #请求api地址，对应config main里面的 param 的 sign
    protected $param_key = null; #请求api地址，对应config main里面的 key
    protected $cacheId = 'cache';
    protected $curl = null;
    private static $instances = array(); #多例
    //日志参数
    public static $inParam = array();
    public static $outResult = '';

    public static function api() {
        $key = get_called_class() . serialize(func_get_args());
        if (!isset(self::$instances[$key])) {
            $rc = new ReflectionClass(get_called_class());
            self::$instances[$key] = $rc->newInstanceArgs(func_get_args());
        }

        return self::$instances[$key];
    }

    public function __construct() {
        //缺少相关参数错误提示
        if (is_null($this->param_key) || !isset(Yii::app()->params[$this->param_key])) {
            return $this->output(1, '模型param参数key未设置');
        }
        $param_key = $this->param_key;
        if (!isset(Yii::app()->params[$param_key]['url'])) {
            return $this->output(3, '模型param参数key，地址未设置');
        }

        if (!isset(Yii::app()->params[$param_key]['sign'])) {
            return $this->output(4, '模型param参数key，签名值未设置');
        }

        if (is_null($this->c)) {
            $this->c = strtolower(get_class($this));
        }
        //给相关参数赋值
        $this->apiUrl = Yii::app()->params[$param_key]['url'];
        $this->sign = Yii::app()->params[$param_key]['sign'];
        $this->init();
    }

    public function init() {
        
    }

    /**
     * @param type $name
     * @param type $arguments
     * $arguments[0] $params = array()
     * $arguments[1] $timeout = 5
     */
    public function __call($name, $arguments) {
        $serverUrl = rtrim($this->apiUrl, '/') . '/' . trim($this->c, '/') . '/' . $name . '/';
        //user_name和user_id必传
        $arguments[0] = isset($arguments[0]) ? $arguments[0] : array();
        if(!Yii::app()->user->isGuest){
            $arguments[0]['user_type'] = 1 ;
            $arguments[0]['user_id'] = Yii::app()->user->uid ;
            $arguments[0]['user_name'] = trim(Yii::app()->user->display_name)?Yii::app()->user->display_name:Yii::app()->user->id ;
            $arguments[0]['user_account'] = Yii::app()->user->id ;
        }        
        self::$inParam = $params = $arguments[0];
        $cache = isset($arguments[1]) ? $arguments[1] : false;
        $cacheTime = isset($arguments[2]) ? $arguments[2] : 5 * 60;

        if ($cache && $this->cacheId !== null) {
            $key = $serverUrl . ':' . http_build_query($params);
            $rs = $this->getCache()->get($key);
            if (empty($rs)) {
                $params['sign'] = $this->sign($params, $this->sign);
                $rs = self::curlPost($serverUrl, $params);
                $this->getCache()->set($key, $rs, $cacheTime);
            }
            $data = json_decode($rs, true);
        } else {
            $params['sign'] = $this->sign($params, $this->sign);
            $rs = $this->curlPost($serverUrl, $params);
            $data = json_decode($rs, true);
        }
        self::$outResult = $data;
        //LogCollect::add();
        return $data;
    }

    public function output($code, $message = '', $body = array()) {
        //LogCollect::add($message);
        return array('code' => $code, 'message' => $message, 'body' => $body);
    }

    protected function sign($data, $verify) {
        $this->setDebug('sign:' . $verify);
        ksort($data);
        return md5(http_build_query($data) . $verify);
    }

    //得到列表
    public static function isSucc($data) {
        if (isset($data['code']) && $data['code'] != 'fail') {
            return true;
        }
        return false;
    }

    public static function getLists($data) {
        return isset($data['body']) && isset($data['body']['data']) ? $data['body']['data'] : array();
    }

    public static function getPagination($data) {
        return isset($data['body']) && isset($data['body']['pagination']) ? $data['body']['pagination'] : array('count' => 0, 'current' => 0, 'items' => 15, 'total' => 0);
    }

    public static function getData($data) {
        return isset($data['body']) ? $data['body'] : array();
    }

    /**
     * 获取缓存组件
     * 如果$cacheId没有设置则回返回nul，表示不进行对象缓存
     * 如果$cacheId设置了并且$isGlobal不为true的话，会返回app的{$platform}_{$cacheId}(@see UPlatformManager)组件
     * 如果$cacheId设置了并且$isGlobal为true的话，会返回app的{$cacheId}组件
     * @return CDbConnection the database connection used by active record.
     *
     */
    protected function getCache() {
        if (!$this->cacheId)
            return null;
        $cacheId = $this->cacheId;

        if (!isset(Yii::app()->$cacheId))
            return null;
        $cache = Yii::app()->$cacheId;
        if ($cache instanceof CCache)
            return $cache;
        else
            throw new CException(Yii::t('yii', 'Active Record requires a "cache" CCache application component.'));
    }

    /**
     * curl POST请求
     * @param string $url
     * @param array $array
     * @return string 
     */
    public function curlPost($url, $array, $timeout = 50) {
        $useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0.1";
        $fields_string = http_build_query($array);
//        foreach ($array as $k => $v) {
//            $fields_string .= $k . '=' . $v . '&';
//        }
//        rtrim($fields_string, '&');

        $this->setDebug('serverUrl:' . $url);
        $this->setDebug('fields_string:' . $fields_string);
        $this->setDebug('total_Url:' . $url . '?' . $fields_string);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_POST, count($array));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_REFERER, "http://www.piaotai.com/");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result = curl_exec($ch);
        $this->setDebug('rs:' . $result);
        //$result = json_decode($result,true);
        return $result;
    }

    /**
     * curl GET请求
     * @param string $url 请求url
     * @param int $timeout 页面超时时间
     * @return string 
     */
    public function curlGet($url, $timeout = 5) {
        $useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0.1";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_COOKIE, $cookie); // 读取上面所储存的Cookie信息    
        curl_setopt($ch, CURLOPT_REFERER, "http://www.piaotai.com/");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result = curl_exec($ch);
        //$result = json_decode($result,true);
        return $result;
    }

    protected function setDebug($param) {
        array_push($this->debugs, $param);
    }

    public function __destruct() {
        if ($this->debug) {
            print_r($this->debugs);
        }
    }

}

?>
