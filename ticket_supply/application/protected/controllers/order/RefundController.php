<?php

class RefundController extends Controller {

    public function actionIndex() {
        $param = array();
        $param = $_GET;
        $param['current'] = isset($param['page']) ? $param['page'] : 0;
        $param['supplier_id'] = YII::app()->user->org_id;
        $lists = Refund::api()->apply_list($param);
        $list = ApiModel::getLists($lists);
        //分页
        $pagination = ApiModel::getPagination($lists);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目
        //print_r($list);
        $this->render('index', compact('list', 'pages'));
    }

    //单个订单信息
    public function actionPoint() {
        //订单信息
        if (isset($_GET) && !empty($_GET['id'])) {
            $id = $_GET['id'];
            $param = $_GET;
            $infos = Refund::api()->apply_list($param);
            $infoid = ApiModel::getLists($infos);
            $info = $infoid[$_GET['id']];
            //print_r($id);
        }
        $this->renderPartial('point', compact('info', 'id'));
    }

    //单个订单信息
    public function actionPoint1() {
        //同意
        if (isset($_GET['idnum']) && $_GET['idnum'] == 1) {
            $field1 = array();
            $field1['id'] = $_GET['id'];
            $field1['allow_status'] = 1;
            $field1['user_id'] = yii::app()->user->uid;
            $data = Refund::api()->check_refund($field1);
           
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
        
        //驳回  
        if (isset($_GET['reject']) && $_GET['reject'] == 1) {
            $field2['id'] = $_GET['id'];
            $field2['allow_status'] = 3;
            $field2['user_id'] = yii::app()->user->uid;
            $field2['reject_reason'] = $_GET['reject_reason'];
            $data = Refund::api()->check_refund($field2);
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }

}
