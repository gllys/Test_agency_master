<?php

/**
 * 机构管理控制器
 * 2014-1-2
 * @package controller
 * @author cuiyulei
 * */
class OrganizationController extends BaseController {

    //定义机构模块
    protected $organizationCommon;

    public function __construct() {
        parent::__construct();
        $this->organizationCommon = $this->load->common('organization');
    }

    //查找机构列表
    public function lists($type = 'supply') {
	    $label = array('supply' => '供应商', 'agency' => '分销商');
        $get = $this->getGet();
        $post = $this->getGet();
        $post['current'] = empty($post['p'])?1:$post['p'];
	    $cached = !empty($post) ? 0 : 1/6; // 10 minutes cache
	    $post['type'] = $type;
        //查询条件控制
        $post = array_filter($post);//移除查询条件中空值
        if($post['province']!="__NULL__")$post['province_id'] = $post['province'];
        if($post['city']!="__NULL__")$post['city_id'] = $post['city'];
        if($post['area']!="__NULL__")$post['district_id'] = $post['area'];
        if(isset($post['id'])){
            $post['id'] = trim($post['id']);
        }
        if(isset($post['name'])){
            $post['name'] = trim($post['name']);
        }

        $rs = Organizations::api()->list($post, $cached);
	    if ($rs['code'] = 'succ') {
		    $data = $rs['body'];
		    $key = array_diff(array_keys($label), array($type));
		    $key = reset($key);
		    foreach($data['data'] as &$item) {
				if ($item[$key.'_id'] > 0) {
					$info = Organizations::api()->show(array('id' => $item[$key.'_id']), $cached);
					if ($info['code'] == 'succ') {
						$item['self-support'] = $info['body']['name'];
					}
				}
                $districts = $this->load->model('districts');
                $address = "";
                if($item['province_id']>0){
                    $province =$districts->getId($item['province_id']);
                    $address .= $province['name'];
                }

                if($item['city_id']>0){
                    $city =$districts->getId($item['city_id']);
                    $address .= $city['name'];
                }

                if($item['district_id']>0){
                    $distr = $districts->getId($item['district_id']);
                    $address .= $distr['name'];
                }
                $item['address'] = $address.$item['address'];
		    }
	    }
        $page = ApiModel::getPagination($rs);
        $data['pagination'] = $this->getPagination($page);

        $data['post'] = $get;
	    $data['type'] = $type;
	    $data['label'] = $label[$type];
        $data['verifyStatus'] = organizationCommon::getVerifyStatus();
        $this->load->view('organization/list', $data);
    }

	public function supply() {
		$this->lists('supply');
	}

	public function agency() {
		$this->lists('agency');
	}

	public function register(){
		$this->load->view('organization/register');
	}

	public function enroll(){
		$this->load->view('organization/enroll');
	}

    public function scenic(){
        $this->load->view('organization/scenic');
    }
    /**
     * 添加机构
     *
     * @return void
     * @author cuiyulei
     * */
    public function add() {
        $data = $this->organizationCommon->getBankList();
        $this->load->view('organization/add', $data);
    }

    //查看机构信息
    public function view() {
        $get = $this->getGet();
        $rs = Organizations::api()->show(array('id'=>$get['id']));
        $data['data'] = array($rs['body']);

        $address = "";
        $districts = $this->load->model('districts');
        if($data['data']['province_id']>0){
            $province =$districts->getId($data['data']['province_id']);
            $address .= $province['name'];
        }

        if($data['data']['city_id']>0){
            $city =$districts->getId($data['data']['city_id']);
            $address .= $city['name'];
        }

        if($data['data']['district_id']>0){
            $distr = $districts->getId($data['data']['district_id']);
            $address .= $distr['name'];
        }
        $data['data']['district'] = $address;

        //$data = $this->organizationCommon->getOrganization($get);
        $this->load->view('organization/show', $data);
    }

