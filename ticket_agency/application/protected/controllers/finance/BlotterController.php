<?php

class BlotterController extends Controller
{
	public function actionView() {
        $this->actionIndex();
    }


	public function actionIndex()
	{
		$params = $_REQUEST;
		$data['status_labels'] = array('1' => '支付', '2' => '退款', '3' => '充值', '4' => '提现');
        $data['status_class'] = array('1' => 'success', '2' => 'danger', '3' => 'warnning', '4' => 'info');
        //$data['mode_type'] = array('cash' => '现金','offline' => '线下','credit'=>'信用支付','advance' =>'储值支付','union'=>'平台支付','alipay'=>'支付宝','kuaiqian'=>'快钱');
        $data['mode_type'] = array('union'=>'平台支付','credit'=>'信用支付','advance' =>'储值支付','kuaiqian'=>'快钱','union'=>'平台支付','alipay'=>'支付宝',);
		$data['type'] = array_keys($data['status_labels']);
		$data['mode'] = array_keys($data['mode_type']);
        if (!empty($params)) {
            if (isset($params['type']) && !in_array($params['type'], $data['type'])) {
                unset($params['type']);
            }
            if (isset($params['mode']) && !in_array($params['mode'], $data['mode'])) {
            	unset($params['mode']);
            }
            if(empty($params['id'])){
            	unset($params['id']);
            }
            if(isset($params['time']) && empty($params['time'][0]) && empty($params['time'][1])){
        		unset($params['time']);
        	}elseif(!empty($params['time'][0]) && !empty($params['time'][1])){
        		$params['time'] = implode(' - ', $params['time']);
        	}
        }
        $data['get'] = $params;
        $org_id = Yii::app()->user->org_id;
        if (intval($org_id) > 0) {
            $params['agency_id'] = $org_id;
            $params['current'] = isset($params['page']) ? $params['page'] : 1;
            $params['items'] = 20;
            if (!isset($params['type'])) {
                $params['type'] = '1,2,3,4';
            }
            $result = Transflow::api()->list($params);
            if ($result['code'] == 'succ') {
                $data['lists'] = $result['body'];
                $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                $data['pages']->pageSize = $params['items'];
            }
        }
        $this->render('index', $data);
	}
}