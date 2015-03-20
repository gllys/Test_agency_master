<?php
/**
 * @link
 */

namespace common\huilian\models;

/**
 * 地区类
 * 本类会结合一些业务关系 
 */
class District {
	
	/**
	 * 获取所有省份信息
	 */
	public static function provinces($ids=array()) {
                if($ids){
                    return \Districts::model()->findAllByPk($ids);
                }
		return \Districts::model()->findAllByAttributes(array('level' => 1));
	}
	
	/**
	 * 获取正在使用的省份信息的地区码值
	 * 注意：
	 * 本初的目前是指定地区，沿袭之前的逻辑
	 */
	public static function usedProvinces() {
		return array(310000, 110000, 320000, 330000, 340000, 350000);
	}
	
	/**
	 * 采用首字母方式排列可用的地区码值
	 * @return array 字母为键值的地区数组
	 */
	public static function initial() {
		$groups = array(
			'ABCDE' => array(110000, 340000, ),
			'FGHIJ' => array(320000, 350000),
			'KLMNO' => array(),
			'PQRST' => array(310000, ),
			'UVWXYZ' => array(330000, ),
		);
		$usedProvinces = self::usedProvinces();
		$initial = array_fill_keys(array_keys($groups), array());	
		foreach(self::provinces($usedProvinces) as $province) {
			if(in_array($province->id, $usedProvinces)) {
				foreach($groups as $k => $codes) {
					if(in_array($province->id, $codes)) {
						array_push($initial[$k], $province);
						break;
					}
				}
			}
		}
		return $initial;
	}
	
}



?>