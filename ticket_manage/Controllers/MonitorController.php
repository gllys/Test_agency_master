<?php

/**
 * 监管控制器
 * 2014-07-28
 * @package controller
 * @author grg
 **/
class MonitorController extends BaseController
{

	protected $monitorCommon;

	public function __construct() {
		parent::__construct();
		$this->monitorCommon = $this->load->common('monitor');
	}

	public function lists() {
		$get  = $this->getGet();
		$post = $this->getPost();
		$post = array_merge((array)$post, (array)$get);

		$post['with_scenic'] = true;

		$data = $this->monitorCommon->getMonitor($post);

		$data['pagination'] = $this->getPagination($data['pagination']);
		$data['post']       = $post;
		$data['setting']    = $this->monitorCommon->getSetting();

		$this->load->view('monitor/list', $data);
	}

	public function create() {
		$data['relation'] = $this->monitorCommon->getMonitorRelationship();
		$this->load->view('monitor/create', $data);
	}

	public function edit() {
		$get = $this->getGet();

		$data['get']      = $get;
		$data['monitor']  = $this->monitorCommon->getOneMonitor($get['id']);
		$data['relation'] = $this->monitorCommon->getMonitorRelationship();
		$data['scenic']   = $this->monitorCommon->getScenic(array('monitor_id' => $get['id']));

		$this->load->view('monitor/edit', $data);
	}

	public function delete() {
		$this->doAction('monitor', 'delete', (array)$this->getPost());

	}

	public function save() {
		$post = $this->getPost();
		echo $this->monitorCommon->save($post);
		exit;
	}

	public function monitor() {
		//if (Yii::app()->request->isAjaxRequest) {
		$get  = $this->getGet();
		$data = $this->monitorCommon->getMonitorNoHead(array('term' => $get['term']), true);

		header('Content-type: application/json');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
		//}
	}

	public function scenic() {
		//if (Yii::app()->request->isAjaxRequest) {
		$get  = $this->getGet();
		$data = $this->monitorCommon->getScenic(array('term' => $get['term']), true);

		header('Content-type: application/json');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
		//}
	}

	//为机构设置监管机构
	public function catchMonitor() {
		$this->doAction('monitor', 'catchMonitor', (array)$this->getPost());

	}

	//解除监管关系
	public function releaseMonitor() {
		$this->doAction('monitor', 'releaseMonitor', (array)$this->getPost());

	}

	//为景区设置监管机构
	public function catchScenic() {
		$this->doAction('monitor', 'catchScenic', (array)$this->getPost());

	}

	//解除景区的监管\
	public function releaseScenic() {
		$this->doAction('monitor', 'releaseScenic', (array)$this->getPost());

	}

	public function accountLists() {
		$get  = $this->getGet();
		$post = $this->getPost();
		$post = array_merge((array)$post, (array)$get);
		$data = $this->monitorCommon->getAccount($post);

		$data['pagination'] = $this->getPagination($data['pagination']);
		$data['post']       = $post;
		$this->load->view('monitor/account_lists', $data);
	}

	public function createAccount() {
		$get = $this->getGet();

		$landscapeModel = $this->load->model('landscapes');
        $param = array(
            'relate' => 'level,organization',
            'with'   => 'districts',
            'order'  => $landscapeModel->table . '.updated_at DESC ',
        );
	
		$data['get']       = $get;
		$data['type']      = isset($get['type']) ? intval($get['type']) : 0;
		$data['org_id']    = intval($get['id']);
		$data['relation']  = $this->monitorCommon->getMonitorRelationship();
		$list = $landscapeModel->commonGetList($param);
		$data['landscapes'] = $list['data'];

		$this->load->view('monitor/account_create', $data);
	}

	public function saveAccount() {
		$this->doAction('monitor', 'saveAccount', $this->getPost());

	}

	public function editAccount() {
		$get = $this->getGet();
		$aid = intval($get['id']);
		$monitorAccountModel = $this->load->model('monitorAccount');
		$data['account'] = $monitorAccountModel->getOne(array('id' => $aid));
		$this->load->view('monitor/account_edit', $data);

	}

	public function doStaff() {
        $post = $this->getPost();
        if (!$post['id']) {
            echo json_encode(array('data' => 'fail'));
            exit;
        }
        $ids = implode(',', $post['id']);
        if ($post['type'] == 'del') {
            $this->monitorCommon->delAccount($ids);
        } elseif ($post['type'] == 'status') {
            $this->monitorCommon->editStatus($ids);
        }
        echo json_encode(array('data' => 'success'));
        exit;
    }

	public function gbSetting() {
		$post = $this->getPost();
		$val = intval(boolval($post['val']));// 0 1
		switch ($post['gb']) {
			case 'editable':
				$param = 'gb_editable';
				break;
			case 'upward' :
				$param = 'gb_upward';
				break;
			default : //针对单个监管机构
				$monitor_mod = $this->load->model('Monitor');
				$id = substr($post['gb'],5);
				$monitor_mod->query('UPDATE '.$monitor_mod->table.' SET editable ='.$val.' WHERE id ='.intval($id));
				return 1 - $val;
		}
		$setting_mod = $this->load->model('Setting');
		$setting_mod->query('UPDATE '.$setting_mod->table.' SET `value` ='.$val.' WHERE `key` ="'.$param.'"');
		return 1 - $val;
	}


} // END class 
