<?php
/**
 * 文件日志
 * @author  mosen
 */
class Log_Debug
{
	protected static $config = array();
	protected static $list = array();
	
	/**
	 * [getConfig description]
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public static function getConfig($key=null) {
		if (!self::$config) {
			$config = Yaf_Registry::get("config");
        	self::$config = $config['debug'];
		}
		return $key ? self::$config[$key] : self::$config;
	}

	/**
	 * [log description]
	 * @param  [type] $msg [description]
	 * @param  string $dir [description]
	 * @return [type]      [description]
	 */
	public static function log($msg, $dir='common'){
		$debug = self::getConfig();
		if(!$debug['open'])
			return true;
		if(is_array($msg))
			$msg = var_export($msg,true);

		if($debug['open'] == 1) {
			echo $msg . "\n";
			return true;
		}

		if(!isset(self::$list[$dir]))
			self::$list[$dir] = '';

		self::$list[$dir] .= '['.date('Y-m-d H:i:s').']' . $msg . "\n";
		
		if(BACKEND)
			self::output();
	}
	
	/**
	 * [output description]
	 * @return [type] [description]
	 */
	public static function output(){
		foreach(self::$list as $key => $msg) {
			$file = 'debug_'.date('YmdH');
			Log_Base::save($key . $file, $msg);
		}
		self::$list = array();
	}
	
}
