<?php
/**
 *  用户相关 
 * 
 * 2014-02-26
 *
 * @author  cuiyulei
 * @version 1.0
 */
class AdminCommon extends BaseCommon
{
	protected $_code = array(
		'-1'  => '{"errors":{"msg":["post data is null"]}}',
		'-2'  => '{"errors":{"msg":["保存至数据库失败"]}}',
		'-3'  => '{"errors":{"msg":["账号不能为空"]}}',
		'-4'  => '{"errors":{"msg":["账号必须在4-16个字符之间"]}}',
		'-5'  => '{"errors":{"msg":["手机号码不能为空"]}}',
		'-6'  => '{"errors":{"msg":["必须是正确的手机号码格式"]}}',
		'-7'  => '{"errors":{"msg":["姓名不能为空且必须在2-16个字符之间"]}}',
		'-8'  => '{"errors":{"msg":["密码不能为空"]}}',
		'-9'  => '{"errors":{"msg":["密码必须在6-16个字符之间"]}}',
		'-10'  => '{"errors":{"msg":["该账号已经存在"]}}',
		'-11'  => '{"errors":{"msg":["原密码不能为空且必须在6-16个字符之间"]}}',
		'-12'  => '{"errors":{"msg":["确认密码不能为空且必须在6-16个字符之间"]}}',
		'-13'  => '{"errors":{"msg":["原密码不正确"]}}',
		'-14'  => '{"errors":{"msg":["确认密码和密码须一致"]}}',
		'-15'  => '{"errors":{"msg":["机构类型必选"]}}',
		'-16'  => '{"errors":{"msg":["错误的机构类型"]}}',
		'-17'  => '{"errors":{"msg":["公司名称不能为空"]}}',
		'-18'  => '{"errors":{"msg":["公司名称已存在"]}}',
		'-19'  => '{"errors":{"msg":["用户名已存在"]}}',
		'-20'  => '{"errors":{"msg":["角色权限必选"]}}',
		'-21'  => '{"errors":{"msg":["角色权限保存失败"]}}',
		'-22'  => '{"errors":{"msg":["请至少选择一个员工进行添加"]}}'
	);
	protected $_errorMsg = '';
	protected static $status = array(
		'1' => '启用',
		'0' => '停用'
	);

	/**
	 * 验证用户登录
	 *
	 * @param array $post 用户数据 
	 * @return json
	 */
	public function authVerify($post)
	{
		if($post){
			$adminModel = $this->load->model('admin');
			$msg        = '';
			$exist      = $adminModel->checkUser(trim($post['account']), trim($post['password']), $msg);
			if($exist){
				if($exist['password']){
					unset($exist['password']);
				}
				$_SESSION['backend_userinfo'] = $exist;
				return json_encode(array('data'=> array($exist)));
			}else{
				return '{"errors":{"msg":["'.$msg.'"]}}';
			}
		}else{
			return $this->_getUserError(-1);
		}
	}

	/**
	 * 检测密码是否正确
	 *
	 * @param string $password
	 * @param string $msg
	 *
	 * @return boolean
	 * @author cuiyulei
	 **/
	public function checkPass($password, &$msg)
	{
		$adminModel = $this->load->model('admin');
		$userInpass = $adminModel->getHashedPassword($_SESSION['backend_userinfo']['id'], $password);
		$userInfo   = $adminModel->getID($_SESSION['backend_userinfo']['id']);
		if ($userInfo['password'] == $userInpass) {
			return true;
		} else {
			$msg = $this->_getUserError(-13);
			return false;
		}
	}

	/**
	 *
	 * 添加用户
	 * @param array $post 用户数据 
	 * @return json
	 */
	public function userAdd($post)
	{
		if($post){	
			if(!$post['group_members']){
				return $this->_getUserError(-22);
			}else{
				//加载管理员模型
				$adminModel = $this->load->model('admin');

				//加载redmine模型
				$redmineModel = $this->load->model('redmine');

				//获取已经添加的管理员
				$extensionListResult = $adminModel->getListBySQL('SELECT `rid` from '.$adminModel->table. ' WHERE `id`>1 and deleted_at is null');

				//获取已经添加的管理员的redmine用户id
				$extensionList = array();
				foreach ($extensionListResult as $v){
					$extensionList[] = $v['rid'];
				}

				$insertStr = $updateStrTrue = $updateStrFalse = array();

				//获取需要删除的员工
				$updateStrFalse = array_diff($extensionList,$post['group_members']);

				foreach ($post['group_members'] as $k => $v){	
					if(in_array($v,$extensionList)){
						$updateStrTrue[] = $v;
					}else{
						$data = $this->_formatAddData($v);
						$adminModel->add($data);
					}
				}

				$updateStrTrue = implode(',',$updateStrTrue);
				$updateStrFalse = implode(',',$updateStrFalse);
				$time = date('Y-m-d H:i:s');

				//更新sql
				if($updateStrTrue) {
					$updateStrTrue  = "UPDATE `{$adminModel->table}` set updated_at='".$time."' where `rid` IN ($updateStrTrue)";
					$adminModel->query($updateStrTrue);
				}
				
				
				//删除sql
				if($updateStrFalse){
					$updateStrFalse = "DELETE FROM `{$adminModel->table}` where `rid` IN ($updateStrFalse)";
					$adminModel->query($updateStrFalse);
				}
				

				
				

				return json_encode(array('data' => array('succ' => true)));
		
			}
		}else{
			return $this->_getUserError(-1);
		}
	}

