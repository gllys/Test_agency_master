<?php
/**
 * 信息控制器
 * 2014-3-6
 * @package controller
 * @author cuiyulei
 **/
class MessageController extends BaseController
{
	
	public function __construct() {
        parent::__construct();
        $this->organizationCommon = $this->load->common('organization');
    }
	/**
	 * 系统公告
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	public function notice()
	{
		$get = $this->getGet();

		$messageCommon               = $this->load->common('message');
		$data                        = $messageCommon->getNotice($get);

		// print_r($data);

		$data['pagination']          = $this->getPagination($data['pagination']);
		$data['organization_status'] = MessageCommon::getOrganizationType();
		$data['status']              = MessageCommon::getVerifyStatus();
		$data['get']                 = $get;
		//加载视图
		$this->load->view('message/notice', $data);
	}

	/**
	 * 公告详情
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	public function detail()
	{
		$id = $this->getGet('id');

		$messageModel = $this->load->model('message');
		$message      = $messageModel->getID($id);
		if ($message) {
			$organizationsModel = $this->load->model('organizations');
			$usersModel         = $this->load->model('users');
			$adminModel         = $this->load->model('admin');

			//获取发布消息的机构
			if ($message['from_organization_id']) {
				$message['from_organization'] = $organizationsModel->getID($message['from_organization_id']);
			}
			
			//获取发布消息的用户
			if ($message['msg_type'] == 'all' || $message['msg_type'] == 'org') {
				$message['publish'] = $usersModel->getID($message['from_user']);
			} else {
				$message['publish'] = $adminModel->getID($message['from_user']);
			}

			//获取审核消息的用户
			if ($message['verify_by']) {
				$message['verify']  = $adminModel->getID($message['verify_by']);
			}
		}

		$data['message']  = $message;

		//加载视图
		$this->load->view('message/notice_detail', $data);
	}

	/**
	 * 审核信息
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	public function verify()
	{
		$post = $this->getPost();
		$this->doAction('message', 'verify', $post);
	}

	/**
	 * 发布消息
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	public function publish()
	{
		$page                   = $this->getGet('p') ? intval($this->getGet('p')) : 1;
		$viewType               = $this->getGet('viewType') ? intval($this->getGet('viewType')) : 2;
		if(in_array($viewType, array(1,2,3))) {
			$messageModel        = $this->load->model('message');
			$messageList         = $messageModel->getMessageListByViewType($viewType, $page);
			$data['messageList'] = $messageList['data'];

			//分页信息  
			$data['pagination']  = $this->getPagination($messageList['pagination']);
		} elseif($viewType == 4) {
			$organizationCommon  = $this->load->common('organization');
			$params              = array('items' => 1000,'filter' => 'status=\'normal\'');
			$partnerList         = $organizationCommon->getOrganization($params);
			$data['partnerList'] = $partnerList['data'];
		}
		
		$data['viewType'] = $viewType;

		//加载视图
		$this->load->view('message/publish', $data);
	}

	//删除消息
	public function delete()
	{
		$this->doAction('message', 'delete', $this->getPost());
	}

	//读消息
	public function read()
	{
		$this->doAction('message', 'read', $this->getPost());
	}

	//发消息
	public function addmsg()
	{
		$this->doAction('message', 'addmsg', $this->getPost());
	}
	
	//删除公告
	public function deleteNotice()
	{
		$this->doAction('message', 'deleteNotice', $this->getPost());
	}
	
	//读取建议
	public function suggest(){	
		$suggestModel       = $this->load->model('userSuggest');
		$organizationsModel = $this->load->model('organizations');
		$page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $param = array(
            'page' => $page,
            'items' => 10,
            'with' => 'districts',
            'order' => $suggestModel->table . '.created_at DESC ',
        );

        $get = $this->getPost();
        $data['get'] = $get;
        $list = $suggestModel->commonGetList($param);
        $data['pagination'] = $this->getPagination($list['pagination']);
        $data['list'] = $list['data'];
		$this->load->view('message/suggest', $data);
	}

	//建议回复
	public function report(){
		$get = $this->getGet();
		$id  = $get['id'];
		$state = $get['state'];
		
        $suggestModel      = $this->load->model('userSuggest');
        $organizationModel = $this->load->model('organizations');
        $reportModel       = $this->load->model('userSuggestReport');
        
        $suggest      = $suggestModel->getOne(array('id='.$id));
        $orgId        = $suggest['organization_id'];
        $organization = $organizationModel->getOne(array('id='.$orgId));

        $data = array();
        $data['suggest']      = $suggest;
        $data['organization'] = $organization;

        if($state == 1){
        	$report = $reportModel->getOne(array('suggest_id='. $id));
        	$data['report'] = $report;
        }
        $this->load->view('message/report',$data);
         
	}

	//保存回复
	public function addReport(){
		$post    = $this->getPost();
		$id      = $post['id'];
		$content = $post['content'];

		$reportModel  = $this->load->model('userSuggestReport');
		$suggestModel = $this->load->model('userSuggest');
				
		$data = array(
			'suggest_id'      => $id,
			'content'         => $content,
		);
		$reportModel->add($data);
		$upSuggest    =array(
			'state' => 1,
		);
		$where = 'id='.$id;
		$suggestModel->update($upSuggest,$where,'id');
		echo json_encode($data);
		exit();
	}
} // END class 