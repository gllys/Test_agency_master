<?php
/**
 * @link
 */
use common\huilian\utils\Header;
use common\huilian\utils\GET;
use common\huilian\utils\Date;
use common\huilian\models\Supply;
use common\huilian\models\Agency;
use common\huilian\models\Landscape;
use common\huilian\models\API;

/**
 * 分销商销量
 */
class SalesController extends Controller
{
	/**
	 * 列表
	 */
	public function actionIndex()
	{	
		Header::utf8();
		$typeNames = [
			'whole' => '供应商',
			'agency' => '分销商',
			'scenic' => '景区',
		];
		$params = [
			'type' => GET::name('type', 'whole'),
			'start_date' => GET::required('start_date', Date::firstDayOfThisMonth()),
// 			'start_date' => '2013-01-01',
			'end_date' => Date::today(),
			'current' => GET::required('page', 1),				// 分页
			'items' => 20,
		];
		
		// 此处根据不同的name_type类型对供应商、分销商和景区进行名字模糊查询，不同的查询类型，传递给接口的参数不同
		if($name = GET::required('name')) {
			if($params['type'] == 'whole') {
				if($ids = Supply::searchNameForIds($name)) {
					$params['supplier_id'] = $ids;
				}
			} else if($params['type'] == 'agency') {
				if($ids = Agency::searchNameForIds($name)) {
					$params['distributor_id'] = $ids;
				}
			} else {
				if($ids = Landscape::searchNameForIds($name)) {
					$params['landscape_ids'] = $ids;
				}
			}
			// 如果对名称的模糊查询没有结果，则直接渲染view
			if(!$ids) {
				$this->render('index', [
					'type' => $params['type'],
					'typeNames' => $typeNames,
					'startDate' => $params['start_date'],
					'endDate' => $params['end_date'],
					'lists' => [],
					'pages' => null,
				]);
				exit;
			}
		}
		
		// 如果是导出
		if(GET::required('is_export')) {
			$this->actionExport($params);
			exit;
		}
		
		$res = Stat::api()->plateform_list($params);
		$lists = ApiModel::getLists($res);
		$amount = $res['body']['amount'];
		
		/*
		 * 返回数组中的区分供应商、分销商、景区的字段分别为supplier_id, distributor_id,landscape_ids
		 */
		if($params['type'] == 'whole') {
			$lists = API::simultaneous($lists, 'supplier_id', 'Organizations::list', 'id', 'id', 'owner');
		} else if($params['type'] == 'agency') {
			$lists = API::simultaneous($lists, 'distributor_id', 'Organizations::list', 'id', 'id', 'owner');
		} else {
			$lists = API::simultaneous($lists, 'landscape_ids', 'Landscape::lists', 'ids', 'id', 'owner');
		}
		
// 		var_dump($params);
// 		var_dump($res);
// 		var_dump($lists);
// 		exit;

		$pagination = ApiModel::getPagination($res);
		$pages = new CPagination($pagination['count']);
		$pages->pageSize = 20; #每页显示的数目
		
		$this->render('index', [
			'type' => $params['type'],
			'typeNames' => $typeNames,
			'startDate' => $params['start_date'],
			'endDate' => $params['end_date'],
			'lists' => $lists,
			'amount' => $amount,
			'pages' => $pages,
		]);
	}

	/**
	 * 内页
	 * 备注：
	 * - 该页从列表点击过来，带了四个参数
	 * - 参数$type的值['whole', 'agency', 'scenic']分别代表供应商、分销商和景区
	 * - 参数$id的值，代表不同$type所对象的标识，其值来源于供应商的supplier_id，分销商的distributor_id，景区的landscape_ids
	 */
	public function actionView() {
		Header::utf8();
		$typeNames = [
			'whole' => '供应商',
			'agency' => '分销商',
			'scenic' => '景区',
		];
		
		$params = [
			'type' => GET::required('type', 'whole'),
			'start_date' => GET::required('start_date', Date::firstDayOfThisMonth()),
// 			'start_date' => '2013-01-01',
			'end_date' => Date::today(),
		];
		// 不同的$type，请求接口的参数不同
		if($params['type'] == 'whole') {
			$params['supplier_id'] = GET::required('id');
		} else if($params['type'] == 'agency') {
			$params['distributor_id'] = GET::required('id');
		} else {
			$params['landscape_ids'] = GET::required('id');
		}
		// 不同$tab,请求接口的参数不同，如果$tab非空，即为图表数据，图表数据时，是没有分页的，获取该时间段所有的数据
		$tab = GET::required('tab', 0);
		if($tab) {
			$params['items'] = 9999999999999;
		}
		
		$api = API::lists('Stat::plateform_detail', $params);
		
// 		var_dump($api);
// 		exit;
		$this->render('view', [
			'type' => $params['type'],
			'typeNames' => $typeNames,
			'startDate' => $params['start_date'],
			'endDate' => $params['end_date'],
			'lists' => $api['lists'],
			'pages' => $api['pages'],
			'tab' => $tab,
		]);
		
	}
	
	/**
	 * 导出
	 */
	public function actionExport($params) {
		$typeNames = [
			'whole' => '供应商',
			'agency' => '分销商',
			'scenic' => '景区',
		];
		
		$params['items'] = 1000;
		$params['current'] = 1;

		$this->renderPartial('excelTop', [
			'type' => $params['type'],
			'typeNames' => $typeNames,
		]);
		
		$num = 0;
		do {
			$res = Stat::api()->plateform_list($params);
			$params['current']++;

			if(!empty($res['body']['data'])) {
				$lists = $res['body']['data'];
				/*
				 * 返回数组中的区分供应商、分销商、景区的字段分别为supplier_id, distributor_id,landscape_ids
				 */
				if($params['type'] == 'whole') {
					$lists = API::simultaneous($lists, 'supplier_id', 'Organizations::list', 'id', 'id', 'owner');
				} else if($params['type'] == 'agency') {
					$lists = API::simultaneous($lists, 'distributor_id', 'Organizations::list', 'id', 'id', 'owner');
				} else {
					$lists = API::simultaneous($lists, 'landscape_ids', 'Landscape::lists', 'ids', 'id', 'owner');
				}
				
				$this->renderPartial('excelBody', [
					'lists' => $lists,
					'type' => $params['type'],
					'typeNames' => $typeNames,
				]);
				
				// 累计记录总数
				if(!empty($res['body']['pagination']['count'])) {
					$num += $res['body']['pagination']['count'];
				}
			}
		} while(!empty($res['body']['data']));		
		
		$this->renderPartial('excelBottom', [
			'num' => $num,
		]);
		
	}
}