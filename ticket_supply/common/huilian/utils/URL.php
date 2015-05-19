<?php
/**
 * @link
 */

namespace common\huilian\utils;

/**
 * URL.php
 * URL处理类
 * 
 * @author LRS
 */
 
class URL {
	
	/**
	 * 获取协议,值应当是http或https
	 * HTTPS
     * Set to a non-empty value if the script was queried through the HTTPS protocol. Note that when using ISAPI with IIS, the value will be off if the request was not made through the HTTPS protocol. 
	 * Does the same for IIS7 running PHP as a Fast-CGI application.
	 */
	public static function protocol() {
		return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? "https" : "http";
	}

	/**
	 * 必须有协议前缀
	 * 如果$url中不包含http, https等，则添加协议前缀
	 * @param string $url 网址
	 * @param integer $type 要添加前缀的协议类型
	 * @return string
	 */
	public static function mustProtocol($url, $type = 0) {
		$protocols = [
			0 => 'http',
			1 => 'https',
		];
		$url = trim($url);
		return preg_match('/^http/i', $url) ? $url : $protocols[$type].'://'.$url;
	}
	
	/**
	 * 必须有协议前缀，如果没有则加http协议前缀名
	 * @param string $url 网址
	 * @return string
	 */
	public static function mustHttp($url) {
		return self::mustProtocol($url);
	}
	
	/**
	 * 返回完整的url信息
	 * 特别注意：
	 * $_SERVER['HTTP_HOST'] 如果域名后有端口号，则会有端口号的
	 * $_SERVER['SERVER_NAME'] 为apache配置匹配的 ServerName 或 ServerAlias 的名称，不带端口号
	 */
	public static function full() {
		return self::protocol() .'://' .$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	
	/**
	 * 组装查询数组
	 * 注意：
	 * PHP_QUERY_RFC3986 把空格转换成%20这是通常我们所用的
	 * PHP_QUERY_RFC1738 把空格转换成+这不是通常我们所用的
	 * @param Array $paramArr 组装的查询字符串
	 * @param $unshift 是否在前面添加一个$separator
	 * @param $separator 分割符，默认是&
	 * @return String
	 */
	public static function httpBuildQuery(Array $paramArr, $unshift=false, $separator = '&') {
		if(empty($paramArr))
			return '';
		else 
			return ($unshift ? $separator : '') . http_build_query($paramArr, null, $separator, PHP_QUERY_RFC3986);
	}
	
	/**
	 * 移除url中指定参数的参数
	 * 注意：
	 * 参数顺序有两种特殊样式
	 * - 直接跟在 ? 后面的参数，此时清除需要清除掉参数值后面的& 如:
	 *   index.php?a=1&b=2 清除为 index.php?b=2 被清除掉的是 a=1&
	 * - 不直接跟在 ? 后面的参数，需要清除掉前面的 & 如：
	 *   index.php?a=1&b=2 清除为 index.php?a=1 被清除掉的是 &b=2
	 * 因此我们先清除不直接跟在?后面的参数（反向断言前面不是?）, 然后再清除直接跟在 ? 后面的参数。
	 * 如果反之，则不行，因为后面的&b会变成直接跟在 ? 后面的参数
	 *
	 * 惯例：
	 * 参数和=之间不能有空格，否则是无法正常获取的该值的，因此在$param后面添加(=|%3D)
	 * $_GET 获取转码后的URL的参数的值，会自动转换过来
	 * 对一个完整的网址进行urlencode是不可以访问的，如：http%3A%2F%2Fwww.baidu.com
	 * urlencode主要功能是解决参数的传值
	 *
	 * urlencode编码
	 * & %26
	 * = %3D
	 * ? %3F
	 *
	 * 参数指当前url中的参数，而不是url中某个参数的值（该值若是url）中的参数
	 *
	 * @param string $url
	 * @param string $param 要移除的参数
	 * @return string
	 */
	public static function removeParam($url, $param) {
		// 首先清除两边的空白符
		$url = trim($url);
		// 此处清除不直接跟在 ? 后面的参数，如: index.php?a=1&b=2&c=3... 清除b参数为 index.php?a=1&c=3... 参数前面的&同时被清除
		$url = preg_replace('/&'.$param.'(=[\s\S]*)?((?=&)|(?=#)|(?=$))/Ui', '', $url);
		// 此处清除直接跟在 ? 后面的参数，如：index.php?a=1&b=2&c=3... 清除a参数为 index.php?b=3&c=3... 参数后面的&同时被清除
		$url = preg_replace('/(?<=\?)'.$param.'(=[\s\S]*)?(&|(?=#)|(?=$))/Ui', '', $url);
		return $url;
	}
	
	/**
	 * 移除url中指定参数的参数
	 * @param string $url
	 * @param array $param 要移除的参数数组
	 */
	public static function removeParams($url, Array $params) {
		foreach($params as $param) {
			$url = self::removeParam($url, $param);
		}
		return $url;
	}
	
	/**
	 * 添加参数
	 * 如果不存在则添加，如果存在可更改参数或再次追加
	 * 注意：
	 * 本方法目前存在一个问题，当url中有很多参数时，被更改的参数的位置可能出现变动。此处日后应当优化
	 * @param string $url
	 * @param array $param 要移除的参数数组
	 * @param string $value 参数的值
	 * @param boolean $override 如果url已经存在该参数，是否覆盖，默认覆盖
	 * @return string
	 */
	public static function addParam($url, $param, $value, $override = true) {
		$url = self::removeParam($url, $param);
		if(strpos($url, '?') === false) {
			$url .= '?';
		}
		return $url .'&'.$param.'='.$value;
	}
	
}
 
?>