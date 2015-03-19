<?php

class ScenicController extends Controller {

    public function actionDesk() {
        $this->actionIndex();
    }
    public function actionIndex() {
        //景点查询
        //Landscape::api()->debug = true ;
        $param = $_REQUEST;
        $param['status'] = 1;
        $param['current'] = isset($param['page']) ? $param['page'] : 1;
        if (isset($param['province_ids'])) {
            $param['province_ids'] = join(',', $param['province_ids']);
        }
        if (intval(YII::app()->user->org_id) > 0) {
            $param['organization_id'] = YII::app()->user->org_id;
            $data = Landscape::api()->lists($param);
            $lists = ApiModel::getLists($data);
           

            //分页
            $pagination = ApiModel::getPagination($data);
            $pages = new CPagination($pagination['count']);
            $pages->pageSize = 15; #每页显示的数目
        } else {
            $lists = $pages = array();
        }



        $this->render('index', compact('lists', 'pages'));
    }

  
    
    
    public function actionView() {
        $param = array();
        $param['id'] = Yii::app()->request->getParam('id');
        $param['organization_id'] = YII::app()->user->org_id ;
        $rs = Landscape::api()->detail($param);
        $data = ApiModel::getData($rs); //景区基本信息
       
        $param['organization_ids'] = Yii::app()->user->org_id; //机构id
        $param['landscape_ids'] = $param['id'];

        $param['current'] = isset($param['page']) ? $param['page'] : 1;
        $param['items'] = 100;
        $datas = Poi::api()->lists($param); 
        $lists = $datas['body']['data']; //景点的基本信息
        
        
       // $field['or_id']  =   Yii::app()->user->org_id; //机构id  
        $field['scenic_id'] = $param['id'];
        $field['p'] = isset($param['page']) ? $param['page'] : 1;
        $field['items'] = 100; //获票列表
        $tickets = Tickettemplatebase::api()->lists($field);
        $ticket = ApiModel::getLists($tickets);
        if(empty($ticket)){
            $data['ticket'] = array();
        }else{
            $data['ticket'] =  $ticket[$param['id']];
        }

        //分页
        $pagination = ApiModel::getPagination($datas);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 100; #每页显示的数目
        
        $this->render('view', compact('data','lists','pages'));
    }
    
        //编辑景区信息
    public function actionEditinfo(){ 
        $param = $_POST;
        $param['user_id'] = Yii::app()->user->uid;
        $param['user_name'] = Yii::app()->user->account;
        $update = Landscape::api()->update($param);
        echo json_encode($update );
    }
    
    
    //新增票种
    public function actionTicket(){
        $id = $_GET['scenic_id']; 

        //下方景点
        $param['landscape_ids'] = $id;
        $param['organization_ids'] = Yii::app()->user->org_id;
        $param['status'] = 1;
        $data = Poi::api()->lists($param);
        $list = ApiModel::getLists($data);
        
        $this->render('ticket',  compact('id','list'));
    }

    //发布票
    public function actionAddticket(){
         if (Yii::app()->request->isPostRequest) {
            $field = $_REQUEST;       
            //通过景区 获取省市区
            $param['id'] = $field['scenic_id'];
            $param['organization_id'] = $field['organization_id'] = Yii::app()->user->org_id;
            $detail = Landscape::api()->detail($param);
            $val = ApiModel::getData($detail);

            $field['province_id'] = $val['province_id'];
            $field['city_id']     = $val['city_id'];
            $field['district_id'] = $val['district_id'];
            
            $field['user_id']     = Yii::app()->user->uid;
            $field['user_account']     = Yii::app()->user->account;
            $field['user_name']     = Yii::app()->user->display_name;

            $field['fat_price'] = 0 ;
            $field['group_price'] = 0 ;
            $field['refund'] = 0 ;
            $field['scheduled_time'] = 0 ;
            $field['mini_buy'] = 1 ;
            $field['max_buy'] = 100 ;
                
            if (!empty($_REQUEST['view_point'][0]) && isset($_REQUEST['view_point'][0])) {
                $field['view_point'] = implode(',', $_REQUEST['view_point']);
            } else {
                $this->_end(1, '景点不可以为空！');
            }
             
            if (isset($field['all_available']) && $field['all_available'] == 1) {
              //  $field['date_available'] = 1;
                $field['is_infinite'] = 1;
                $field['date_available'] = '0,9999999999';
                unset($field['all_available']);
            }else {
                $a_time = strtotime($_REQUEST['date_available'][0] . ' 00:00:00');
                $b_time = strtotime($_REQUEST['date_available'][1] . ' 23:59:59');
                if ($b_time < $a_time) {
                    //交换
                    $t_time = $a_time + 86399;//23:59:59
                    $a_time = $b_time - 86399;
                    $b_time = $t_time;
                }
                $field['date_available'] = $a_time . ',' . $b_time;
            }
            if (count($_REQUEST['week_time']) > 0) {
                $field['week_time'] = implode(',', $_REQUEST['week_time']);
            } else {
                $this->_end(1, '适用日期不可为空！');
            }
            
            if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'edit'){
               $field['or_id'] = Yii::app()->user->org_id;
             //  Tickettemplatebase::api()->debug = true;
                $list = Tickettemplatebase::api()->update($field);
            }else{
                $list = Tickettemplatebase::api()->addGenerate($field);
            }
            if ($list['code'] == 'succ') {
                $this->_end(0, $list['message']);
            } else {
                $this->_end(1, $list['message']);
            }
         }
    }
    
    //编辑门票
    public function actionTicketedit(){
        //票基本信息
        $ticketinfo = Tickettemplatebase::api()->ticketinfo(array('ticket_id'=>$_GET['id']));
        $info = ApiModel::getData($ticketinfo);
        $id = $info['scenic_id'];
        
        //景点
        $param['landscape_ids'] = $id;
        $param['organization_ids'] = Yii::app()->user->org_id;
        $param['status'] = 1;
        $data = Poi::api()->lists($param);
        $list = ApiModel::getLists($data);
        $this->render('ticket',  compact('id','list','info'));
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
            //print_r($_POST);
            $data = Poi::api()->update($param);
            //print_r($data);
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
