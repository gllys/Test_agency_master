<?php
/**
 * @link
 */
use common\huilian\utils\Header;
use common\huilian\utils\GET;
use common\huilian\models\Supply;

/**
 * 设备管理控制器
 */
class DeviceController extends Controller
{
	/**
	 * 列表页
	 */
	public function actionIndex()
	{
		Header::utf8();
		$params = [
			'current' => isset($_GET['page']) ? $_GET['page'] : 1,
			'organization_id' => Yii::app()->user->org_id,
		];
		$params = GET::requiredAdd(['s_time', 'e_time', 'is_bind', 'is_fix', 'scenic_name', ], $params);
        $params['s_time'] = empty($params['s_time'])? 0 : strtotime($params['s_time']);
        $params['e_time'] = empty($params['e_time'])? 0 : strtotime($params['e_time']) + 86399;
		$res = Device::api()->lists($params);
		$devices = ApiModel::getLists($res);

// 		var_dump($params);
// 		var_dump($devices);
// 		exit;
		
		$pagination = ApiModel::getPagination($res);
		$pages = new CPagination($pagination['count']);
		$pages->pageSize = 15; #每页显示的数目
		
		$this->render('index', [
			'devices' => $devices,
			'pages' => $pages,
			'landscapeNames' => Supply::landscapeNames(),
		]);
	}

	/**
	 * 绑定
	 */
	public function actionBind() {
		if($_POST) {
			$params = [
				'id' => $_POST['id'],
				'scene' => $_POST['scene'],
				'type' => $_POST['type'],
				'scene_id' => $_POST['scene_id'],
				'landscape_id' => $_POST['landscape_id'],
			];
			$res = Device::api()->binding($params);
			$res ? $this->_end(!ApiModel::isSucc($res), $res['message']) : $this->_end(1, '通信失败');
		}
	}	
	
	/**
	 * 解绑
	 */
	public function actionUnbind() {
		if($_POST) {
			$params = [
				'id' => $_POST['id'],
				'scene_id' => 0,
				'scene' => 0,
			];
			Device::api()->binding($params);
		}
	}
	
}