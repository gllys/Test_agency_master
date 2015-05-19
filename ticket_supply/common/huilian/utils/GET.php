<?php
/**
 * @link
 */
namespace common\huilian\utils;

/**
 * GET参数类
 * 本类主要用于处理$_GET参数
 * 
 * @author LRS
 */
class GET {
	
	/**
	 * 获取全局变量$_GET
	 * @param string $name 名称
	 * @param mixed $default 如果不存在，返回该值，默认null
	 */
	public static function name($name, $default = null) {
		return isset($_GET[$name]) ? $_GET[$name] : $default;
	}
	
	/**
	 * 获取全局变量$_GET
	 * 本方法用于form表单，一些必填字段的过滤
	 * 注意：
	 * - 当url类似`&type=&from=2015-03-12`时，认为$_GET['type']不存在。 这与`&type=0`不同。
	 * @param string $name 名称
	 * @param mixed $default 如果不存在，返回该值，默认null
	 */
	public static function required($name, $default = null) {
		return isset($_GET[$name]) && $_GET[$name] !== '' ? $_GET[$name] : $default;
	}
	
	/**
	 * 如果url中存在必须的参数$name，则把它添加到数组$arr中
	 * @param array|string $names 名称
	 * @param array $arr 默认是空数组
	 * @return array
	 */
	public static function requiredAdd($names, array $arr = []) {
		if(!is_array($names)) {
			$names = [$names];
		}
		foreach($names as $name) {
			$value = self::required($name);
			if($value !== null) {
				$arr[$name] = $value;
			}
		}
		return $arr;
	}
}



?>