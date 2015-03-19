<?php

class BlotterController extends Controller {

    public function actionView() {
        $this->actionIndex();
    }

    public function actionIndex() {
        $params = $_REQUEST;
        $data['status_labels'] = array( '3' => '充值', '4' => '提现', '5' => '应收账款');
        $data['status_class'] = array('1' => 'success', '2' => 'danger', '3' => 'warnning', '4' => 'info');
        //$data['mode_type'] = array('cash' => '现金','offline' => '线下','credit'=>'信用支付','advance' =>'储值支付','union'=>'平台支付','alipay'=>'支付宝','kuaiqian'=>'快钱');
        $data['mode_type'] = array('union' => '平台支付', 'credit' => '信用支付', 'advance' => '储值支付', 'kuaiqian' => '快钱');
        $data['type'] = array_keys($data['status_labels']);
        $data['mode'] = array_keys($data['mode_type']);
        if (!empty($params)) {
            if (isset($params['type']) && !in_array($params['type'], $data['type'])) {
                unset($params['type']);
                $params['type'] = '3,4,5';
            }
            if (isset($params['mode']) && !in_array($params['mode'], $data['mode'])) {
                unset($params['mode']);
            }
            if (empty($params['id'])) {
                unset($params['id']);
            }
            if (isset($params['time'])) {
                $params['time'] = $params['time'][0] . ' - ' . $params['time'][1];
            }
        }
        $data['get'] = $params;
        $org_id = Yii::app()->user->org_id;
        if (intval($org_id) > 0) {
            $params['supplier_id'] = $org_id;
            $params['current'] = isset($params['page']) ? $params['page'] : 1;
            $params['items'] = 20;
            if (!isset($params['type'])) {
                $params['type'] = '3,4,5';
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
