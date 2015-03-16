<?php
class URedisCache extends CCache{
	/**
	 * redis服务器 ip地址
	 *
	 * @var string
	 */
	public $host;
	
	/**
	 * redis服务器端口号
	 *
	 * @var int
	 */
	public $port;
	
	/**
	 * 连接超时时间
	 *
	 * @var int
	 */
	public $timeOut=3;


	/**
	* 默认有效时间
	*
	* @var int
	*/
	public $defaultDuration = 86400;
	
	/**
	 * redis连接句柄
	 *
	 * @var resource
	 */
	protected $_handler;
	
	public function init(){
		parent::init();
		try{
			$this->_handler = new Redis ();
			$this->_handler->connect ( $this->host, $this->port,$this->timeOut);
		}catch(Exception $e){}
	}
	
	protected function generateUniqueKey($key)
	{
		return $this->keyPrefix.$key;
	}
	
	protected function getValue($key)
	{
		try{
			if($this->_handler == null) return false;
			return $this->_handler->get($key);
		}catch(Exception $e){
			return false;
		}
	}
	
	protected function getValues($keys)
	{
		try{
			if($this->_handler == null) return false;
			return $this->_handler->mget($keys);
		}catch(Exception $e){
			return false;
		}
	}
	
	protected function setValue($key,$value,$expire=0)
	{
		try{
			if($this->_handler == null) return false;
			if($expire==0) $expire = $this->defaultDuration;
			return $this->_handler->setex($key, $expire, $value);
		}catch(Exception $e){
			return false;
		}
	}
	
	protected function addValue($key,$value,$expire=0)
	{
		try{
			if($this->_handler == null) return false;
			if($expire==0) $expire = $this->defaultDuration;
			if($this->_handler->setnx($key, $value)){
				$this->_handler->expire($key, $expire);
				return true;
			}
			return false;
		}catch(Exception $e){
			return false;
		}
	}
	protected function deleteValue($key)
	{
		try{
			if($this->_handler == null) return false;
			return $this->_handler->delete($key);
		}catch(Exception $e){
			return false;
		}
	}
	protected function flushValues()
	{
		try{
			if($this->_handler == null) return false;
			return $this->_handler->flushAll();
		}catch(Exception $e){
			return false;
		}
	}
}