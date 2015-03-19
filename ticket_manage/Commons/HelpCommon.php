<?php

/**
 * 帮助文档
 *
 * 2014-07-18
 *
 * @package commons
 * @author  grg
 */
class HelpCommon extends BaseCommon
{
	protected $_code = array('-1' => '{"errors":{"msg":["post data is null"]}}', '-2' => '{"errors":{"msg":["删除消息失败"]}}', '-3' => '{"errors":{"msg":["删除消息失败，缺少必要参数"]}}', '-4' => '{"errors":{"msg":["消息内容不能为空"]}}', '-5' => '{"errors":{"msg":["请选择要发送的机构"]}}', '-6' => '{"errors":{"msg":["发送消息失败"]}}',

		'-21' => '{"errors":{"msg":["null post"]}}', '-22' => '{"errors":{"msg":["缺少必要的参数"]}}', '-23' => '{"errors":{"msg":["错误的状态"]}}', '-24' => '{"errors":{"msg":["不存在的公告信息"]}}', '-25' => '{"errors":{"msg":["公告已经审核，无需重复操作"]}}', '-26' => '{"errors":{"msg":["公告已经拒绝，无需重复操作"]}}', '-27' => '{"errors":{"msg":["保存至数据库失败"]}}',);

	public function getHelp($get = array()) {
		$helpModel = $this->load->model('help');
		$helpTypeModel = $this->load->model('helpType');

		$param = array();
		$param['filter'][$helpModel->table.'.deleted_at'] = null;
		if ($get['id']) {
			$param['filter'][$helpModel->table.'.id'] = intval($get['id']);
		}
		if ($get['name']) {
			$param['filter'][$helpModel->table.'.name|like'] = trim($get['name']);
		}

		//类别搜索
		if ($get['type_id']) {
			$param['filter']['type_id'] = trim($get['type_id']);
		}

		if (!$get['id']) {
			$param['page'] = $this->getGet('p') ? $this->getGet('p') : ($get['p'] ? $get['p'] : 1);
			$param['items'] = 10;
		}

		$param['join'] = array(
			array('left_join' => $helpTypeModel->table.' ht on ht.id='.$helpModel->table.'.type_id AND ht.deleted_at is null'),
		);

		$param['fields']  = $helpModel->table.'.*, ht.name as type_name';
		$param['order'] = $helpModel->table.'.updated_at desc';
		$data = $helpModel->commonGetList($param);

		return $data;
	}

	//文档保存
	public function save($post){
		$HelpModel = $this->load->model('Help');
		$data = array(
			'type_id' => $post['type_id'],
			'name'    => $post['name'],
			'info'    => $post['info'],
			);
		if($post['id']){
			$result = $HelpModel->update($data, array('id' => intval($post['id'])));
		}else{
			$result = $HelpModel->add($data);
		}

		if($result){
			return json_encode(array('success' => 'success'));
		}else{
			return $this->_getUserError(-27);
		}
	}

	public function delete($post) {
		if ($post) {
			$HelpModel = $this->load->model('Help');
			$HelpModel->update(array('deleted_at' => date('Y-m-d H:i:s', time())), array('id' => intval($post['id'])));
			return json_encode(array('succ' => true));
		} else {
			return $this->_getUserError(-1);
		}
	}

	/**
	 * 添加类别
	 *
	 *     类别字段         字段类型                     含义
	 *     name                 varchar(255)                 名称
	 * @param array $post
	 * @return json
	 */
	public function addType($post) {
		if ($post) {
			$HelpTypeModel = $this->load->model('HelpType');

			//组织数据
			$data['name'] = $post['name'];
			$data['type'] = $post['type'];
			$data['created_at'] = date('Y-m-d H:i:s', time());

			$HelpTypeModel->add($data);
			//return json_encode(array('succ'=>true));
			redirect('help_type.html', json_encode(array('succ' => true)), 1);

		} else {
			return $this->_getUserError(-1);
		}
	}

	/**
	 * 更新类别
	 * @param array $post 类别数据
	 * @return json
	 */
	public function updateType($post) {
		if ($post) {
			$postData = array('name' => $post['name'], 'type' => $post['type'], 'updated_at' => date('Y-m-d H;i:s', time()));
			$HelpTypeModel = $this->load->model('HelpType');
			$HelpTypeModel->update($postData, array('id' => intval($post['id'])));
			return json_encode(array('succ' => true));
		} else {
			return $this->_getUserError(-1);
		}
	}

	/**
	 * 删除类别  逻辑删除
	 * @param array $post 机构数据
	 * @return json
	 */
	public function deleteType($post) {
		if ($post) {
			$HelpTypeModel = $this->load->model('HelpType');
			$HelpTypeModel->update(array('deleted_at' => date('Y-m-d H:i:s', time())), array('id' => intval($post['id'])));
			return json_encode(array('succ' => true));
		} else {
			return $this->_getUserError(-1);
		}
	}

	/**
	 * 获取类型
	 *
	 * @return array $data
	 * @author grg
	 **/
	public function getType() {
		$HelpTypeModel = $this->load->model('HelpType');
		return $HelpTypeModel->getHelpType();
	}

	public function getFile($get) {
		$HelpFileModel    = $this->load->model('HelpFile');
		$AttachmentsModel = $this->load->model('Attachments');
		$HelpFile         = $HelpFileModel->getOne('help_id='.$get);
		if($HelpFile['file_id']){
			$Attachments      = $AttachmentsModel->getOne('id='.$HelpFile['file_id']);
			$data = array(
				'url'  => $Attachments['url'],
				'name' => $HelpFile['name'],
				'desc' => $HelpFile['desc']
				);
			return $data;
		}else{
			return false;
		}				
	}

	//保存文件
	public function saveFile($post){
		$HelpFileModel = $this->load->model('HelpFile');
		$file = array(
				'file_id' => $post['file_id'],
				'name'    => $post['file_name'],
				'desc'	  => $post['desc']
				);
		//$fileResult = $HelpFileModel->add($file);
		if($post['id']){
			$fileResult = $HelpFileModel->update($file, array('id' => intval($post['id'])));
		}else{
			$fileResult = $HelpFileModel->add($file);
		}

		if($fileResult){
			return json_encode(array('success' => 'success'));
		}else{
			return $this->_getUserError(-27);
		}
	}

	//删除文件
	public function deleteFile($get){
		$HelpFileModel    = $this->load->model('HelpFile');
		if($get){
			$id = $get['id'];
	 		$HelpFileModel->query("DELETE FROM ".$HelpFileModel->table." WHERE id = $id");	
	 		return json_encode(array('succ' => true));		
		}else{
			return $this->_getUserError(-1);
		}

	}

}


	
