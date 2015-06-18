<?php
/**
 * @link
 */

namespace common\huilian\models;

use Yii;
use Organizations;


/**
 * 分销商类
 * 本类基于分销商商封装一些常用的方法
 */
class Agency 
{

	/**
	 * 获取当前分销商所有的产品的名称
	 * 本方法适用于一些表单控件挑选产品
	 * @return array ['分销商主键'  => '分销商名称', ...]
	 */
	public static function allNames() 
	{
		
	}
	
	/**
	 * 对分销商进行名字模糊查询
	 * @return array
	 */
	public static function searchName($name) {
		$params = [
			'type' => 'agency',
			'name' => $name,
			'items' => 99999999,
		];
		$res = Organizations::api()->list($params);
		return empty($res['body']['data']) ? [] : $res['body']['data'];
	}
	
	/**
	 * 对分销商进行名字模糊查询, 返回分销商主键
	 * @return string 未查到返回空字符串
	 */
	public static function searchNameForIds($name) {
		return implode(',', array_keys(self::searchName($name)));
	}
}

?>