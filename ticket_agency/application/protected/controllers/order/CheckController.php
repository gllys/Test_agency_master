<?php

class CheckController extends Controller {

    public function actionView($is_export = false) {
        $this->actionIndex($is_export);
    }

    public function actionIndex() {
        //如果是景区用户，则直接跳转
        /*if (Yii::app()->user->lan_id && empty($_GET['landscape_id'])) {
            $this->redirect('/check/check/?landscape_id='.Yii::app()->user->lan_id);
        }*/

        //景点查询
        //Landscape::api()->debug = true ;
        $param = array();
        foreach ($_GET as $k => $v) {
            if (!empty($v))
                $param[$k] = $v;
        }
        $param['current'] = isset($param['page']) ? $param['page'] : 0;

        $param['distributor_id'] = YII::app()->user->org_id;
        //echo "<pre>";print_r($param);echo "</pre>";
        $data = Verification::api()->record($param);
        //echo "<pre>";print_r($data);die("</pre>");
        $lists = ApiModel::getLists($data);
        $totalNums = $data['body']['total_nums'] ;
        $orderNums = $data['body']['order_nums'] ;
        //分页
       $pagination = ApiModel::getPagination($data);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目
        $param['status'] = 1;
        $param['items'] = 1000;
        $param['organization_id'] = YII::app()->user->org_id;
        // 此处不能为景区列表引入分页值，否则造成第二分页无法获取景区列表
        $data = Landscape::api()->lists(array_merge($param, array('current' => 0, )));
        $landscapes = ApiModel::getLists($data);

        $pois = array();
        if(isset($_GET['landscape_id'])) {
            $lanid = $_GET['landscape_id'];
            $param['status'] = 1;
            $param['items'] = 1000;
            $param['landscape_ids'] = $lanid;
            //$param['organization_ids'] = YII::app()->user->org_id;
            $data = Poi::api()->lists($param);
            $pois = ApiModel::getLists($data);
        }
        //echo "<pre>";print_r($data);die("</pre>");
        $this->render('index', compact('lists', 'pages', 'landscapes', 'pois', 'totalNums', 'orderNums'));
    }


}
