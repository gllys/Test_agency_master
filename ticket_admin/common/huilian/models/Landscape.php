<?php
/**
 * @link
 */

namespace common\huilian\models;

use Yii;
use Landscape as LandscapeAPI;


/**
 * 景区类
 * 本类基于景区商封装一些常用的方法
 */
class Landscape 
{
	
	/**
	 * 对景区进行名字模糊查询
	 * @return array
	 */
	public static function searchName($name) {
		$params = [
			'keyword' => $name,
			'items' => 99999999,
		];
		$res = LandscapeAPI::api()->lists($params);
		return empty($res['body']['data']) ? [] : $res['body']['data'];
	}
	
	/**
	 * 对景区进行名字模糊查询, 返回景区主键
	 * @return string 未查到返回空字符串
	 */
	public static function searchNameForIds($name) {
		return implode(',', array_keys(self::searchName($name)));
	}
}

?>