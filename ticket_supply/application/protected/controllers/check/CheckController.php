<?php

class CheckController extends Controller {

    public function actionIndex() {
        //如果是景区用户，则直接跳转
        if (Yii::app()->user->lan_id && empty($_GET['landscape_id'])) {
            $this->redirect('/check/check/?landscape_id='.Yii::app()->user->lan_id);
        }
        
        //景点查询
        //Landscape::api()->debug = true ;
        $param = array();
        foreach ($_GET as $k => $v) {
            if (!empty($v))
                $param[$k] = $v;
        }
        $param['p'] = isset($param['page']) ? $param['page'] : 0;

        $param['supplier_id'] = YII::app()->user->org_id;
        $data = Verification::api()->record($param);
        
        $lists = ApiModel::getLists($data);
        //分页
        $pagination = ApiModel::getPagination($data);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目

        $param['status'] = 1;
        $param['items'] = 1000;
        $param['organization_id'] = YII::app()->user->org_id;
        $data = Landscape::api()->lists($param);
        $landscapes = ApiModel::getLists($data);

        $pois = array();
        if(isset($_GET['landscape_id'])){
            $lanid = $_GET['landscape_id'];
            $param['status'] = 1;
            $param['items'] = 1000;
            $param['landscape_ids'] = $lanid;
            $param['organization_ids'] = YII::app()->user->org_id;
            $data = Poi::api()->lists($param);
            $pois = ApiModel::getLists($data); 
        }
        

        $this->render('index', compact('lists', 'pages','landscapes','pois'));
    }

    public function actionGetPoi(){
        $id = $_POST['id'];
        $param['status'] = 1;
        $param['items'] = 1000;
        $param['landscape_ids'] = $id;
        $param['organization_ids'] = YII::app()->user->org_id;
        $data = Poi::api()->lists($param);
        $data['result'] = ApiModel::getLists($data);
        echo json_encode($data);
    }
    
    
    
    //撤销
    public function actionCancel() {
        if (Yii::app()->request->isPostRequest) {
            $param['id'] = $_POST['id'];
            $param['supplier_id'] = YII::app()->user->org_id;
            $rs = Verification::api()->cancel($param);
          //  print_r($rs);
            if($rs['code'] != 'fail'){
                $this->_end(0, $rs['message']); 
            }else{
                 $this->_end(1, $rs['message']);
            }
        }
    }

}
