<?php
/**
 * @link
 */
use common\huilian\utils\Header;
use common\huilian\utils\GET;
use common\huilian\models\API;

/**
 * 验票控制器
 * 本类处理验票记录、撤销核销等
 * 
 */
class VerificationController extends Controller
{
	/**
	 * 验票记录
	 */
	public function actionIndex()
	{	
		Header::utf8();
		$params = [
			'current' => isset($_GET['page']) ? $_GET['page'] : 1,
		];
		$params = GET::requiredAdd(['begin_date', 'end_date', 'order_id', 'supply_name', 'scenic_name', ], $params);
		$res = Verification::api()->record($params);
		$orderNums = $res['body']['order_nums'];
		$totalNums = $res['body']['total_nums'];
		
		$verifications = ApiModel::getLists($res);
		$verifications = API::simultaneous($verifications, 'supplier_id', 'Organizations::list', 'id', 'id', 'organization');
		$verifications = API::simultaneous($verifications, 'landscape_id', 'Landscape::lists', 'ids', 'id', 'landscapes');
		$verifications = API::simultaneous($verifications, 'equipment_code', 'Device::getlist', 'codes', 'code', 'device');
		$verifications = API::simultaneous($verifications, 'poi_id', 'Poi::lists', 'ids', 'id', 'pois');
		
		$pagination = ApiModel::getPagination($res);
		$pages = new CPagination($pagination['count']);
		$pages->pageSize = 15; #每页显示的数目		

// 		var_dump($params);
// 		var_dump($verifications);
// 		exit;
		$this->render('index', [
			'verifications' => $verifications, 
			'pages' => $pages, 
			'orderNums' => $orderNums, 
			'totalNums' => $totalNums,
		]);
	}
	
	/**
	 * 撤销
	 * 备注：有一些票是无法撤消的，如：已撤销、没有可撤销的票等。
	 */
	public function actionCancel() {
		if($_POST) {
			$params = [
				'id' => $_POST['id'],
				'is_force' => 1,
				'user_id' => Yii::app()->user->uid,
				'user_name' => Yii::app()->user->display_name,
				'user_account' => Yii::app()->user->account,
				'cancel_source' => 1,
				'cancel_uid' => Yii::app()->user->uid,
				'cancel_name' => Yii::app()->user->display_name,
				'cancel_account' => Yii::app()->user->account,
			];

			$res = Verification::api()->cancel($params);
			$res ? $this->_end(!ApiModel::isSucc($res), $res['message']) : $this->_end(1, '通信失败');
		}
	}

}