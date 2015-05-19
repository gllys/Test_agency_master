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
    protected $method = 'GET';
    protected $useSign  = 1;
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

    public function request($header = null) {
        $params = $this->params;
        if ($this->useSign) {
            $sign = $this->getSign($params);
            $params['sign'] = $sign;
        }
        $url = $this->getSrvUrl(). $this->url;
        if ($params && strtoupper($this->method) == 'GET') {
            $strParam = http_build_query($params);
            $url .= '?' .$strParam;
            $params = array();
        }
        // echo $url;
        $this->params = array();
        return Tools::curl($url, $this->method, $params, $header);
    }

    public function getSign($params) {
        unset($params['sign']);
        ksort($params);
        return md5(http_build_query($params) . self::$appSecret);
    }
}
