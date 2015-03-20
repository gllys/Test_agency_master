<?php
/**
 * @link
 */

namespace common\huilian\utils;

/**
 * Format.php
 * 格式化类,如格式化钱数输出
 * 
 * @author LRS
 */
 
class Format {
	
	/**
	 * 格式化钱数
	 * @param Numerica $money
	 * @param Integer $divisor 除数
	 */
	public static function money($money, $divisor = 100, $decPoint='.', $thousandsSep=',') {
		return number_format($money/$divisor, 2, $decPoint, $thousandsSep);
	}
	
	/**
	 * 格式化日期
	 * @param integer $timestampe 时间戳
	 * @param string|null $language 语言类型
	 */
	public static function date($timestamp, $language = null) {
		switch($language) {
			case 'zh':
				$format = 'Y年m月d日';
				break;
			default:
				$format = 'Y-m-d';	
		}
		return \date($format, $timestamp);
	}
	
	
}

?>