<?php

class RenwuController extends Controller
{
	public function actionView() {
		$this->actionIndex();
	}
	public function actionIndex() {
		$params = $_REQUEST;
		$data['status_labels'] = array('unpaid'=>'未确认','paid' => '已确认','cancel' => '已取消','finish' => '已结束','billed' => '已结款');
		$data['status_class'] = array('unpaid'=>'danger','cancel' => 'warning','paid' => 'success','finish' => 'info','billed' => 'error');
		$data['status'] = array_keys($data['status_labels']);
		if (!empty($params)) {
			if (isset($params['status']) && !in_array($params['status'], $data['status'])) {
				unset($params['status']);
			}
		}
		$data['get']       = $params;
		$params['supplier_id'] = Yii::app()->user->org_id;
		$params['type'] = 1;
		$params['current'] = isset($params['page']) ? $params['page'] : 1;
		$params['items']   = 20;
		$result            = Order::api()->lists($params, 0);
		if ($result['code'] == 'succ') {
			$data['lists'] = $result['body'];
			$data['pages'] = new CPagination($data['lists']['pagination']['count']);
			$data['pages']->pageSize = $params['items'];
		}
		$this->render('index', $data);
	}

}