<?php

class PaymentController extends Controller
{
	public function actionIndex()
	{
		$org_id = Yii::app()->user->org_id;
		if(!empty($org_id) && intval($org_id) > 0){
			$param = $_REQUEST;
			$param['agency_id'] = $org_id;
			//日期查询 update 2014.12.09 by ccq
			if(isset($_REQUEST['bill_sd']) && !empty($_REQUEST['bill_sd'])){
				$param['bill_sd'] = $_REQUEST['bill_sd'];
			}
			//日期查询 update 2014.12.09 by ccq
			if(isset($_REQUEST['bill_ed']) && !empty($_REQUEST['bill_ed'])){
				$param['bill_ed'] = $_REQUEST['bill_ed'];
			}
			//供应商名称查询 update 2014.12.09 by ccq
			if(isset($_REQUEST['supply_name']) && !empty($_REQUEST['supply_name'])){
				$param['supply_name'] = $_REQUEST['supply_name'];
			}
			//支付状态查询 update 2014.12.09 by ccq
			if(isset($_REQUEST['pay_state']) && !is_null($_REQUEST['pay_state'])){
				$param['pay_state'] = strval($_REQUEST['pay_state']);
			}
			$param['current'] = isset($param['page']) ? $param['page'] : 1;
			$bill = Bill::api()->lists($param);
			$data['bill'] = $bill['body']['data'];
			$data['pages'] = new CPagination($bill['body']['pagination']['count']);
			$data['pages']->pageSize = 15;
		}
		
		$this->render('index',$data);
	}
}
