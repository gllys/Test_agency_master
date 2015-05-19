<?php
/**
 * @link
 */
namespace common\huilian\models;

use common\huilian\utils\Header;

/**
 * 性能类
 * 本类主要用于处理一些性能上的问题，如：测试接口的相应时间等
 */
class Performance {
	
	/**
	 * API性能测试类
	 * @param string $interface 接口， 例如：  `Organizations::list`
	 * @param mixed $params 传递给接口的参数
	 * @return float 响应时间，单位秒
	 */
	public static function API($interface, $params)
	{
		if(strncasecmp($interface, '\\', 1) !== 0) {
			$interface = '\\' . $interface;
		}
		list($class, $func) = explode('::', $interface);
		$start = microtime(true);
		$obj = $class::api();
		$query = $obj->$func($params);
		return microtime(true) - $start;
	}
	
	/**
	 * API输出
	 * @see API
	 */
	public static function APIEcho($interface, $params)
	{
		Header::utf8();

		$str = '[';
		foreach($params as $k => $v) {
			$str .=  '\'' . $k .'\' => \'' .$v .'\', ';
		}
		$str .= ']';	
		
		$consume = self::API($interface, $params);
		
		echo 
			'接口名称: ' . $interface. '<br/>',
			'参数: '.$str . '<br/>',
			'响应时间: ' . $consume;
			
	}
}

?>