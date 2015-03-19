<?php

class GoodsController extends Controller {

    public function actionIndex() {
        //绑定供应商的所有景区
        $param = array();
        $param['organization_id'] = Yii::app()->user->org_id;
        $param['items'] = 100000;
        $data = Landorg::api()->lists($param, true);
        $supplyLans = ApiModel::getLists($data);


        //得到可编辑景区ID
        $param = array();
        $param['organization_id'] = Yii::app()->user->org_id;
        $param['is_manage'] = 1;
        $param['items'] = 100000;
        $data = Landscape::api()->lists($param, true);
        $lanLists = ApiModel::getLists($data);
        $lanIds = PublicFunHelper::arrayKey($lanLists, 'id');


        //得到门票列表
        $param = array();
        $param['state'] = 1;
        //$param['organization_id'] = YII::app()->user->org_id;
        $param['p'] = isset($_GET['page']) ? $_GET['page'] : 1;
        $param['items'] = 15;
        $param['show_group'] = 1;

        if (!empty($_GET['scenic_id'])) {
            $param['scenic_id'] = $_GET['scenic_id'];
        } else {
            $p = array();
            $p['organization_id'] = Yii::app()->user->org_id;
            $p['items'] = 100000;
            $data = Landorg::api()->lists($p, true);
            $_supplyLans = ApiModel::getLists($data);
            $_lanIds = PublicFunHelper::arrayKey($_supplyLans, 'landscape_id');
            $_lanIds = array_diff($_lanIds, $lanIds);
            if ($_lanIds) {
                $param['scenic_id'] = implode(',', $_lanIds);
            } else {
                $param['organization_id'] = Yii::app()->user->org_id;
            }
        }
        //Tickettemplatebase::api()->debug = true;
        $datas = Tickettemplatebase::api()->lists($param);
        $lists = ApiModel::getLists($datas);
       

        //分页
        $pagination = ApiModel::getPagination($datas);

        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目
        $this->render('index', compact('lists', 'pages', 'lanIds', 'supplyLans'));
    }
    
    /**
     * 我的门票
     * 02-10
     * xj
     */
    public function actionIndex2() {
        //绑定供应商的所有景区
        $param = array();
        $param['organization_id'] = Yii::app()->user->org_id;
        $param['items'] = 100000;
        $data = Landorg::api()->lists($param, true);
        $supplyLans = ApiModel::getLists($data);

        $acction = Yii::app()->user->lan_id;

        //echo $acction;die;

        //得到可编辑景区ID
        $param = array();
        $param['organization_id'] = Yii::app()->user->org_id;
        $param['is_manage'] = 1;
        $param['items'] = 100000;
        $data = Landscape::api()->lists($param, true);
        $lanLists = ApiModel::getLists($data);
        $lanIds = PublicFunHelper::arrayKey($lanLists, 'id');

        //得到门票列表
        $param = array();
        $param['state'] = 1;
        //$param['organization_id'] = YII::app()->user->org_id;
        $param['p'] = isset($_GET['page']) ? $_GET['page'] : 1;
        $param['items'] = 15;
        $param['show_group'] = 1;

        if (!empty($_GET['scenic_id'])) {
            $param['scenic_id'] = $_GET['scenic_id'];
        } else {
            if ($_lanIds=$lanIds) {
                $param['scenic_id'] = implode(',', $_lanIds);
            } else {
                $param['organization_id'] = Yii::app()->user->org_id;
            }
        }
        //Tickettemplatebase::api()->debug = true;
        $datas = Tickettemplatebase::api()->lists($param);
        $lists = ApiModel::getLists($datas);

        //分页
        $pagination = ApiModel::getPagination($datas);

        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目
        $this->render('index2', compact('lists', 'pages', 'lanIds', 'supplyLans','acction'));
    }

    //新增基础门票
    public function actionAdd() {
        //add
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            $param['organization_id'] = Yii::app()->user->org_id; //机构id
            $param['view_point'] = join(',', $param['view_point']);
            $param['items'] = json_encode($param['items']);
            $param['state'] = 1;
            //Tickettemplatebase::api()->debug = true;
            $data = Tickettemplatebase::api()->addBatch($param);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }

        //得到景区列表
        $param = array();
        $param['organization_id'] = Yii::app()->user->org_id;
        $param['is_manage'] = 1;
        $param['items'] = 100000;
        $data = Landscape::api()->lists($param);
        $lanLists = ApiModel::getLists($data);
        $this->renderPartial('add', compact('lanLists'));
    }

    //编辑基础门票
    public function actionEdit() {
        //add
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            $param['items'] = json_encode($param['items']);
            $param['view_point'] = join(',', $param['view_point']);
            //Tickettemplatebase::api()->debug =true;
            $data = Tickettemplatebase::api()->updateBatch($param);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }

        $data = Tickettemplatebase::api()->lists($_GET);
        $items = ApiModel::getLists($data);
        $this->renderPartial('edit', array('items' => $items));
    }

    //删除基础门票
    public function actiondel() {
        //add
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            $data = Tickettemplatebase::api()->updateBatch($param);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
    }

    //预览基础门票
    public function actionView() {
        $data = Tickettemplatebase::api()->lists($_GET);
        $items = ApiModel::getLists($data);
        $this->renderPartial('view', array('items' => $items));
    }

    //得到子景点
    public function actionGetPois() {
        if (Yii::app()->request->isPostRequest) {
            $field = array();
            $field['landscape_ids'] = $_POST['ids'];
            $field['status'] = 1;
            $field['items'] = 10000;
            // $field['organization_ids'] = Yii::app()->user->org_id; //机构id
            $datas = Poi::api()->lists($field);
            $data = ApiModel::getLists($datas);
            if ($data) {
                $vals = '';
                foreach ($data as $key => $item) {
                    $vals = $vals . '<div class="ckbox ckbox-primary pull-left" style="margin-right: 5px; min-width:100px;" >'
                        . '<input type="checkbox" value="' . $item['id'] . '" id="remember' . $item['id'] . '" checked="checked" class="view_point" name="view_point[]">'
                        . '<label for="remember' . $item['id'] . '">' . $item['name'] . '</label></div>';
                }
                echo json_encode($vals);
                Yii::app()->end();
            }
        }
    }

}
