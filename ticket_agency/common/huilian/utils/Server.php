<?php
/**
 * @link
 */

namespace common\huilian\utils;

/**
 * 全局变量$_SERVER
 * 本类主要处理一些全局变量的信息
 * 
 * @author LRS
 */
class Server {
	
	/**
	 * 获取$_SERVER['HTTP_REFERER']，如果不存在则返回 $default
	 * 注意：
	 * - $_SERVER['HTTP_REFERER'] 可能是没有定义的变量，因此需要empty
	 * @param string|null $default
	 */
	public static function HttpReferer($default = null) {	
		return empty($_SERVER['HTTP_REFERER']) ? $default : $_SERVER['HTTP_REFERER'];
	}
}




?>