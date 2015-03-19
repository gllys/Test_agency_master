<?php
/**
 *
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class UsersModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'users';
	public $pk         = 'id';


	/**
	 * 返回加密的密码，不可逆的加密方式
	 *
	 * @param string $password 密码
	 * @return string
	 **/
	public function getHashedPassword($password)
	{
		return password_hash($password, PASSWORD_BCRYPT, array('cost'=>8));
	}

	/**
	 * 检查用户名和密码是否正确
	 * @param string $uname 用户名
	 * @param string $password 密码
	 * @param string $msg 返回错误信息
	 * @return boolean
	 **/
	public function checkUser($uname, $password, &$msg = '')
	{
		$cols     = 'id,account,name,organization_id,password,status';
		$exist    = $this->getOne("account='".$uname."' AND deleted_at IS NULL", '', $cols);
		if($exist){
			if(password_verify($password, $exist['password'])){
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

	//获取机构下的用户id
	public function getOrganizationUserIds($organizationId)
	{
		$list = $this->getList('organization_id='.$organizationId, '', '', 'id');
		$ids = array();
		if($list){
			foreach($list as $key => $value){
				$ids[] = $value['id'];
			}
		}
		return $ids;
	}

	//获取账号
	public function getAccountById($id)
	{
		$info = $this->getID($id, 'account');
		return $info['account'];
	}

	//添加用户
	public function saveUser($data){
		if($data['id']){
			$id = $data['id'];
			unset($data['id']);
			$this->update($data, array('id'=>$id));
			$param['filter']['id'] = $id;
		}else{
			$this->add($data);
			$param['filter']['id'] = $this->getAddID();
		}
		return $this->commonGetList($param);
	}
}