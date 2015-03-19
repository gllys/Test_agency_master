<?php
/**
 * session存储在REDIS
 * @author  mosen
 */
class Session_Writer_Redis extends Base_Model_Abstract
{
	protected $config;
	protected $preCacheKey = 'Session_Writer_Redis|';

	/**
	 * [__construct description]
	 * @param [type] $options [description]
	 */
	public function __construct($options) {
		$this->config = new SessionWriterRedisConfig($options);
	}

	/**
	 * [read description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function read($id) {
		return $this->redis->get($this->getCacheKey($id));
	}

	/**
	 * [write description]
	 * @param  [type] $id   [description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function write($id, $data) {
		$key = $this->getCacheKey($id);
		$this->redis->set($key, $data);
		$this->redis->expire($key, $this->config->expire);
		return true;
	}

	/**
	 * [destroy description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function destroy($id) {
		$this->getRedis()->del($this->getCacheKey($id));
		return true;
	}
}

class SessionWriterRedisConfig
{
    public $server;
	public $expire = 3600;
    
    public function __construct($config) {
        foreach ($config as $key => $item) $this->$key = $item;
    }
}