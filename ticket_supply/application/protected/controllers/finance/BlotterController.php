<?php

class BlotterController extends Controller {

    public function actionView() {
        $this->actionIndex();
    }

    public function actionIndex() {
        $params = $_REQUEST;
        $data['status_labels'] = array( '3' => '充值', '4' => '提现', '5' => '应收账款');
        $data['status_class'] = array('1' => 'success', '2' => 'danger', '3' => 'warnning', '4' => 'info','5' => 'info');
        //$data['mode_type'] = array('cash' => '现金','offline' => '线下','credit'=>'信用支付','advance' =>'储值支付','union'=>'平台支付','alipay'=>'支付宝','kuaiqian'=>'快钱');
        $data['mode_type'] = array('union' => '平台支付', 'credit' => '信用支付', 'advance' => '储值支付', 'kuaiqian' => '快钱','alipay'=>'支付宝',);
        $data['type'] = array_keys($data['status_labels']);
        $data['mode'] = array_keys($data['mode_type']);
        if (!empty($params)) {
            if (isset($params['type']) && !in_array($params['type'], $data['type'])) {
                unset($params['type']);
//                $params['type'] = '3,4,5';
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
            $params['items'] = $params['is_export'] ==true?1000:20;
            if (!isset($params['type'])) {
                $params['type'] = '3,4,5';
            }

            $lists = $this->getApiLists($params,$params['is_export'],$data);  //导出数据处理
            if ($lists['lists']['result']['code'] == 'succ') {
                $data['lists'] = $lists['lists'];
                if($params['is_export'] != true) {
                    $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                    $data['pages']->pageSize = $params['items'];
                }
            }
            /*$result = Transflow::api()->list($params);
            if ($result['code'] == 'succ') {
                $data['lists'] = $result['body'];
                $data['pages'] = new CPagination($data['lists']['pagination']['count']);
                $data['pages']->pageSize = $params['items'];
            }*/
        }
        $this->render('index', $data);
    }


    private function getApiLists($params,$is_export,$data)
    {
        $pagination =null;
        $result = null;
        if($is_export==true)
        {
            $this->renderPartial("excelTop",$data);
        }
        do{

            $result = Transflow::api()->list($params);
            $params["current"] = ((int)trim($params["current"]))+1;
            $params["page"] = $params["current"];
            if($result['code'] == 'succ') {
                $pagination = $result['body']['pagination'];
                $data['lists'] = array("data"=>$result['body']["data"],"pagination"=>$pagination,"result"=>$result);

                if($is_export ==true)
                {
                    $this->renderPartial("excelBody",$data);
                }
            }

        }while($params["current"]<1000 && $is_export==true && $result['code'] == 'succ' && $pagination['current']<$pagination['total']);
        if($is_export==true)
        {
            $this->renderPartial("excelBottom",$data);
            exit;
        }
        return $data;
    }

}
