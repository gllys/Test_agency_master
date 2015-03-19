<?php
/**
 * admin model
 *
 * 2014-2-25
 *
 * @package model
 * @author cuiyulei
 **/
class AdminModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'admin';
	public $pk         = 'id';

	//可关联的,对应value为model前缀
	public $relateAble = array(
		'role' => 'adminRole'
	);

	//关联的字段,对应value为表字段
	public $relateField = array(
		'role' => 'role_id'
	);

	/**
	 * 检查用户名和密码是否正确
	 * @param string $uname 用户名
	 * @param string $password 密码
	 * @param string $msg 返回错误信息
	 * @return boolean
	 **/
	public function checkUser($uname, $password, &$msg = '')
	{
		$cols     = 'id,account,name,password,salt,status,is_super';
		$exist    = $this->getOne("account='".$uname."' AND deleted_at IS NULL", '', $cols);
		if($exist){
			if(sha1($exist['salt'].sha1($password)) == $exist['password']){
                if($exist['status'] == 0){
                    $msg = '账号限制';
                    return false;
                }
				return $exist;
			}else{
				$msg = '密码不正确';
				return false;
			}
		}else{
			$msg = '用户名不存在';
			return false;
		}
	}

	/**
	 * 重置密码
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	public function getHashedPassword($uid, $password)
	{
		$user = $this->getID($uid);
		if (!$user) {
			return false;
		} else {
			return sha1($user['salt'].sha1($password));
		}
	}

} // END class