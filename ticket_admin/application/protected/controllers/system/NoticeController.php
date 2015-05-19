<?php
use common\huilian\utils\Header;

/*
 * message
 * created by ccq
 * api:  ticket-api-organization/message
 * */

class NoticeController extends Controller {

	/**
	 * 显示公告列表信息
	 */
	public function actionIndex() {
		$get = $_GET;
		$params = null;
		/* 合并时间参数格式传递给api => start_date - end_date */
		if (isset($get['start_date']) && !empty($get['start_date'])) {
			$params['created_at'] = $get['start_date'];
		} else {
			$params['created_at'] = "0000-01-01";
		}
		$params['created_at'] .= ' - ';
		if (isset($get['end_date']) && !empty($get['end_date'])) {
			$params['created_at'] .= $get['end_date'];
		} else {
			$params['created_at'] .= "9999-12-31";
		}

		if (isset($get['send_source']) && $get['send_source'] !== '') {
			$params['send_source'] = $get['send_source'];
		}
		if (isset($get['is_allow']) && $get['is_allow'] !== '') {
			$params['is_allow'] = $get['is_allow'];
		}
		if (isset($get['receiver_organization_type']) && $get['receiver_organization_type'] !== '') {
			$params['receiver_organization_type'] = $get['receiver_organization_type'];
		}
		/* 过滤已删除 */
		$params['is_del'] = 1;
		$params['current'] = isset($get['page']) ? $get['page'] : 0;
		$params['items'] = 15;
		$orgRs = Message::api()->backendlist($params);
		$body = ApiModel::getData($orgRs);
		$data['datas'] = $body['data'];
		$data['users'] = $this->getUsers($body['data']);

		//分页
		$data['pages'] = new CPagination($body['pagination']['count']);
		$data['pages']->pageSize = $params['items'];

		$data['get'] = $get;
// 		Header::utf8();
// 		var_dump($data);
// 		exit;
		$this->render("index", $data);
	}

	/**
	 * @desc 获取根据公告数据中的所有id获取对应用户数据
	 */
	private function getUsers($datas) {
		// 用于查询的供应商ids
		$supplyIds = array();
		// 用于查询后台用户的数组ids
		$adminIds = array();
		$i=0;
		$length=count($datas);
		foreach ($datas as $val) {
			// 首先确认公告的发送源
			$sendUser = isset($val['send_user']) ? $val['send_user'] : -1; // -1表示错误的发送人类型
			if(isset($val['send_source']) && $val['send_source'] == 1) {
				$supplyIds[] = $sendUser;
			} else {
				$adminIds[] = $sendUser;
			}
			$i++;
		}
		
		// 去除重复id
		$supplyIds = array_unique($supplyIds);
		$adminIds = array_unique($adminIds);

		$retDatas = array();
		// 查询供应商用户列表
		$supplyRs = SupplyUser::api()->lists(array("ids"=>  implode(",", $supplyIds), "fields"=>"id,account,name"));
		$supplyDatas = $supplyRs["data"];
        if(is_array($supplyDatas)){
            foreach ($supplyDatas as $val) {
                $retDatas[$val['id']] = $val;
            }
        }
		
		// 查询后台用户列表
		$criteria = new CDbCriteria();
		$criteria->select = "id, account , name";
		$criteria->addInCondition("id", $adminIds);
		$adminRs = Users::model()->findAll($criteria);
		if($adminRs && is_array($adminRs)) {
			foreach ($adminRs as $val) {
				$retDatas[$val["id"]] = array("id"=>$val["id"], "name"=>$val["name"], "account"=>$val["account"]);
			}
			return $retDatas;
		} else {
			return array();
		}
	}
	
