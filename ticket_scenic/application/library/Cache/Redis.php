<?php
/**
 * REDIS缓存
 * @author  mosen
 */
class Cache_Redis implements Countable
{
    protected static $instances = array();
    protected static $default = array('host' => '127.0.0.1', 'port' => 6379, 'db' => 0);

    protected $redis;
    protected $connected = false;
    protected $config;
    protected $queue = array();
    protected $try = 0;
    protected $tryLimit = 3;
    
    /**
     * [factory description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function factory($name='default') {
        if(!isset(self::$instances[$name])) {
            $cls = __CLASS__;
            self::$instances[$name] = new $cls(self::getConfig($name));
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
        $redis = $config['redis'];
        // print_r($redis);
        return $redis && isset($redis['servers'][$name]) ? $redis['servers'][$name] : self::$default;
    }

    /**
     * [__construct description]
     * @param [type] $options [description]
     */
    public function __construct($options) {
        $this->config = new RedisConfig($options);
    }
    
    /**
     * [_connect description]
     * @return [type] [description]
     */
    private function _connect() {
        if (!$this->connected) {
            if (!class_exists('Redis', false))
                throw new Exception('redis not found.');
            $this->redis = new Redis();
            $command = $this->config->pconnect ? "pconnect" : "connect";
            if (!$this->connected = call_user_func_array(array($this->redis, $command), array($this->config->host, $this->config->port, $this->config->timeout))) {
                throw new Exception('redis connect fail');
            }
        }
        return $this->connected;
    }
    
    /**
     * [connect description]
     * @return [type] [description]
     */
    public function connect() {
        try {
            $this->_connect();
            $this->try = 0;
        }
        catch(Exception $e) {
            $this->try++;
            if ($this->try > $this->tryLimit) {
                throw $e;
            } else {
                usleep(500);
                $this->connect();
            }
        }
    }
    
    public function __call($command, $params = array()) {
        $this->connect();
        return call_user_func_array(array($this->redis, $command), $params);
    }
    
    public function count() {
        return count($this->queue);
    }
    
    public function commit() {
        $this->connect();
        $this->redis->multi(Redis::MULTI);
        foreach ($this->queue as $item) {
            call_user_func_array(array($this->redis, $item[0]), $item[1]);
        }
        $this->redis->exec();
        $this->cleanAll();
    }
    
    public function push($command, $params) {
        $this->queue[] = array($command, $params);
        return $this;
    }
    
    public function cleanAll() {
        $this->queue = array();
    }
    
    public function close() {
        $this->connected = false;
        $this->redis->close();
        return true;
    }
    
    public function __destruct() {
        $this->close();
    }
}

/**
 * REDIS CONFIG
 * @author mosen
 */
class RedisConfig
{
    public $host = '127.0.0.1';
    public $port = '6379';
    public $timeout = 60;
    public $pconnect = false;
    
    public function __construct($config) {
        foreach ($config as $key => $item) $this->$key = $item;
    }
}
