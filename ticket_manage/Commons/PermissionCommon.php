<?php
/**
 * 后台角色权限
 *
 * 2014-2-25
 *
 * @package commons
 * @author cuiyulei
 **/ 
class PermissionCommon extends BaseCommon
{
	protected $_code = array(
		'-1'  => '{"errors":{"msg":["null post"]}}',
		'-2'  => '{"errors":{"msg":["保存至数据库失败"]}}',
		'-3'  => '{"errors":{"msg":["删除失败"]}}'
	);

	//添加角色
	public function addRole($post)
	{
		if($post){
			$postData         = $this->_formatRoleData($post);
			$roleModel        = $this->load->model('adminRole');
			$roleModel->add($postData);

			$addId            = $roleModel->getAddID();
			if($addId) {
				$postData['id'] = $addId;
				return json_encode(array('data' => array($postData)));
			} else {
				return $this->_getUserError(-2);
			}
		}else{
			return $this->_getUserError(-1);
		}
	}

	//更新角色
	public function updateRole($post)
	{
		if($post){
			$postData         = $this->_formatRoleData($post);
			$roleModel        = $this->load->model('adminRole');
			$result           = $roleModel->update($postData, array('id'=> $post['id']));
			if($result) {
				$postData['id'] = $post['id'];
				return json_encode(array('data' => array($postData)));
			} else {
				return $this->_getUserError(-2);
			}
		}else{
			return $this->_getUserError(-1);
		}

	}

	/**
	 * 删除角色
	 * @param array $post  
	 * @return json
	 */
	public function deleteRole($post)
	{
		$roleModel        = $this->load->model('adminRole');
		$result           = $roleModel->update(array('disabled' => '1'), array('id'=> $post['id']));
		$affectedRows     = $roleModel->affectedRows();
		if($result && $affectedRows >= 1){
			return '{"succ":"succ"}';
		}else{
			return $this->_getUserError(-3);
		}
	}

	//组织表单数据
	private function _formatRoleData($post)
	{
		$postData = array(
			'name'            => $post['name'],
			'description'     => $post['description'],
			'permissions'     => implode(',', $post['permissions']),
		);
		return $postData;
	}

	/**
	 * 用户是否有权限进入该控制器
	 * 逻辑说明：
	 *           1.该用户是超级管理员则无权限限制
	 *           2.menu.php中无permission_id的则无权限限制
	 *           3.menu.php中有permission_id 根据当前用户的角色权限判断
	 *
	 * @param int $uid 用户id 
	 * @param string $controller 检测的访问的控制器
	 * @param string $action 检测的访问的方法
	 * @return bool
	 */
	public function allowInto($info)
	{
		$controller = strtolower($info['controller']);
		$action     = strtolower($info['action']);
		$menuList   = unserialize(PI_MENU);
		
		//1
		if($_SESSION['backend_userinfo']['is_super'] == 1) {
			return TRUE;
		}

		//2
		$menuPermissionId = $menuList[$controller]['menu'][$action]['permission_id'];
		if(!isset($menuPermissionId)) {
			return TRUE;
		}

		//3
		$roleModel     = $this->load->model('adminRole');
		$permissions   = $roleModel->getUserPermission($_SESSION['backend_userinfo']['id']);
		if($permissions) {
			if(in_array($menuPermissionId, explode(',', $permissions['permissions']))) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

} // END class