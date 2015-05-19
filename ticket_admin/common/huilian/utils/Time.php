<?php

/**
 * @link
 */
namespace common\huilian\utils;

/**
 * Time.php
 * 时间类
 *
 * @author LRS
 */
class Time {

	/**
	 * 把秒数转换成时间
	 * @param integer $seconds 秒数
	 * @return array
	 */
	public static function seconds($seconds) {
		$value = [
			'years' => 0,
			'days' => 0,
			'hours' => 0,
			'minutes' => 0,
			'seconds' => 0
		];
		if($seconds >= 31556926) {
			$value['years'] = intval(floor($seconds / 31556926));
			$seconds = $seconds % 31556926;
		}
		if($seconds >= 86400) {
			$value['days'] = intval(floor($seconds / 86400));
			$seconds = $seconds % 86400;
		}
		if($seconds >= 3600) {
			$value['hours'] = intval(floor($seconds / 3600));
			$seconds = $seconds % 3600;
		}
		if($seconds >= 60) {
			$value['minutes'] = intval(floor($seconds / 60));
			$seconds = $seconds % 60;
		}
		$value['seconds'] = intval(floor($seconds));
		return $value;
	}
	
	/**
	 * 如果是个位,则前置0
	 * @param integer $num 
	 */
	public static function prefixZero($num) {
		return $num > 9 ? $num : '0'.$num;
	}
}

?>