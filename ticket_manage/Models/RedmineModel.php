<?php
/**
 * redmine的相关 redmine用户密码加密方式为 sha1({salt}.sha1(密码))
 *
 * 2014-2-24
 *
 * @package model
 * @author  cuiyulei
 */
class RedmineModel extends Model
{
	// 定义要操作的表名
	public $db          = 'redmine';
	public $table       = 'users';
	public $pk          = 'id';
	public $defaultCols = 'id,login,firstname,lastname,mail';

	//获取加密的密码，用于登陆验证
	public function getFormatPassword($uname, $password)
	{
		if(!trim($uname) || !$password){
			return FALSE;
		}else{
			$salt     = $this->getSaltByUname($uname);
			$format   = sha1($salt.sha1($password));
			return $format;
		}
	}

	/**
	 * 获取用户信息
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	public function getUserByUname($uname)
	{
		if (!$uname) {
			return FALSE;
		} else {
			$user = $this->getOne("login='{$uname}'");
			return $user;
		}
	}

	//通过用户名获取用户的salt码
	public function getSaltByUname($uname)
	{
		if(!trim($uname)){
			return FALSE;
		}else{
			$info = $this->getOne("login='{$uname}'", '', 'salt');
			return $info['salt'];
		}
	}

	//获取用户名称
	public function getUserNameByIds($ids)
	{
		if(is_int($ids)){
			$where = 'id='.$ids;
		}else{
			$where = "id in($ids)";
		}
		$info = $this->getList($where, '', '', 'firstname,lastname');
		if($info){
			$names = array();
			foreach($info as $value){
				$names[] = $value['lastname'].$value['firstname'];
			}
			return implode(',', $names);
		}else{
			return FALSE;
		}
	}

} //end class