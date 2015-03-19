<?php
class URedis extends CApplicationComponent{
	
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
	 * redis连接句柄
	 *
	 * @var resource
	 */
	protected $_handler;

	/**
	 * 错误信息
	 *
	 * var string
	 */
	protected $_error;
	
	
	public function init(){
		parent::init();
		if(empty($this->host)) throw new CException("请配置redis host");
		//if(empty($this->port)) throw new CException("请配置redis端口号");
	}
	
	public function getHandler(){
		if($this->_handler==null){
			try{
				$this->_handler = new Redis ();
				$status = $this->_handler->connect ( $this->host, $this->port,$this->timeOut);
                if(!$status) throw new CException('connect Redis Error; host:'.$this->host);
			}catch(Exception $e){
                Yii::log('redisError: |host:'.$this->host.'|port:'.$this->port.'|timeout:'.$this->timeOut);
                //send log
                Yii::import('common.extensions.dc.DcLog');
                $serverId   = 'sid';
                $key	    = "key";
                $dc			= DcLog::getInstance($key, $serverId);
                $message = array('Redis Error Info|ServerIP:'.$_SERVER['SERVER_ADDR'].'|'.$e->getMessage().'|port:'.$this->port.'|timeout:'.$this->timeOut.'|time:'.date("Y-m-d H:i:s"),time());
                $dc->send_log('RedisErr',$message);
                //send log end
				return null;
			}
		}
		return $this->_handler;
	}

	public function getError(){
		return $this->_error;
	}
	
	public function __call($method, $arg_array){
		try{
			if($this->handler==null) return false;
			return call_user_func_array(array($this->handler,$method), $arg_array);
		}catch(Exception $e){
			$this->_error = $e->getMessage();
			return false;
		}
//		return call_user_method_array($method, $this->handler, $arg_array);
	}
}