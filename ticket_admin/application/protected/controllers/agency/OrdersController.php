<?php
/**
 * @link
 */
use common\huilian\models\Pay;
use common\huilian\utils\Header;
use common\huilian\utils\GET;

/**
 * 分销系统-订单管理
 */
class OrdersController extends Controller {

	public function actionView($is_export = false) {
		$this->actionIndex($is_export);
	}
	
	/**
	 * 列表
	 */
	public function actionIndex($is_export = false) {
		
		$params = PublicFunHelper::filter($_GET);
		
		$data['menus'] = array(
			'all' => array(
				'title' => '全部订单'
			),
			'verify' => array(
				'title' => '审核订单'
			),
			'paid' => array(
				'title' => '未使用订单'
			),
			'refund' => array(
				'title' => '退款订单'
			),
			'bill' => array(
				'title' => '已使用订单'
			)
		);
		
		$data['status_labels'] = array(
				"auditing" => "待确认",
				"reject" => "已驳回",
				'unpaid' => '未支付',
				'canceled' => '已取消',
				'paid' => '已付款',
				'finish' => '已完成',
				'billed' => '已结款'
		);
		
		//状态配置
		$data['status_labels'] = array("unaudited"=>"待确认","reject"=>"已驳回",'unpaid'=>'未支付','cancel' => '已取消','paid' => '已支付','finish' => '已完成','billed' => '已结款');
		$data['status_class'] = array("unaudited"=>"info","reject"=>"danger",'unpaid' => 'danger', 'cancel' => 'warning', 'paid' => 'success', 'finish' => 'info', 'billed' => 'error');
		$data['status'] = array_keys($data['status_labels']);
		
		// 获取支付类型配置
		$data['payTypes'] = Pay::types();
		
		// 获取所有游玩类型配置
		$data['timeTypes'] = array(
			'预订日期',
			'游玩日期',
			'入园日期'
		);
		$data['time_type'] = isset($params['time_type']) ? $params['time_type'] : 0;
		
		$data['get'] = $params;
		
		//得到查询数据
		if(!empty($params['menu'])&&$params['menu']=='verify'){
			$params['statuses'] = 'unaudited,reject' ;
		}elseif(!empty($params['menu'])&&$params['menu']=='paid'){
			$params['status'] = 'unused' ;
		}elseif (!empty($params['menu']) && $params['menu'] == 'bill') {
			$params['status'] = 'used' ;
		}
		
		$params['current'] = isset($params['page']) ? $params['page'] : 1;
		
		$params['items'] = $is_export == true ? 1000 : 20;
		
		$params['type'] = 0;
		//$params['supplier_id'] = $org_id;
		$params['time_type'] = isset($params['time_type']) ? $params['time_type'] : 0;
		$params = GET::requiredAdd(['source', 'agency_name', ], $params);
		$data = $this->getApiLists($params, $is_export, $data);
		
		$lans = $data['lists']['data'];
		
		// 获取景区id和名字的数组
		$lanIds = PublicFunHelper::arrayIds($lans, 'landscape_ids');
                $landscapeDatas =  Landscape::api()->getSimpleByIds($lanIds);
                $landscapes = array();
		foreach( $landscapeDatas as $v ) {
			$landscapes[$v['id']] = $v['name'];
		}
		// print_r($landscapes);
		$data['landscape_lists'] = $landscapes;
		
		if($data['lists']["result"]['code'] == 'succ') {
			// $data['lists'] = $result['body'];
			if($is_export == false) {
				$data['pages'] = new CPagination($data['lists']['pagination']['count']);
				$data['pages']->pageSize = $params['items'];
			}
		}
// 		Header::utf8();
// 		var_dump($data['lists']);
// 		exit;
		$this->render('index', $data);
	}
	
	private function getApiLists($params,$is_export,$data)
	{
		$d = array();
		$pagination =null;
		$result = null;
		$num = 0;
		$params['show_verify_items'] = 1;
		if($is_export)
		{
			$this->renderPartial("excelTop",$data);
		}
	
		do{
			if($result)
			{
				unset($result);
			}
	
			if (!empty($params['menu']) && $params['menu'] == 'refund') {
				$result = Refund::api()->order($params);
			} else{
				$result = Order::api()->lists($params);
			}
			$params["current"] = ((int)trim($params["current"]))+1;
			$params["page"] = $params["current"];
			if($result['code'] == 'succ')
			{
				$pagination = $result['body']['pagination'];
				$data['lists'] = array("data"=>$result['body']["data"],"statics"=>$result['body']["statics"],"pagination"=>$pagination,"result"=>$result);
				if($is_export)
				{
					$this->renderPartial("excelBody",$data);
				}
				$num += count($data['lists']["data"]);
			}
		}while($params["current"]<1000 && $is_export==true && $result['code'] == 'succ' && $pagination['current']<$pagination['total']);
		if($is_export==true)
		{
			$data["num"] = $num;
			$this->renderPartial("excelBottom",$data);
			exit;
		}
		return $data;
	}
	
	

	public function actionDetail() {
		// 查看是否是景区身份
		$data['status_labels'] = array(
			"unaudited" => "待确认",
			"reject" => "已驳回",
			'unpaid' => '未支付',
			'cancel' => '已取消',
			'paid' => '已付款',
			'finish' => '已完成',
			'billed' => '已结款'
		);
		
		$data['paid_type'] = array(
			'cash' => '现金',
			'offline' => '线下',
			'credit' => '信用支付',
			'advance' => '储值支付',
			'union' => '平台支付',
			'alipay' => '支付宝',
			'kuaiqian' => '快钱'
		);
		$detail = Order::api()->detail(array(
			'id' => $_GET['id'],
			'show_order_items' => 1
		));
		if($detail['code'] == 'succ') {
			$data['detail'] = $detail['body'];
			$data['ticket'] = $detail['body']['order_items'];
            
			// 备注内容ubb to html
			if(isset($data['detail']['remark'])) {
				$data['detail']['remark'] = UbbToHtml::Entry($data['detail']['remark'], time());
			}
		}
        
        $infos = Order::api()->infos(array('id' => $_GET['id'], 'type' => 1), 0);
        if($infos['code'] == 'succ') { $data['infos'] = $infos['body']; }
        
		// print_r($data['detail']);
		$this->render('detail', $data);
	}
	
	/*
	 * 重发短信
	 * 03-13
	 * xujuan
	 */
	public function actionAgainSms() {
		if(Yii::app()->request->isPostRequest) {
			$rs = Order::api()->lists(array(
				'items' => 1,
				'ids' => $_POST['id'],
				'fields' => 'id,name,nums,used_nums,use_day,refunding_nums,refunded_nums,distributor_id,distributor_name,owner_mobile,owner_name,send_sms_nums,landscape_ids,supplier_id'
			), 0);
			
			if($rs['code'] == 'succ') {
				$orderInfo = $rs['body']['data'][0];
				$orderInfo['nums'] = $orderInfo['nums'] - $orderInfo['used_nums'] - $orderInfo['refunding_nums'] - $orderInfo['refunded_nums'];
				$sms = new SMS();
				$orderInfo['host'] ='http://www.piaotai.com';
				$content = $sms->_getCreateOrderContent($orderInfo);
				$result = $sms->sendSMS($_POST['mobile'], $content, 1, $orderInfo['id']);
				
				if($result) {
					echo json_encode(array(
						'errors' => '短信发送成功！！！'
					)
					// 'errors' => $result
					);
					exit();
				}
			}
			echo json_encode(array(
				'errors' => '短信发送失败！'
			));
		}
	}
}
