<?php

class RenwuController extends Controller
{
	public function actionView() {
		$this->actionIndex();
	}
	public function actionIndex() {
		$params = $_REQUEST;
		$data['status_labels'] = array('unpaid'=>'未确认','paid' => '已确认','canceled' => '已取消','billed' => '已结款');
		$data['status_class'] = array('unpaid'=>'danger','canceled' => 'warning','paid' => 'success','billed' => 'error');
		$data['status'] = array_keys($data['status_labels']);
		if (!empty($params)) {
            if (isset($params['status'])) {
                foreach (Order::$status as $status_type => $status_lists) {
                    foreach ($status_lists as $status_item => $status_value) {
                        if ($params['status'] == $status_item) {
                            $params[$status_item] = $status_value;
                            break 2;
                        }
                    }
                    unset($status_item, $status_value);
                }
                unset($status_type, $status_lists, $params['status']);
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
