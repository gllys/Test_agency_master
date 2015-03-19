<?php 
/**
 * 首页管理控制器
 * 2014-1-7
 * @package controller
 * @author cuiyulei
 **/
class HomeController extends BaseController
{
	

	/**
	 * 角色权限
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	public function role()
	{
        $roleModel    = $this->load->model('adminRole');
        $data['list'] = $roleModel->getList('disabled=0');
		$this->load->view('system/role', $data);
	}

    /**
     * 添加角色
     *
     * @return void
     * @author cuiyulei
     **/
    public function roleAdd()
    {
        $data['menuList'] = unserialize(PI_MENU);
        $data['pageType'] = 'add';
        $this->load->view('system/roleAdd', $data);
    }

    /**
     * 编辑角色
     *
     * @return void
     * @author cuiyulei
     **/
    public function roleEdit()
    {
        $id               = $this->getGet('id');
        $roleModel        = $this->load->model('adminRole');
        $data['info']     = $roleModel->getID($id);
        $data['menuList'] = unserialize(PI_MENU);
        $data['pageType'] = 'edit';
        $this->load->view('system/roleAdd', $data);
    }

    /**
     * 保存角色
     *
     * @return void
     * @author cuiyulei
     **/
    public function roleSave()
    {
        $post = $this->getPost();
        if($post['pageType'] == 'add') {
            $this->doAction('permission', 'addRole', $post);
        }elseif($post['pageType'] == 'edit'){
            $this->doAction('permission', 'updateRole', $post);
        }
    }

    /**
     * 删除角色
     *
     * @return void
     * @author cuiyulei
     **/
    public function roleDelete()
    {
        $this->doAction('permission', 'deleteRole', $this->getPost());
    }

	/**
	 * 员工管理
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	public function staff()
	{
        $adminModel = $this->load->model('admin');
        $page       = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $name       = $this->getGet('name')?$this->getGet('name'):"";

        //组织查询条件
        $param['filter']['deleted_at'] = null;
        $param['relate']               = 'role';
        $param['page']                 = $page;
        $param['items']                = 10;
        $param['filter']['name|like']  = $name;

        //获取数据
        $UserList           = $adminModel->commonGetList($param);
        $data               = $UserList;
        $data['pagination'] = $this->getPagination($data['pagination']);
        $data['get'] = $this->getGet();
		$this->load->view('system/staff', $data);
	}

	 /**
     * 添加机构员工
     *
     * @return void
     * @author cuiyulei
     **/
    public function addStaff()
    {
        $redmineModel         = $this->load->model('redmine');
        $adminModel           = $this->load->model('admin');
        $data['type']         = 'add';
        $data['redmineUsers'] = $redmineModel->getList("type='user' AND id>1");
        $userList             = $adminModel->getList('deleted_at is null AND id>1');
        $addedList            = array();

        $roleModel = $this->load->model('adminRole');
        $roles = $roleModel->getList();
        $data['roles']= $roles;

        foreach ($userList as $key => $value) {
            $addedList[] = $value['rid'];
        }

        $data['addedList']    = $addedList;

        $this->load->view('system/staff_add', $data);
    }

    public function addUser(){
        $post = $this->getPost();
        $data = $post;
        $adminModel           = $this->load->model('admin');
        if($adminModel->getOne(array('account'=>$post['account']))){
            echo json_encode(array('error'=>1,'message'=>"帐户已存在"));
        }else{
            $data['rid']          = 0;
            $data['password']     = $adminModel->getHashedPassword($_SESSION['backend_userinfo']['id'], 'huilian123');
            $data['salt']         = md5(microtime(true));
            $data['created_at']   = date('Y-m-d H:i:s');
            $data['created_by']   = $_SESSION['backend_userinfo']['id'];
            if($adminModel->add($data)){
                echo json_encode(array('error'=>0,'message'=>"保存成功"));
            }else{
                echo json_encode(array('error'=>1,'message'=>"保存到数据库失败"));
            }
        }
    }

    /**
     * 编辑机构员工
     *
     * @return void
     * @author cuiyulei
     **/
    public function editStaff()
    {
        $id = $this->getGet('id');
        $userModel = $this->load->model('admin');
        $roleModel = $this->load->model('adminRole');
        $info  = $userModel->getOne(array('id'=>$id));
        $roles = $roleModel->getList(); 
        $data['info'] = $info;
        $data['type'] = 'edit';
        $data['roles']= $roles;
        $this->load->view('system/staff_edit', $data);
    }

    /**
     * 操作员工
     *
     * @return void
     * @author cuiyulei
     **/
    public function doStaff()
    {
        $post = $this->getPost();
        if(!$post['id']){
            echo json_encode(array('data'=>'fail'));
            exit;
        }
        $ids = implode(',', $post['id']);
        $adminCommon = $this->load->common('admin');
        if($post['type']=='del'){
            $adminModel = $this->load->model('admin');
            $adminModel->update(array('deleted_at'=>date('Y-m-d H:i:s',time())),"id IN ($ids)");
        }elseif ($post['type'] == 'status'){
            $adminCommon->editStatus($ids);
        }
        echo json_encode(array('data'=>'success'));
        exit;
    }

    /**
     * 保存员工
     *
     * @return void
     * @author cuiyulei
     **/
    public function saveStaff()
    {
        $post = $this->getPost();
        if($post['type'] == 'edit'){
            unset($post['type']);
            if(empty($post['password'])){
                unset($post['password']);
            }
            $this->doAction('admin', 'userEdit',$post);
        }else{
            unset($post['type']);
            $this->doAction('admin', 'userAdd',$post);
        }
    }

	/**
	 * 修改密码
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	public function repass()
	{
		if($this->getMethod()=='POST'){
			$post = $this->getPost();
			$adminCommon = $this->load->common('admin');
			echo $adminCommon->rePass($post);
			exit;
		}
		$this->load->view('system/repass');
	}
	
} // END class 