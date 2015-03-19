


<?php
/**
 *  用户相关 
 * 
 * 2013-09-04
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class UserCommon extends BaseCommon
{
	protected $_code = array(
		'-1'  => '{"errors":{"post":["post data is null"]}}',
		'-2'  => '{"errors":{"msg":["保存至数据库失败"]}}',
		'-3'  => '{"errors":{"msg":["账号不能为空"]}}',
		'-4'  => '{"errors":{"msg":["账号必须在6-16个字符之间"]}}',
		'-5'  => '{"errors":{"msg":["手机号码不能为空"]}}',
		'-6'  => '{"errors":{"msg":["必须是正确的手机号码格式"]}}',
		'-7'  => '{"errors":{"msg":["姓名不能为空且必须在2-16个字符之间"]}}',
		'-8'  => '{"errors":{"msg":["密码不能为空"]}}',
		'-9'  => '{"errors":{"msg":["密码必须在6-16个字符之间"]}}',
		'-10'  => '{"errors":{"msg":["该账号已经存在"]}}',
		'-11'  => '{"errors":{"msg":["原密码不能为空且必须在6-16个字符之间"]}}',
		'-12'  => '{"errors":{"msg":["确认密码不能为空且必须在6-16个字符之间"]}}',
		'-13'  => '{"errors":{"msg":["原密码不正确"]}}',
	);
	protected $_errorMsg = '';
	protected static $status = array(
		'1' => '启用',
		'0' => '停用'
	);
	/**
	 * 登录检测
	 *
	 *
	 */
	public function isLogin()
	{
		$controller = $this->getGet('c') ? $this->getGet('c') : PI_DEFAULT_CONTROLLER;
		if ($controller != 'login'){
			if (empty($_SESSION['backend_userinfo'])){
				redirect('/login.html', '验证已失效，请重新登录!', 0);
			}
		}
	}

	/**
	 * 验证用户登录（用户为redmine用户）
	 *
	 * @param array $post 用户数据 
	 * @return json
	 */
	public function authVerify($post)
	{
		if($post){
			$redmineModel = $this->load->model('redmine');
			$msg          = '';
			$exist        = $redmineModel->checkUser(trim($post['account']), trim($post['password']), $msg);
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
	 *
	 * 添加用户
	 *
	 *     字段           类型                    含义
	 *     account        varchar(100)            用户名
	 *     password       varchar(100)            密码
	 *     name           varchar(100)            昵称
	 *     gender         enum('male','female')   性别
	 *     email          varchar(100)            email
	 *     mobile         varchar(100)            手机
	 *     telephone      varchar(100)            座机
	 *     birthday       date                    生日
	 *     identity       varchar(100)            身份证
	 *     position       varchar(100)            职位
	 *     biography      text                    简介
	 *
	 * @param array $post 用户数据 
	 * @return json
	 */
	public function userAdd($post)
	{
		if($post){
			$UserModel = $this->load->model('users');
			if(!$this->checkUserData($post)){
				return $this->_errorMsg;
			}
			if($UserModel->getOne(array('account'=>$post['account']))){
				return $this->_getUserError(-10);
			}
			$post['password'] = $UserModel->getHashedPassword($post['password']);
                        $post['is_super'] = 1;
			$data = $UserModel->saveUser($post);
			if($data){
				return json_encode($data);
			}else{
				return $this->_getUserError(-2);
			}
		}else{
			return $this->_getUserError(-1);
		}
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
			$UserModel = $this->load->model('users');
			if(!$this->checkUserData($post)){
				return $this->_errorMsg;
			}
			if($UserModel->getListExtension(array('account'=>$post['account'],'id|notequal'=>$post['id']))){
				return $this->_getUserError(-10);
			}
			if($post['password']){
				$post['password'] = $UserModel->getHashedPassword($post['password']);
			}
			$data = $UserModel->saveUser($post);
			if($data){
				return json_encode($data);
			}else{
				return $this->_getUserError(-2);
			}
		}else{
			return $this->_getUserError(-1);
		}
	}
	/**
	 * 用户列表
	 *
	 *  
	 * @return json
	 */
	public function getUserList($param = array(), $id = 0)
	{
		$extenseUrl = '';
		if($id){
			$extenseUrl .= '/'.$id;
		}
		$extenseUrl .= $this->_getExtenseUrl($param);
		$cookie       = 'laravel_session='.$_COOKIE['laravel_session'];
		return $this->request('user', __FUNCTION__, array(), 0, $cookie, $extenseUrl);
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
			$UserModel = $this->load->model('users');
			if(!$this->checkUserData($post)){
				return $this->_errorMsg;
			}
			if(!$UserModel->checkUser($_SESSION['backend_userinfo']['account'],$post['oldpass'])){
				return $this->_getUserError(-13);
			}
			$post = array('id'=>$_SESSION['backend_userinfo']['id'],'password'=>$post['password']);
			return $this->userEdit($post);
		}else{
			return $this->_getUserError(-1);
		}
	}
	
	//验证提交的数据
	public function checkUserData($data)
	{
		$validation = $this->load->tool('Validate');
		$msg = '';
		//验证账号
		if(isset($data['account'])){
			if(!$validation->validateRequired($data['account'])){
				$this->_errorMsg = $this->_getUserError(-3);
				return false;
			}
			if(!$validation->validateLengthBetweenAnd(array('minSize'=> 6,'maxSize'=> 16,'value'=> $data['account']))){
				$this->_errorMsg = $this->_getUserError(-4);
				return false;
			}
		}
		//验证手机号码
		if(isset($data['mobile'])){
			if(!$validation->validateRequired($data['mobile'])){
				$this->_errorMsg = $this->_getUserError(-5);
				return false;
			}
			if(!$validation->validateMobile($data['mobile'])){
				$this->_errorMsg = $this->_getUserError(-6);
				return false;
			}
		}
		//验证姓名
		if(isset($data['name'])){
			if(!$validation->validateLengthBetweenAnd(array('minSize'=> 2,'maxSize'=> 16,'value'=> $data['name']))){
				$this->_errorMsg = $this->_getUserError(-7);
				return false;
			}
			//验证中文
		}
		//验证密码
		if(isset($data['password'])){
			if(!$validation->validateRequired($data['password'])){
				$this->_errorMsg = $this->_getUserError(-8);
				return false;
			}
			if(!$validation->validateLengthBetweenAnd(array('minSize'=> 6,'maxSize'=> 16,'value'=> $data['password']))){
				$this->_errorMsg = $this->_getUserError(-9);
				return false;
			}
		}
		if(isset($data['oldpass'])){
			if(!$validation->validateLengthBetweenAnd(array('minSize'=> 6,'maxSize'=> 16,'value'=> $data['oldpass']))){
				$this->_errorMsg = $this->_getUserError(-11);
				return false;
			}
		}
		if(isset($data['confirm_password'])){
			if(!$validation->validateLengthBetweenAnd(array('minSize'=> 6,'maxSize'=> 16,'value'=> $data['confirm_password']))){
				$this->_errorMsg = $this->_getUserError(-12);
				return false;
			}
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
		$UserModel = $this->load->model('users');
		return $UserModel->query("UPDATE ".$UserModel->table." SET `updated_at`='".date('Y-m-d H:i:s')."',`status`=if(`status`,0,1) WHERE id IN ($ids)");
	}
}