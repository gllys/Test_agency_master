<?php
/**
 * @link
 */
namespace common\huilian\models;

use Users;

/**
 * 管理员类
 * 注意本来是通过AR类查询，而不是通过接口（API）方式获得数据
 */
class Admin {
	
	/**
	 * 获取所有管理员的信息
	 * 注意：
	 * 数据库中名称不是唯一索引，有重名的情况。本处不过同名过滤。
	 * @return array ['管理员主键' => '管理员名称', ...]
	 */
	public static function allNames() {
		$users = Users::model()->findAll('status=1');
		$allNames = [];
		foreach($users as $user) {
			$allNames[$user->id] = $user->name;
		}
		return $allNames;
	}
}


?>