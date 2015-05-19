<?php
/**
 * @link
 */
namespace common\huilian\models;

use Districts;

/**
 * 行政区域类
 */
class District 
{
	
	/**
	 * 通过ar类查询数据库获取信息
	 * 注意：
	 * - 本方法直接连接数据库，而不是通过接口
	 * - 数据库省、市、县在一张表中，通过主键即可查询到名称，对它们查询的方式一样。
	 * - 如果$id为0，则返回null
	 * @param integer $id 主键
	 * @return ActiveRecord|null
	 */
	public static function ar($id) {
		return $id ? Districts::model()->findByPk($id) : null;
	}

	/**
	 * 获取省或市或县的名称
	 * 注意：
	 * - 如果不存在则返回空字符串，而不抛出异常
	 * @param integer $id 主键
	 * @return string 
	 */
	public static function nameOfAr($id) {
		$ar = self::ar($id);
		return isset($ar['name']) ? $ar['name'] : '';
	}
	
	/**
	 * 地址
	 * @param array $arr 一般包含省、市、县三级地址主键以及详细地址，约定键名分别为province_id, city_id, district_id, address
	 * @return string
	 */
	public static function addressOfArr(array $arr) {
		$address = self::nameOfAr($arr['province_id']);
		$city = self::nameOfAr($arr['city_id']);
		if(!in_array($city, ['市辖区', '县', ])) {
			$address .=  $city;
		}  
		return	$address . 
				self::nameOfAr($arr['district_id']) . 
				$arr['address'];
	}
	
}


?>