    //编辑机构信息
    public function edit() {
        $get = $this->getGet();
        if (!$get['id']) {
            exit('ID 不能为空');
        }
        $rs = Organizations::api()->show(array('id'=>$get['id']));
        $districts = $this->load->model('districts');
        $data['data'] = array($rs['body']);
        if($rs['body']['province_id']){
            $province =$districts->getId($rs['body']['province_id']);
            $data['data'][0]['districts'] = array('id'=>$rs['body']['province_id'],'name'=>$province['name']);
        }
        if($rs['body']['city_id']){
            $city =$districts->getId($rs['body']['city_id']);
            $data['data'][0]['city'] = array('id'=>$rs['body']['city_id'],'name'=>$city['name']);
        }
        if($rs['body']['district_id']){
            $area =$districts->getId($rs['body']['district_id']);
            $data['data'][0]['area'] = array('id'=>$rs['body']['district_id'],'name'=>$area['name']);
        }

        $this->load->view('organization/edit', $data);
    }

    //更新景区关联机构信息
    public function changestatus(){
    	$get = $this->getGet();
    	$id = $get['id'];
    	$status = $get['status'];
    	$orgModel = $this->load->model("Organizations");
    	$orgModel->update(array("status"=>$status),'id='.$id);
    }

    /**
     * 机构审核
     *
     * @return void
     * @author cuiyulei
     * */
    public function verify() {
	    $params = $this->getGet();
	    $params['verify_status'] = $params['status'] == 'checked' ? 'reject' : 'checked';
	    unset($params['status']);
        $params['uid'] = $_SESSION['backend_userinfo']['id'];
	    $data = Organizations::api()->edit($params);
	    if ($data['code'] = 'succ') {
		    echo 1;
	    } else {
		    echo 0;
	    }
    }

    //保存
    public function save() {
        $post = $this->getPost();

        if($post['province'] == ''){
            echo json_encode(array('errors' => '省市区至少选择一个' ));
            exit;
        }
        $post['uid'] = $_SESSION['backend_userinfo']['id'];
        $post['district_id'] = $post['area']; unset($post['area']);
        $post['city_id'] = $post['city'];unset($post['city']);
        $post['province_id'] = $post['province'];unset($post['province']);
        $post['business_license'] = $post['licence_id'];unset($post['licence_id']);
        $post['tax_license'] = $post['tax_id'];unset($post['tax_id']);
        $post['certificate_license'] = $post['certificate_id'];unset($post['certificate_id']);
        $post['logo'] = $post['logo_id'];unset($post['logo_id']);

        $rs = Organizations::api()->edit($post);
        if($rs['code'] == 'succ'){
            echo json_encode(array('data'=>$post));
        }else{
            echo json_encode(array('errors'=>$rs['message']));
        }
    }

    /**
     * 员工管理
     *
     * @return void
     * @author cuiyulei
     * */
    public function staff() {
        //获取参数
        $get = $this->getGet();
        if($get['type']=='agency'){
           // AgencyUser::api()->debug = true;
            $rs = AgencyUser::api()->lists(array('organization_id'=>$get['organization_id'])); 
        }else{
            $rs = SupplyUser::api()->lists(array('organization_id'=>$get['organization_id']));  
        }
        $data = empty($rs['data'])?array():$rs['data'] ;
        //加载视图
        $this->load->view('organization/staff', $data);
    }

    /**
     * 添加员工
     *
     * @return void
     * @author cuiyulei
     * */
    public function addStaff() {
        $organization = $this->organizationCommon->getOrganization($this->getGet());
        $data['organization'] = $organization['data'][0];
        $data['type'] = 'add';
        $this->load->view('organization/staff_add', $data);
    }

    /**
     * 编辑员工
     *
     * @return void
     * @author cuiyulei
     * */
    public function editStaff() {
        $id = $this->getGet('id');
        $userModel = $this->load->model('users');
        $info = $userModel->getOne(array('id' => $id));
        $data['info'] = $info;
        $data['type'] = 'edit';
        $organization = $this->organizationCommon->getOrganization(array('id' => $info['organization_id']));
        $data['organization'] = $organization['data'][0];
        $this->load->view('organization/staff_add', $data);
    }