	/**
	 * 查看指定id公告信息
	 */
	public function actionView() {
		$get = $_GET;
		$params = null;
		if (isset($get['id']) && !empty($get['id'])) {
			$params['id'] = $get['id'];
		}
		$rs = Message::api()->backendlist($params);
		$list = ApiModel::getLists($rs);
		$data = current($list);

		/* 获取发送用户信息 */
		$rs = array();
		if (isset($data['send_source']) && $data['send_source'] == 0) {
			// 后台发送，获取后台用户信息
			$criteria = new CDbCriteria();
			$criteria->select = "name";
			$criteria->condition = "id=:send_user";
			$criteria->params['send_user'] = $data['send_user'];
			$userInfo = Users::model()->find($criteria);
			$rs['name'] = $userInfo['name'];
		} else if (isset($data['send_source']) && $data['send_source'] == 1) {
			// 前台供应商发布
			$supplyUserRes = SupplyUser::api()->search(array('id' => $data['send_user']));
			$list = (isset($supplyUserRes['message']) && is_array($supplyUserRes['message'])) ? $supplyUserRes['message'] : array();
			$rs['name'] = isset($list['name']) ? $list['name'] : "";
		}
		$data = array_merge($rs, $data);
		$data['get'] = $get;
		$this->renderPartial('view', $data);
	}

	/**
	 * 增加公告信息
	 */
	public function actionAdd() {
		
		$this->render('add');
	}
	
	/**
	 * 保存公告信息
	 */
	public function actionSave() {
		
		$post  = $_REQUEST;
		if(!empty($post) && is_array($post)) {
			$content = trim($post['content']);
			$content = htmlspecialchars_decode($content);
			$content = preg_replace("/<(.*?)>/","",$content);
			if(empty($content)) {
				$data = array();
				$data['code'] = '1';	// code为1表示公告内容为空
				$data['message'] = '内容不能为空';
				echo json_encode($data);
			} else {
				$params['title'] = $post['title'];
				$params['receiver_organization_type'] = $post['receiver_organization_type'];
				$params['content'] = htmlspecialchars_decode(stripcslashes($post['content']));
				$params['sms_type'] = 0;
				$params['sys_type'] = 0;
				$params['send_source'] = 0;
				$params['organization_name'] = "汇联运营团队";
				$params['send_user'] = Yii::app()->user->uid;
				$params['is_allow'] = 1;
				$params['send_backend'] = Yii::app()->user->uid;
				$params['send_status'] = 1;
				$rs = Message::api()->add($params);
				echo json_encode($rs);
			}
		}

	}
	
	/**
	 * 发布公告
	 * $_POST 
	 *   type为1表示发布公告
	 *		 为2表示驳回公告
	 */
	public function actionPub()
	{
		$post = $_POST;
		$params['id'] =  $post['id'];
		$params['uid'] =  0;
		$params['is_allow'] = $post['type'];		// 发布
		if($post['type'] == 2) {
			$params['remark'] = $post['remark']; 
		}
		$rs = Message::api()->update($params);
		echo json_encode($rs);
	}
	
	/**
	 * @desc 删除公告
	 */
	public function actionDel()
	{
		$get = $_GET;
		$params['id'] = $get['id'];
		$params['uid'] = 0;
		$params['is_del'] = 1;			// 删除
		$rs = Message::api()->update($params);
		echo json_encode($rs);
	}

	/**
	 * 撤回
	 * 本方法撤回公告
	 * 撤回指的是该消息供应和分销将无法查看到，而后台显示为未发布状态。
	 */
	public function actionRevocation() 
	{
		if(Yii::app()->request->isPostRequest) {
			$params = [
				'id' => $_POST['id'],
				'uid' => 0,
				'is_cancel' => 1,
			];
			$rs = Message::api()->update($params);
			empty($rs['code']) ? $this->_end(1, '无法连接接口') : $this->_end($rs['code'] != 'succ', $rs['message']);
		}
		
	}
	
	/**
	 * 预览
	 * 本方法展现POST过来的公告信息
	 * 备注：
	 * - 发布人为固定名称：`汇联运营团队`。
	 * －　若用UBB方式，则采用以下方式：'content' => UbbToHtml::Entry($_POST['content']),
	 */
	public function actionPreview()
	{
		if(Yii::app()->request->isPostRequest) {
// 			var_dump($_POST);
// 			exit;
			$_POST['content'] = htmlspecialchars_decode($_POST['content']);
			$params = [
				'publisher' => '汇联运营团队',
			];
			$this->render('preview', array_merge($params, $_POST));
		}
	}

}
