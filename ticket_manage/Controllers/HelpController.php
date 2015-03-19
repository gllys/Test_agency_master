<?php

/**
 * 帮助文档控制器
 * 2014-07-18
 * @package controller
 * @author grg
 **/
class HelpController extends BaseController
{

	protected $helpCommon;

	public function __construct() {
		parent::__construct();
		$this->helpCommon = $this->load->common('help');
	}

	public function lists() {
		$post = $this->getPost();
		$data = $this->helpCommon->getHelp($post);
		$data['pagination'] = $this->getPagination($data['pagination']);
		$data['post'] = $post;
		$data['allTypes'] = $this->helpCommon->getType();
		$this->load->view('help/list', $data);
	}

	public function add(){
		$data['allTypes'] = $this->helpCommon->getType();
		$this->load->view('help/add', $data);
	}

	public function write() {
		$get = $this->getGet();
		$id = $get['id'];
		$helpFilesModel = $this->load->model('HelpFile');
		$files = $helpFilesModel->getOne(array('id' => $id));
		$data['files'] = $files; 
		$this->load->view('help/write', $data);
	}

	public function edit() {
		$get = $this->getGet();
		$help = $this->helpCommon->getHelp($get);
		$data['help'] = $help['data'][0];
		$data['allTypes'] = $this->helpCommon->getType();
		$data['files'] = $this->helpCommon->getFile($get['id']);
		$this->load->view('help/edit', $data);
	}

	public function file(){
		$helpFilesModel = $this->load->model('HelpFile');
        $page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $param = array(
            'page' => $page,
            'items' => 10,
            'with' => 'districts',
            'order' => $helpFilesModel->table . '.id',
        );
        $get = $this->getGet();
        $helpFilesList = $helpFilesModel->commonGetList($param);
        $data['pagination'] = $this->getPagination($helpFilesList['pagination']);
        $data['helpFilesList'] = $helpFilesList['data'];
        $this->load->view('help/file', $data);

	}

	public function delete() {
		$this->doAction('help', 'delete', (array)$this->getPost());

	}

	public function save() {
		$this->doAction('help', 'save', (array)$this->getPost());
	}

	public function type() {
		$get = $this->getGet();

		$data['allTypes'] = $this->helpCommon->getType();
		$data['get'] = $get;

		$this->load->view('help/type', $data);
	}

	public function addType() {
		$this->doAction('help', 'addType', (array)$this->getPost());
	}

	public function updateType() {
		$this->doAction('help', 'updateType', (array)$this->getPost());
	}

	public function deleteType() {
		$this->doAction('help', 'deleteType', (array)$this->getGet());
	}

	public function saveFile(){
		$this->doAction('help', 'saveFile', (array)$this->getPost());
	}

	public function deleteFile(){
		$this->doAction('help', 'deleteFile', (array)$this->getPost());
	}


} // END class 