    /**
     * 操作员工
     *
     * @return void
     * @author cuiyulei
     * */
    public function doStaff() {
        $post = $this->getPost();
        if (!$post['id']) {
            echo json_encode(array('data' => 'fail'));
            exit;
        }
        $ids = implode(',', $post['id']);
        $UserCommon = $this->load->common('user');
        if ($post['type'] == 'del') {
            $UserModel = $this->load->model('users');
            $UserModel->update(array('deleted_at' => date('Y-m-d H:i:s', time())), "id IN ($ids)");
        } elseif ($post['type'] == 'status') {
            $UserCommon->editStatus($ids);
        }
        echo json_encode(array('data' => 'success'));
        exit;
    }

    /**
     * 保存员工
     *
     * @return void
     * @author cuiyulei
     * */
    public function saveStaff() {
        $post = $this->getPost();
        if ($post['type'] == 'edit') {
            unset($post['type']);
            if (empty($post['password'])) {
                unset($post['password']);
            }
            $this->doAction('user', 'userEdit', $post);
        } else {
            unset($post['type']);
            $post['created_by'] = $_SESSION['backend_userinfo']['id'];
            $post['organization_id'] = $post['organization_id'];
            $post['created_at'] = date('Y-m-d H:i:s');
            $post['updated_at'] = date('Y-m-d H:i:s');
            echo $this->doAction('user', 'userAdd', $post);
            exit;
        }
    }

    /**
     * 获取poi的弹框
     *
     * @return void
     * @author cuiyulei
     * */
    public function getModalJumpPoi() {
        $poiId = $this->getGet('id');
        $poiModel = $this->load->model('poi');
        $poiLModel = $this->load->model('poiLastEdit');
        $param = array(
            'relate' => 'level',
            'with' => 'districts',
            'filter' => array('id' => $poiId)
        );

        $paramL = array(
            'relate' => 'level',
            'with' => 'districts',
            'filter' => array('poi_id' => $poiId)
        );

        $poi = $poiModel->commonGetList($param);
        $poiLast = $poiLModel->commonGetList($paramL);
        $data['poi'] = $poi['data'][0];
        $data['poiLast'] = $poiLast['data'][0];

        //加载视图
        $this->load->view('organization/poi_tpl', $data);
    }

    /**
     * poi verify
     *
     * @return void
     * @author cuiyulei
     * */
    public function poiVerify() {
        $post = $this->getPost();
        echo $this->doAction('poi', 'verify', $post);
    }

    /**
     * 拥有景区审核
     *
     * @return void
     * @author cuiyulei
     * */
    public function poi() {
        $poiModel = $this->load->model('poi');
        $organizationsModel = $this->load->model('organizations');
        $page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $param = array(
            'page' => $page,
            'items' => 10,
            'relate' => 'level,organization',
            'with' => 'districts',
            'order' => $poiModel->table . '.updated_at DESC',
        );

        $get = $this->getGet();

        //提交时间 按照更新时间检索
        if (!empty($get['updated_at']) && isset($get['updated_at'])) {
            $timeFilter = explode(' - ', $get['updated_at']);
            $timeFilter[1] = date('Y-m-d', strtotime($timeFilter[1]) + 86400);
            $param['filter'][$poiModel->table . '.updated_at|between'] = $timeFilter;
        }

        //状态
        if (!empty($get['status']) && isset($get['status'])) {
            $param['filter'][$poiModel->table . '.status'] = $get['status'];
        }

        //机构编号
        if (!empty($get['organization_id']) && isset($get['organization_id'])) {
            $param['join'][] = array(
                'join' => $organizationsModel->table . ' ON ' . $organizationsModel->table . '.id=' . $poiModel->table . '.organization_id',
            );
            $param['filter'][$organizationsModel->table . '.id'] = $get['organization_id'];
        }

        //机构名称
        if (!empty($get['organization_name']) && isset($get['organization_name'])) {
            $param['join'][] = array(
                'join' => $organizationsModel->table . ' ON ' . $organizationsModel->table . '.id=' . $poiModel->table . '.organization_id',
            );
            $param['filter'][$organizationsModel->table . '.name|like'] = $get['organization_name'];
        }

        //机构是否启用
        if (!empty($get['organization_status']) && isset($get['organization_status'])) {
            $param['join'][] = array(
                'join' => $organizationsModel->table . ' ON ' . $organizationsModel->table . '.id=' . $poiModel->table . '.organization_id',
            );
            $param['filter'][$organizationsModel->table . '.status'] = $get['organization_status'];
        }

        $data['get'] = $get;
        $poiList = $poiModel->commonGetList($param);
        $data['pagination'] = $this->getPagination($poiList['pagination']);
        $data['poiList'] = $poiList['data'];

        //加载视图
        $this->load->view('organization/poi', $data);
    }

