<?php
/**
 * @link
 */

namespace common\huilian\utils;

/**
 * 头类
 * 
 * @author LRS
 */
class Header {
	
	/**
	 * 在规定的时间后跳转网址
	 * @param string $url 要跳转的网址
	 * @param integer $seconds 秒数
	 * @param boolean $prompt 是否输出提示
	 */
	public static function redirect($url, $seconds, $prompt = false) {
  		header('refresh:'.$seconds.';url='.$url);
  		if($prompt)
  			echo 'You\'ll be redirected in about '.$seconds.' secs. If not, click <a href="'.$url.'">here</a>.';
	}
	
	/**
	 * 输出utf8头
	 */
	public static function utf8() {
		header('content-type:text/html;charset="utf-8"');
	}
	
}

?>