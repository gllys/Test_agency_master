<?php

class SpotController extends Controller {

    public $childNav = '/scenic/scenic/';

    public function actionIndex() {
        //景点查询
        //Poi::api()->debug = true ;
        $param = $_GET;
        $param['organization_ids'] = Yii::app()->user->org_id; //机构id
        $param['landscape_ids'] = $_GET['id'];
        $param['current'] = isset($param['page']) ? $param['page'] : 1;
        $param['items'] = 10;
        $data = Poi::api()->lists($param);

        $lists = ApiModel::getLists($data);
        //分页
        $pagination = ApiModel::getPagination($data);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 10; #每页显示的数目
        $this->render('index', compact('lists', 'pages'));
    }

    //新建景点
    public function actionAdd() {
        //add
        if (Yii::app()->request->isPostRequest) {
            $info = $_POST;
            $info['status'] = 0;
            $info['organization_id'] = Yii::app()->user->org_id; //机构id
            $data = Poi::api()->add($info);
            if ($data) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
        $this->renderPartial('add');
    }

    //编辑景点
    public function actionEdit() {
        //获取景点信息
        $_GET['organization_id'] = Yii::app()->user->org_id; //机构id
        $rs = Poi::api()->detail($_GET);
        $data = $rs['body'];

        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            $param['organization_id'] = Yii::app()->user->org_id; //机构id
            $data = Poi::api()->update($param);
            if ($data) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
        $this->renderPartial('edit', array('data' => $data));
    }

    //景点上下架  
    public function actionDownUP() {

        if (Yii::app()->request->isPostRequest) {
            $param['id'] = $_POST['id'];
            $param['organization_id'] = Yii::app()->user->org_id; //机构id
            $param['landscape_id'] = $_POST['landscape_id'];
            $param['status'] = $_POST['status'] ? 0 : 1;

            $data = Poi::api()->update($param);
            //print_r($data);exit;
            if ($data['code'] == 'succ') {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
        $this->renderPartial('downUp');
    }

}
