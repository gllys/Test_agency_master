<?php
/**
 * MEMCACHE缓存
 * @author  mosen
 */
class Cache_Memcache implements Countable
{
    protected static $instances = array();
    protected static $default = array('host' => '127.0.0.1', 'port' => 11211);
    private static $useMemcached = true;

    private $config = array();
    private $memcache;
    private $connected = false;
    private $queues = array();
    private $try = 0;
    private $tryLimit = 3;

    /**
     * [factory description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function factory($name='default') {
        if (!$name) $name = 'default';
        if(!isset(self::$instances[$name])) {
            $cls = __CLASS__;
            self::$instances[$name] = new $cls(self::getConfig($name));
            self::$useMemcached = extension_loaded('Memcached');
        }
        return self::$instances[$name];
    }

    /**
     * [getConfig description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function getConfig($name) {
        $config = Yaf_Registry::get("config");
        $memcache = $config['memcache'];
        return $memcache && isset($memcache['servers'][$name]) ? $memcache['servers'][$name] : array(self::$default);
    }

    /**
     * [__construct description]
     * @param [type] $options [description]
     */
    public function __construct($options) {
        if (isset($options['host']))
            $options = array($options);

        foreach($options as $option)
            $this->config[] = new MemcacheConfig($option);
    }

    /**
     * [getExtClass description]
     * @return [type] [description]
     */
    public function getExtClass() {
        if(!self::$useMemcached && !extension_loaded('Memcache'))
            throw new Exception("Memcache extension not found.");

        return self::$useMemcached ? 'Memcached' : 'Memcache';
    }

    /**
     * [_connect description]
     * @return [type] [description]
     */
    private function _connect() {
        if (!$this->connected) {
            $cls = $this->getExtClass();
            $this->memcache = new $cls();
            foreach($this->config as $server) {
                if($server->username && $server->password){
                    $this->memcache->setOption(Memcached::OPT_COMPRESSION, false);//关闭压缩功能
                    $this->memcache->setOption(Memcached::OPT_BINARY_PROTOCOL, true);//使用binary二进制协议
                }

                if(self::$useMemcached){
                    $this->connected = $this->memcache->addServer($server->host,$server->port,$server->weight);//添加实例地址  端口号
                }
                else{
                    $this->connected = $this->memcache->addServer($server->host,$server->port,$server->persistent,$server->weight,$server->timeout,$server->retryInterval,$server->status);
                }

                if($server->username && $server->password){
                    $this->memcache->setSaslAuthData($server->username, $server->password);//设置OCS帐号密码进行鉴权
                }
            }
        }
        if (!$this->connected)
            throw new Exception("Memcache connect error.");

        return true;
    }

    /**
     * [connect description]
     * @return [type] [description]
     */
    public function connect() {
        try {
            $this->_connect();
            $this->try = 0;
        } catch (Exception $e) {
            $this->try ++;
            if ($this->try > $this->tryLimit) {
                throw $e;
            } else {
                usleep(500);
                $this->connect();
            }
        }
        return $this->memcache;
    }

    /**
     * [get description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function get($key) {
        return $this->connect()->get($key);
    }

    /**
     * [mget description]
     * @param  [type] $keys [description]
     * @return [type]       [description]
     */
    public function mget($keys) {
        return self::$useMemcached ? $this->connect()->getMulti($keys) : $this->connect()->get($keys);
    }

    /**
     * [add description]
     * @param [type]  $key         [description]
     * @param [type]  $data        [description]
     * @param integer $expire      [description]
     * @param boolean $compression [description]
     */
    public function add($key, $value, $expire=0, $compression=true) {
        if($expire>0)
            $expire+=time();
        $flag = $compression ? MEMCACHE_COMPRESSED : 0;
        return self::$useMemcached ? $this->connect()->add($key,$value,$expire) : $this->connect()->add($key,$value,$flag,$expire);
    }

    /**
     * [set description]
     * @param [type]  $key         [description]
     * @param [type]  $value       [description]
     * @param integer $expire      [description]
     * @param boolean $compression [description]
     */
    public function set($key,$value,$expire=0,$compression=true) {
        if($expire>0)
            $expire+=time();
        $flag = $compression ? MEMCACHE_COMPRESSED : 0;
        return self::$useMemcached ? $this->connect()->set($key,$value,$expire) : $this->connect()->set($key,$value,$flag,$expire);
    }

    /**
     * [flush description]
     * @return [type] [description]
     */
    public function flush() {
        return $this->connect()->flush();
    }

    /**
     * [delete description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function delete($key) {
        return $this->connect()->delete($key);
    }

    /**
     * [count description]
     * @return [type] [description]
     */
    public function count() {
        return count($this->queues);
    }

    /**
     * [commit description]
     * @return [type] [description]
     */
    public function commit() {
        foreach ($this->queues as $item) {
            if (!$result = call_user_func_array(array($this, $item[0]), $item[1]))
                throw new Exception('Memcache commit error.');
        }
        $this->queues = array();
        return true;
    }

    /**
     * [push description]
     * @param  [type] $command [description]
     * @param  [type] $params  [description]
     * @return [type]          [description]
     */
    public function push($command, $params) {
        $this->queues[] = array($command, $params);
        return $this;
    }

    /**
     * [rollback description]
     * @return [type] [description]
     */
    public function rollback() {
        $this->queues = array();
    }
}


class MemcacheConfig
{
    public $host;
    public $port=11211;
    public $persistent=true;
    public $weight=1;
    public $timeout=15;
    public $retryInterval=15;
    public $status=true;
    public $username='';
    public $password='';

    public function __construct($config) {
        foreach ($config as $key => $item) $this->$key = $item;
    }
}

