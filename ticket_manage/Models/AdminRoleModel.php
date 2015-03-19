<?php
/**
 * admin role model
 *
 * 2014-2-25
 *
 * @package model
 * @author cuiyulei
 **/
class AdminRoleModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'admin_role';
	public $pk         = 'id';

	/**
	 * 获取用户的权限
	 *
	 * @param int $admin_id
	 *
	 * @return array
	 * @author cuiyulei
	 **/
	public function getUserPermission($admin_id)
	{
		$adminModel = $this->load->model('admin');
		$sql        = 'SELECT ar.* FROM '.$this->table.' ar 
						JOIN '.$adminModel->table.' ad ON ar.id=ad.role_id 
						WHERE ad.id='.$admin_id.' AND ar.disabled=0';
		$result     = $this->getOneBySql($sql);
		return $result;
	}

} // END class