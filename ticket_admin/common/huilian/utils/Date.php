<?php
/**
 * @link
 */
namespace common\huilian\utils;

/**
 * 日期类
 * 本类主要处理一些常用的关于日期方面的功能，如本月的第一天
 */
class Date {
	
	/**
	 * 返回今天
	 */
	public static function today() {
		return date('Y-m-d');
	}
	
	/**
	 * 返回本月的第一天。
	 * @return string
	 */
	public static function firstDayOfThisMonth() {
		return Date('Y-m-d', strtotime('first day of this month'));
	}
	
	/**
	 * 返回本月的最后一天
	 */
	public static function lastDayOfThisMonth() {
		return Date('Y-m-d', strtotime('last day of this month'));
	}
	
}


?>