	/**
	 * 组织添加数据格式
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	private function _formatAddData($rid)
	{
		$redmineModel = $this->load->model('redmine');
		$redmineUser  = $redmineModel->getID($rid);
		$data         = array(
			'rid'             => $redmineUser['id'],
			'account'         => $redmineUser['login'],
			'password'        => $redmineUser['hashed_password'],
			'salt'            => $redmineUser['salt'],
			'name'            => $redmineUser['lastname'].$redmineUser['firstname'],
			'email'           => $redmineUser['mail'],
			'role_id'         => 1,
			'created_at'      => date('Y-m-d H:i:s'),
			'created_by'      => $_SESSION['backend_userinfo']['id']	
		);
		return $data;
	}

	/**
	 *
	 * 修改用户
	 *
	 *     字段           类型                    含义
	 *     account        varchar(100)            用户名
	 *     password       varchar(100)            密码
	 *     name           varchar(100)            昵称
	 *     mobile         varchar(100)            手机
	 *
	 * @param array $post 用户数据
	 * @return json
	 */
	public function userEdit($post)
	{
		if($post){
			$adminModel = $this->load->model('admin');
			//首先要根据传过来的数据确定是否unset某些字段
			if(empty($post['password'])) {
				unset($post['password']);
			}
			if(empty($post['role_id'])) {
				unset($post['role_id']);
			}

			if(!$this->_checkUserEditData($post)){
				return $this->_errorMsg;
			}
			$data  = $this->_formatEditData($post);
			$adminModel->update($data, array('id'=> $post['id']));
			$affectedRows = $adminModel->affectedRows();
			if($affectedRows > 0) {
				$data['id'] = $post['id'];
				return json_encode(array('data'=>array($data)));
			}else{
				return $this->_getUserError(-2);
			}
		}else{
			return $this->_getUserError(-1);
		}
	}

	//编辑时候的验证
	private function _checkUserEditData($data)
	{
		$validation = $this->load->tool('Validate');
		$msg = '';
		//验证真实姓名
		if(!$validation->validateLengthBetweenAnd(array('minSize'=> 2,'maxSize'=> 16,'value'=> $data['name']))) {
			$this->_errorMsg = $this->_getUserError(-7);
			return false;
		}

		//验证手机号码
		if(trim($data['mobile'])) {
			if(!$validation->validateMobile($data['mobile'])) {
				$this->_errorMsg = $this->_getUserError(-6);
				return false;
			}
		}

		//验证密码 假如输入了密码，验证密码
		if(isset($data['password'])) {
			if(!$validation->validateRequired($data['password'])) {
				$this->_errorMsg = $this->_getUserError(-8);
				return false;
			}
			if(!$validation->validateLengthBetweenAnd(array('minSize'=> 6,'maxSize'=> 16,'value'=> $data['password']))){
				$this->_errorMsg = $this->_getUserError(-9);
				return false;
			}
		}

		//假如需要验证角色
		if(isset($data['role_id'])) {
			//验证角色
			if(!$validation->validateRequired($data['role_id'])){
				$this->_errorMsg = $this->_getUserError(-20);
				return false;
			}
		}
		return true;
	}

	//获取添加用户时的数据
	private function _formatEditData($post)
	{
		$adminModel = $this->load->model('admin');
		$data      = array(
			'name'            => $post['name'],
			'mobile'          => $post['mobile'],
			'updated_at'      => date('Y-m-d H:i:s'),
		);

		//权限
		if (isset($post['role_id'])) {
			$data['role_id']  =  $post['role_id'];
		}

		//是否重置密码
		if(isset($post['password'])) {
			$data['password'] = $adminModel->getHashedPassword($post['id'], $post['password']);
		}

		return $data;
	}

	/**
	 * 获取用户信息，也可以直接通过PI::get('userInfo') 取
	 *
	 *  
	 * @return json
	 */
	public static function getUserInfo()
	{
		return $_SESSION['backend_userinfo'];
	}

	//修改密码
	public function rePass($post)
	{
		if($post){
			if(!$this->_checkRepassData($post)){
				return $this->_errorMsg;
			}

			$adminModel    = $this->load->model('admin');
			$postData      = array(
				'updated_at' => date('Y-m-d H:i:s'),
				'password'   => $adminModel->getHashedPassword($_SESSION['backend_userinfo']['id'], $post['password']),
			);
			$adminModel->update($postData, array('id' => $_SESSION['backend_userinfo']['id']));
			if($adminModel->affectedRows() >= 1) {
				return json_encode(array('data'=>array($post)));
			} else {
				return $this->_getUserError(-1);
			}
		}else{
			return $this->_getUserError(-1);
		}
	}
	//验证提交的数据
	public function _checkRepassData($data)
	{
		$validation = $this->load->tool('Validate');
		$msg = '';
		$adminModel = $this->load->model('admin');

		//原密码
		if(!$adminModel->checkUser($_SESSION['backend_userinfo']['account'],$data['oldpass'])) {
			$this->_errorMsg = $this->_getUserError(-13);
			return false;
		}

		//新密码
		if(!$validation->validateRequired($data['password'])) {
			$this->_errorMsg = $this->_getUserError(-8);
			return false;
		}
		if(!$validation->validateLengthBetweenAnd(array('minSize'=> 6,'maxSize'=> 16,'value'=> $data['password']))) {
			$this->_errorMsg = $this->_getUserError(-9);
			return false;
		}

		if($data['password'] != $data['confirm_password']) {
			$this->_errorMsg = $this->_getUserError(-14);
			return false;
		}
		return true;
	}
	
	public static function getStatus($code)
	{
		return self::$status[$code];
	}
	//修改状态
	public function editStatus($ids)
	{
		$adminModel = $this->load->model('admin');
		return $adminModel->query("UPDATE ".$adminModel->table." SET `status`=if(`status`,0,1) WHERE id IN ($ids)");
	}

} //end class