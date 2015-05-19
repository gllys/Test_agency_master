<?php
/**
 * @link
 */
use common\huilian\utils\Header;

/**
 * 用户提醒控制器
 * 系统左侧菜单[消息管理]-[用户提醒]，链接该控制器
 */
class NoticeController extends Controller
{
	/**
	 * 用户提醒列表
	 */
	public function actionIndex()
	{
		$status = empty($_GET['status']) ? 0 : $_GET['status'];
		
		$params = [
			'sms_type' => 2,
			'sys_type' => 7,
			'current' => isset($_GET['page']) ? $_GET['page'] : 1,
		];
		// $status == 1 未读，接口参数read_time为 0；$status == 2已读，接口参数read_time为1。
		if($status) {
			$params['read_time'] = $status == 1 ? 0 : 1;
		}

		$res = Message::api()->backendlist($params);

		$pagination = ApiModel::getPagination($res);
		$pages = new CPagination($pagination['count']);
		$pages->pageSize = 15; #每页显示的数目
		
		// 获取未读的数量
		$unreadParams = [
			'sms_type' => 2,
			'sys_type' => 7,
			'read_time' => 0,
		];
		$unreadRes = Message::api()->backendlist($unreadParams);
		$unreadNum = empty($unreadRes['body']['pagination']['count']) ? 0 : $unreadRes['body']['pagination']['count'];
		
// 		Header::utf8();
// 		$messages = ApiModel::getLists($res);
// 		var_dump($messages);
// 		exit;
		$this->render('index', ['status' => $status, 'messages' => ApiModel::getLists($res), 'pages' => $pages, 'unreadNum' => $unreadNum, ]);
	}
	
	/**
	 * 查看消息
	 * 注意：
	 * - 本处查看的消息，需要更改消息的阅读状态。但是实际显示的却是机构的内容。
	 *   即通过消息的主键获得机构的主键，然后展现机构的内容
	 * - 展现机构的内容通过跳转到相应的页面即可。具体路由如下：
	 *   供应：/org/supply/view/id/546/
	 *   分销：/org/agency/view/id/544/
	 * @param integer $id 消息主键
	 */
	public function actionView($id) {
		Message::api()->update(['id' => $id, 'uid' => Yii::app()->user->uid, 'read_time' => time(), ]);
		$res= Message::api()->detail(['id' => $id, ]);
		$message = ApiModel::getData($res);

		$res = Organizations::api()->show(['id' => $message['send_organization'], ]);
		$organization = ApiModel::getData($res);

		$url = '/org/' .$organization['type']. '/view/id/' .$organization['id']. '/';
        $control=Yii::app()->runController($url);
//		$this->redirect($url);
// 		Header::utf8();
// 		var_dump($organization);
// 		exit;
	}
	
	
	/**
	 * 删除消息
	 */
	public function actionDelete() {
		$params['id'] = $_POST['id'];
		$params['uid'] = Yii::app()->user->uid;
		$params['is_del'] = 1;
		$result = Message::api()->update($params);
		if($result['code'] == 'succ'){
			$this->_end(0,'删除成功');
		}else{
			$this->_end(1,$result['message']);
		}
	}
	
	/**
	 * 批量删除
	 */
	public function actionUpdateAll() {
		$param['id'] = trim(str_replace("undefined","",str_replace("on","",$_POST['ids'])),",");
		if(empty($param['id'])){
			$this->_end(1,'请勾选需要删除的消息');
			exit;
		}
	
		$param['uid'] = Yii::app()->user->uid;
		if($_POST['type'] == 'del'){
			$param['is_del'] = 1;
		}
	
		//print_r($param);die;
		$result = Message::api()->updateBatch($param);
		if ($result['code'] == 'succ') {
			$this->_end(0, '删除成功');
		} else {
			$this->_end(1, $result['message']);
		}
	}

	/*
     *批量设置已读
     * xujuan
     */
	public function actionUpdateBatch()
	{
		$param['id'] = trim(str_replace("undefined","",str_replace("on","",$_POST['ids'])),",");
		if(empty($param['id'])){
			$this->_end(1,'请勾选需要设置已读的消息');
			exit;
		}
		$param['uid'] = Yii::app()->user->uid;
		$param['read_time'] =time(); //必传参数

		$result = Message::api()->updateBatch($param);
		if ($result['code'] == 'succ') {
			$this->_end(0, '设置已读成功');
		} else {
			$this->_end(1, $result['message']);
		}
	}
	
}