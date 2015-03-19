<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/18/14
 * Time: 1:29 PM
 */

class RedisCache
{
	private $redis; //redis对象
	protected $_error;

	/**
	 * 初始化Redis
	 * $config = array(
	 *  'server' => '127.0.0.1' 服务器
	 *  'port'   => '6379' 端口号
	 * )
	 * @param array $config
	 * @return Redis
	 * @author grg
	 */
	public function init($config = array()) {
		if ($config['host'] == '')  $config['host'] = '127.0.0.1';
		if ($config['port'] == '')  $config['port'] = '6379';
		$this->redis = new Redis();
		$this->redis->connect($config['host'], $config['port']);
		if (isset($config['db'])) $this->redis->select($config['db']);
		return $this->redis;
	}

	public function getError(){
		return $this->_error;
	}

	public function __call($method, $arg_array){
		try{
			if($this->redis==null) return false;
			return call_user_func_array(array($this->redis,$method), $arg_array);
		}catch(Exception $e){
			$this->_error = $e->getMessage();
			return false;
		}
		//		return call_user_method_array($method, $this->handler, $arg_array);
	}
} 
