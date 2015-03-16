<?php

class PropertyController extends Controller
{
	public function actionIndex()
	{
		$params = $_REQUEST;
		$org_id = Yii::app()->user->org_id;
		if(!empty($org_id) && intval($org_id) > 0){
			$params['distributor_id'] = $org_id;
			$params['current'] = isset($params['page']) ? $params['page'] : 1;
			$params['items']   = 20;
			$credit = Credit::api()->listbyxf($params,0);
			$data['credit'] = $credit['body']['data'];
			$data['lists'] = $credit['body'];		
			$data['pages'] = new CPagination($data['lists']['pagination']['count']);
			$data['pages']->pageSize = $params['items'];
		}
		$this->render('index',$data);
		
	}
}