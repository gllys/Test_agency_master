<?php

class ScenicController extends Controller {

    public function actionDesk() {
        $this->actionIndex();
    }
    public function actionIndex() {
        //景点查询
        $param = $_REQUEST;
        $param['items'] = isset($_REQUEST['items']) ? $_REQUEST['items'] : 15;
        $param['status'] = 1;
        $param['take_from_poi'] = 0;
        //用景区名搜索的时候忽略分页
        $param['keyword'] = isset($param['keyword'])?trim($param['keyword']):'';
        if(!empty($param['keyword'])){
            $param['current'] = 1;
        }else{
            $param['current'] = isset($param['page']) ? $param['page'] : 1;
        }
        if (isset($param['province_ids'])) {
            $param['province_ids'] = join(',', $param['province_ids']);
        }
            //使用产品管理》新建产品 中的获取景区列表逻辑，删除了ArrayByUniqueKey代码 
        $params = array();
        $params['organization_id'] = YII::app()->user->org_id;
        $params['items'] = 100000;
        $data = Landorg::api()->lists($params,true,10);
        $supplyLans = ApiModel::getLists($data);
        $supplylanIds = PublicFunHelper::arrayKey($supplyLans, 'landscape_id');

        if (!empty($supplylanIds)) {
            $param['ids'] = join(',', $supplylanIds);
            // 是否是ajaxkuaiji
            if(isset($_REQUEST['search']) && $_REQUEST['search'] == 1) {
                $param['fields'] = "name";
                $data = Landscape::api()->lists($param, true);  // 搜索添加缓存
                $lists = ApiModel::getLists($data);
                echo json_encode($lists);
                Yii::app()->end();
            } else {
                $data = Landscape::api()->lists($param);
                $lists = ApiModel::getLists($data);
                //分页
                $pagination = ApiModel::getPagination($data);
                $pages = new CPagination($pagination['count']);
                $pages->pageSize = 15; #每页显示的数目
            }
        } else {
            $lists = $pages = array();
        }

        $this->render('index', compact('lists', 'pages'));
    }

    /**
     * 更新信息
     */
    public function actionUpdate()
    {
        $data = array(
            'id' => $_REQUEST['landscape_id'],
            'biography' => $_REQUEST['biography']
        );
        $update = Landscape::api()->update($data);
        echo json_encode($update );
    }
  
    /**
     * 备注：
     * - 需返回所有景点，不对用户进行organization_id筛选，
     * 即$param['organization_ids'] = YII::app()->user->org_id;
     */
    public function actionView() {
        $param = array();
        $param['id'] = Yii::app()->request->getParam('id');
        //$param['organization_id'] = YII::app()->user->org_id ;
        $rs = Landscape::api()->detail($param);
        $data = ApiModel::getData($rs);
        /*只读权限只能看见上架的景点*/
        if($data['organization_id'] == Yii::app()->user->org_id){
            $data['yes'] = 1;
        }else{
            $param['status'] = 1;
        }

        $param['landscape_ids'] = $param['id'];
        $param['current'] = isset($param['page']) ? $param['page'] : 1;
        $param['items'] = 1000;
        $datas = Poi::api()->lists($param);
        $lists = $datas['body']['data'];
        //分页
        $pagination = ApiModel::getPagination($datas);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 100; #每页显示的数目

        $this->render('view', compact('data','lists','pages'));
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

    //景点上下架  该景点还有上架门票 不可下架
    public function actionDownUP() {

        if (Yii::app()->request->isPostRequest) {
            $param['id'] = $_POST['id'];
            $param['organization_id'] = Yii::app()->user->org_id; //机构id
            $param['landscape_id'] = $_POST['landscape_id'];
            $param['status'] = $_POST['status'] ? 0 : 1;


            if($param['status'] == 0){
                //下架
                $t_request_data = array(
                    'view_point'=>$_POST['id'],
                    'state'  => 1,
                    'items' => 10000
                );
                $data = Tickettemplatebase::api()->lists($t_request_data);
                //判断是否有上架门票
                if ($data['code'] == 'succ' && empty($data['body']['data'])) {
                    $data = Poi::api()->update($param);
                    if ($data['code'] == 'succ') {
                        $this->_end(0, $data['message']);
                    } else {
                        $this->_end(1, $data['message']);
                    }
                }

                if(!empty($data['body']['data'])){
                    $this->_end(1,'该景点还有未下架门票');
                }


            }else{
                 //上架
                $data = Poi::api()->update($param);
                if ($data['code'] == 'succ') {
                    $this->_end(0, $data['message']);
                } else {
                    $this->_end(1, $data['message']);
                }
            }


        }
        $this->renderPartial('downUp');
    }


}
