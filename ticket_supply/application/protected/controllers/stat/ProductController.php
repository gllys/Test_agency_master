<?php
/**
 * @link 
 * 
 */

use common\huilian\utils\Header;
use common\huilian\utils\GET;
use common\huilian\models\Supply;

/**
 * 分销商统计
 * 本控制器处理分销商相关数据信息，如门票销售、销售额、人次等
 */
class ProductController extends Controller
{
	/**
	 * 列表
	 */
	public function actionIndex()
	{	
// 		Header::utf8();
		// 包括三种'统计详情'、'线状图'、'饼图'；
		$tab = isset($_GET['tab']) ? $_GET['tab'] : 0;
		
		$params = [
			'supply_id' => Yii::app()->user->org_id,
			'year' => GET::required('year', date('Y')),			// 如果没有$_GET['year']，默认为当前年份
			'type' => GET::required('type', 2),					// 如果没有$_GET['type']，默认值为1入园人次（2销售额，3门票销售张数）
			'current' => GET::required('page', 1),				// 分页
				
		];
		$params = GET::requiredAdd(['province_id', 'city_id', 'agency_id', 'product_id', ], $params);
		// 不同的tab，不同的接口参数
		if($tab == 0) {
			$params['show_amount'] = 1; // 请求接口返回所有分销商以月为单位的销售总和
		} else if($tab == 1) {
			$params['x_axis_type'] = GET::required('unit', 0); // 以单位统计，0月，1日。默认0
		} else if($tab == 2) {
			$params['month'] = GET::required('month', 0); 
			$params['show_amount'] = 1; // 请求接口返回所有分销商以月为单位的销售总和
			$params['show_data'] = 1; 	// 是否显示详情数据，1是（默认），0否
			$params['show_pie'] = 1; 	// 是否显示饼图数据，1是(需上面2个参数为1)，0否（默认）
			$params['show_stack'] = 1;	// 是否显示堆叠数据，1是(需上面3个参数为1)，0否（默认）
		}
			
		$res = Salestat::api()->product($params);
		$agencies = ApiModel::getLists($res);
		
		// 不同的tab，获取不同的接口数据
		if($tab == 0) {
			$amounts = empty($res['body']['amount']) ? [] : $res['body']['amount'];
			$pagination = empty($res['body']['pagination']) ? [] : $res['body']['pagination'];
			$pages = new CPagination($pagination['count']);
			$pages->pageSize = 15; #每页显示的数目
		} else if($tab == 1) {
			
		} else if($tab == 2) {
			$amounts = empty($res['body']['amount']) ? [] : $res['body']['amount'];
		}
		
		// view页面通用变量
		$data = [
			'tab' => $tab,
			'year' => $params['year'],
			'type' => $params['type'],
			'productNames' => Supply::productNames(), 
			'agencyNames' => Supply::agencyNames(),
			'agencies' => $agencies,
		];
		
		// 不同的tab, 不同的变量
		if($tab == 0) {
			$data['amounts'] = $amounts;
			$data['pages'] = $pages;
		} else if($tab == 1) {
			$data['unit'] = $params['x_axis_type'];
		} else if($tab == 2) {
			$data['amounts'] = $amounts;
			$data['month'] = $params['month'];
			$data['pie'] = $res['body']['pie'];
			$data['stack'] = $res['body']['stack'];
		}
		
// 		var_dump($params);
// 		var_dump($agencies);
// 		exit;
		$this->render('index', $data);
	}

	
	
}