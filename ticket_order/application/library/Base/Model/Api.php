<?php

/**
 * 数据模型基类
 * @author  mosen
 */
class Base_Model_Api
{
    protected $srvKey = 'ticket_organization';
    protected $url = '';
    protected $params = array();
    protected $method = 'POST';
    protected $memSrvKey = 'default';
    protected $expireTime = 2;
    protected static $appSecret;
    protected static $srvUrls;
    protected static $instances = array();

    public static function model() {
        $className = get_called_class();
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new $className();
            if (!self::$appSecret) {
                $config = Yaf_Registry::get("config");
                self::$appSecret = $config['api']['appSecret'];
                self::$srvUrls = unserialize($config['api']['urls']);
            }
        }
        return self::$instances[$className];
    }

    public function setParams($params = array()) {
        $this->params = $params;
        return $this;
    }

    public function getSrvUrl() {
        return self::$srvUrls[$this->srvKey];
    }

    public function setExpireTime($cd) {
        $this->expireTime = $cd;
        return $this;
    }

    public function getMemcache() {
        $key = 'memcache|' . $this->memSrvKey;
        if (!Yaf_Registry::has($key)) Yaf_Registry::set($key, Cache_Memcache::factory($this->memSrvKey));
        
        return Yaf_Registry::get($key);
    }

    public function request($header = null, $timeout = 10) {
        $url = $this->getSrvUrl() . $this->url;
        $params = $this->params;
        $method = strtoupper($this->method);
        $data = false;
        if ($method == 'GET') {
            $cacheKey = 'Base_Model_Api|'.md5(serialize(array('url'=>$url,'params'=>$params)));
            $data = $this->getMemcache()->get($cacheKey);
        }
        if (!$data) {
            $sign = $this->getSign($params);
            $params['sign'] = $sign;
            if ($method == 'GET') {
                $strParam = http_build_query($params);
                $url .= '?' .$strParam;
                $params = array();
            }
            $this->params = array();
            $data = Tools::curl($url, $method, $params, $header, $timeout);
            if ($method == 'GET') $this->getMemcache()->set($cacheKey,$data,$this->expireTime);
        }
        return $data;
    }

    public function getSign($params) {
        unset($params['sign']);
        ksort($params);
        return md5(http_build_query($params) . self::$appSecret);
    }

    public function customCache($cacheKey,$data=null,$expire=3600) {
        $mc = Cache_Memcache::factory();
        $cacheNs = $mc->get($this->preCacheKey.'NS');
        $cacheData = $mc->get($cacheKey);
        if(empty($cacheData) || $cacheData['cacheNS']!=$cacheNs) {
            if($data==null) return null;
            $res = $data;
            if(!empty($res))
                $mc->set($cacheKey,array('data'=>$res,'cacheNS'=>$cacheNs),$expire);
        }
        else {
            $res = $cacheData['data'];
        }
        return $res;
    }
}
