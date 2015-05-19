<?php

class RefundController extends Controller {

    public function actionIndex() {
        $org_id = YII::app()->user->org_id;
        $param = $_GET;
        $param['current'] = isset($param['page']) ? $param['page'] : 0;
        $param['supplier_id'] = $org_id;
        if(isset($param['begin_date']) || isset($param['end_date'])){
            if(empty($param['begin_date'])){
                $param['begin_date'] = '2000-01-01';
            }
            if(empty($param['end_date'])){
                $param['end_date'] = date('Y-m-d');
            }
            $param['created_at'] = $param['begin_date'].' - '.$param['end_date'];
            unset($param['begin_date']);
            unset($param['end_date']);
        }
        $lists = Refund::api()->apply_list($param);
        $list = ApiModel::getLists($lists);
        //批量查操作者,机构名
        $opid = array();//操作者id数组
        $orgid = array();//机构id数组
        $user = array();//操作者id=>操作者姓名关联数组
        $org = array();//机构id=>机构名关联数组
        foreach($list as $one){
            if(!in_array($one['op_id'], $opid)){
                $opid[] = $one['op_id'];
            }
            if(!in_array($one['distributor_id'], $orgid)){
                $orgid[] = $one['distributor_id'];
            }
        }
        if(count($opid) > 0){
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $opid);
            $criteria->select = array("id","account");
            $result = Users::model()->findAll($criteria);
            if(is_array($result)) {
                foreach ($result as $val) {
                    $user[$val['id']] = $val['account'];
                }
            }
        }
        if(count($orgid) > 0){
            $idstr = implode(',', $orgid);
            $orginfo = Organizations::api()->list(array('id'=>$idstr,'fields'=>'id,name'),true);
            if ($orginfo['code'] == 'succ' && isset($orginfo['body']['data'])) {
                foreach ($orginfo['body']['data'] as $val) {
                    $org[$val['id']] = $val['name'];
                }
            }
        }
        // 获取分销商 id=>name,用于搜索条件
        $rs = Credit::api()->lists(array('supplier_id' => $org_id, 'fields' => 'distributor_id,distributor_name', 'items' => 1000));
        $distributorDatas = ApiModel::getLists($rs);
        $distributors_labels = array();
        foreach ($distributorDatas as $v) {
            $distributors_labels[$v['distributor_id']] = $v['distributor_name'];
        }
        //分页
        $pagination = ApiModel::getPagination($lists);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目
        //print_r($list);
        $this->render('index', compact('list', 'user', 'org', 'distributors_labels', 'pages'));
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
            // 增加预定数量和游客姓名
            $info['ordernum'] = '';
            $info['ownername'] = '';
            if(isset($info['order_id'])){
                $data = Order::api()->detail(array('id' => $info['order_id']));
                if ($data['code'] == 'succ') {
                    $info['ordernum'] = $data['body']['nums'];
                    $info['ownername'] = $data['body']['owner_name'];
                }
            }
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
      //  echo 123;die;
        //驳回  
        if (isset($_GET['reject']) && $_GET['reject'] == 1) {
            $field2['id'] = $_GET['id'];
            $field2['allow_status'] = 3;
            $field2['user_id'] = yii::app()->user->uid;
            $field2['reject_reason'] = $_GET['reject_reason'];
            $data = Refund::api()->check_refund($field2);
           // echo "<pre>";print_r($data);die("</pre>");
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, "请填写驳回理由.");
            }
        }
    }

}
