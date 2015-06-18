<?php
/**
 * @link
 */

namespace common\huilian\models;

use Yii;
use Organizations;


/**
 * 供应商类
 * 本类基于供应商封装一些常用的方法
 */
class Supply 
{

	/**
	 * 获取当前供应商所有的产品的名称
	 * 本方法适用于一些表单控件挑选产品
	 * @return array ['供应商主键'  => '供应商名称', ...]
	 */
	public static function allNames() 
	{
		
	}
	
	/**
	 * 对供应商进行名字模糊查询
	 * @return array
	 */
	public static function searchName($name) {
		$params = [
			'type' => 'supply',
			'name' => $name,
			'items' => 99999999,
		];
		$res = Organizations::api()->list($params);
		return empty($res['body']['data']) ? [] : $res['body']['data'];
	}
	
	/**
	 * 对供应商进行名字模糊查询, 返回供应商主键
	 * @return string 未查到返回空字符串
	 */
	public static function searchNameForIds($name) {
		return implode(',', array_keys(self::searchName($name)));
	}
}

?>