    /**
     * 发送消息
     *
     * @return void
     * @author cuiyulei
     * */
    public function sendMessage() {
	    $now = date('Y-m-d H:i:s');
	    $data['msg_type'] = 'system';
	    $data['content'] = $this->getPost('con');
	    $data['status'] = 'normal';
        $data['receive']['to_organization_id'] = $this->getPost('oid');
        $data['receive']['created_at'] = $now;
        $data['receive']['updated_at'] = $now;
	    $data['created_at'] = $now;
	    $data['updated_at'] = $now;
	    $result = SupplyMessages::api()->trans($data);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function attach(){
        $get = $param = $this->getGet();
        $param['type'] = 'agency';
        $cached = !empty($param) ? 0 : 1/6; // 10 minutes cache
        //查询条件控制
        $param = array_filter($param);//移除查询条件中空值
        if($param['province']!="__NULL__")$param['province_id'] = $param['province'];
        if($param['city']!="__NULL__")$param['city_id'] = $param['city'];
        if($param['area']!="__NULL__")$param['district_id'] = $param['area'];
        if(isset($param['name'])){
            $param['name'] = trim($param['name']);
        }
        $param['current'] = empty($param['p'])?1:$param['p'];
        $rs = Organizations::api()->list($param, $cached);
        if ($rs['code'] = 'succ') {
            $data = $rs['body'];
            foreach($data['data'] as &$item) {
                $result = Credit::api()->listbyxf(array('distributor_id'=>$item['id']));
                if(isset($result['body']['data'])&&!empty($result['body']['data'])){
                    //如果存在所属供应商
                    $item['attachList'] = $result['body']['data'];
                }
                $districts = $this->load->model('districts');
                $address = "";
                if($item['province_id']>0){
                    $province =$districts->getId($item['province_id']);
                    $address .= $province['name'];
                }

                if($item['city_id']>0){
                    $city =$districts->getId($item['city_id']);
                    $address .= $city['name'];
                }

                if($item['district_id']>0){
                    $distr = $districts->getId($item['district_id']);
                    $address .= $distr['name'];
                }
                $item['address'] = $address==""?"-":$address;
            }
        }
        $page = ApiModel::getPagination($rs);
        $data['pagination'] = $this->getPagination($page);

        $data['get'] = $get;
        $data['verifyStatus'] = organizationCommon::getVerifyStatus();
        $this->load->view('organization/attach', $data);
    }

    public function getAttach(){//获得用户所属的供应商及所有供应商列表
        $id = $this->getPost('id');
        $data['mySupply'] = array();
        $data['supplyList'] = array();
        $rs = Credit::api()->listbyxf(array('distributor_id'=>$id));
        if(isset($rs['body']['data'])&&!empty($rs['body']['data'])){
            //如果存在所属供应商
            $data['mySupply'] = $rs['body']['data'];
        }
        $result = Organizations::api()->list(array('type'=>'supply','items'=>1000));
        if($result['code'] == "succ" && isset($result['body']['data'])){
            $data['supplyList'] = $result['body']['data'];
        }
        echo json_encode($data);
    }

    public function saveAttach(){
        $agency_id = $this->getPost('agency_id');
        $ids = $this->getPost('ids');
        $rs = Organizations::api()->bind_agency_batch(array('supply_ids'=>$ids,'agency_id'=>$agency_id));
        if($rs['code']=="succ"){
            $data['error'] = 1;
            $data['message'] = "保存成功";
        }else{
            $data['error'] = 0;
            $data['message'] = $rs['message'];
        }
        echo json_encode($data);
    }



    //供应商注册
    public function registerOrganzation(){
    	$post = $this->getPost();
    	if($post['area'] == '' || $post['city'] == '' || $post['province'] == ''){
            echo json_encode(array('errors'=> '省市区至少选择一个'));
            exit;
        }

        $paramL = array(
    		'uid' 				=>  $_SESSION['backend_userinfo']['id'],
    		'district_id' 		=>  $post['area'],
    		'city_id' 			=>  $post['city'],
    		'province_id' 		=>  $post['province'],
    		'business_license' 	=>  $post['licence_id'],
    		'status' 			=> 	1,
    		'verify_status' 	=> 	'checked',
    		'address' 			=> 	$post['address'],
    		'contact' 			=> 	$post['contact'],
    		'mobile' 			=> 	$post['mobile'],
    		'name' 				=> 	$post['name'],
    		'fax' 				=> 	$post['fax'],
    		'email'				=> 	$post['email'],
    		'telephone' 		=> 	$post['telephone'],
    		'contact' 			=> 	$post['contact']
    		);
        if($post['type'] == 'agency'){
        	$paramL['tax_license'] 			= 	$post['tax_id'];
        	$paramL['certificate_license'] 	= 	$post['certificate_id'];
        	$paramL['is_distribute_person'] = 	$post['is_distribute_person'];
        	$paramL['is_distribute_group'] 	= 	$post['is_distribute_group'];
        	$paramL['agency_type']			=	$post['agency_type'];
        	$paramL['type'] 				= 	'agency';
        }else{
            //确定是否为景区机构
        	if($post['sell_role'] == 'scenic'){
                $paramL['supply_type']      =   1;
            }else{
                $paramL['supply_type']      =   0;
            }
            $paramL['type'] 			    =	'supply';
        }        
        
        //优先判断用户是否存在
        if($post['type'] == 'agency'){
    		$result = AgencyAccount::api()->search(array('account'=>$post['account']));
    	}else{
    		$result = SupplyAccount::api()->search(array('account'=>$post['account']));
    	}
   	
    	if($result['data']){
			echo json_encode(array('errors'=> '用户名已存在'));
			exit;
		}
        $rs = Organizations::api()->reg($paramL);
        if($rs['code'] == 'succ'){
        	$param['organization_id'] = $rs['body']['id'];
        	$paramU = array(
	            'account' => $post['account'],
	            'password' => password_hash($post['password'], PASSWORD_BCRYPT, array('cost' => 8)),
                'password_str' => $post['password'],
	            'organization_id' => $param['organization_id'],
	            'status' => 1,
	            'is_super' => 1,
	            'mobile' => $post['mobile'],
	            'created_at' => date('Y-m-d H:i:s'),
	            'updated_at' => date('Y-m-d H:i:s'),
            );
        	//根据机构类型的不同，添加不同的用户
        	if($post['type'] == 'agency'){
            	$userRs = AgencyAccount::api()->add($paramU);
            }else{
                $paramU['sell_role'] = $post['sell_role'];
            	$userRs = SupplyAccount::api()->add($paramU);
            }
        	
        	//AgencyUser::api()->debug = true;
            if ($userRs['code'] != 'succ') {
				echo json_encode(array('errors'=> '保存用户失败'));
                $paramEdit = array(
                    'id'     => $param['organization_id'],
                    'uid'    => $_SESSION['backend_userinfo']['id'],
                    'is_del' => 1
                );
                Organizations::api()->edit($paramEdit);
            }else{
            	echo json_encode(array('data'=>$post));
            }
        }else{
            echo json_encode(array('errors'=>$rs['message']));
        }
    }

}

// END class
