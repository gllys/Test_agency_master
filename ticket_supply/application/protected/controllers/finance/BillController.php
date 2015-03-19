<?php

class BillController extends Controller
{
	public function actionIndex()
	{
		$org_id = Yii::app()->user->org_id;
		if(!empty($org_id) && intval($org_id) > 0){
			$param = $_REQUEST;
			$param['supply_id'] = $org_id;
			if(isset($param['agency_name']) && trim($param['agency_name']) == '汇联' && strlen(trim($param['agency_name'])) == 6){
				$param['agency_name'] = '-';
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
