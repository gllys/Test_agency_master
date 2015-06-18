<?php
use common\huilian\utils\GET;

class ManagequipController extends Controller {

    /*
     * 设备管理列表
     * 15-3-6
     * xujuan
     */
    public function actionIndex() {
    	ini_set('error_reporting','E_ALL & ~E_NOTICE');
        $type = array('手持','闸机','小票打印机(蓝牙)','小票打印机(USB)','身份证阅读器','扫码枪','二维码打印机','掌纹录入机');
        $get = $_GET;

        //供应商 景区 要求是模糊查询
        if(isset($get['org_name']) && !empty($get['org_name']))
            $param['org_name'] = $get['org_name'];

        if(isset($get['scenic_name']) && !empty($get['scenic_name']))
            $param['scenic_name'] = $get['scenic_name'];
        //设备编号 精确查询
        if(isset($get['code']) && !empty($get['code']))
            $param['code'] = $get['code'];
        
        $param = GET::requiredAdd(['is_bind', 'is_fix'], $param);

        if(isset($get['s_time']) && isset($get['e_time']) && !empty($get['s_time']) && !empty($get['e_time'])){
            $param['s_time'] = strtotime($get["s_time"] . " 00:00:00");
            $param['e_time'] = strtotime($get['e_time'] . " 23:59:59");
        }
        $param['p'] = $get['page'] ? $get['page'] : 1;
        $data = Equipments::api()->lists($param);
        $list = ApiModel::getLists($data);
        
        $bindData = Equipments::api()->landorg(array('show_all_organization'=>1,'show_all_landscape'=>1)); 
        //获取绑定供应商的名称
        //$supply_ids = array_unique(ArrayColumn::i_array_column($list, 'supply')); 
        $supply_ids = $bindData['body']['organization_id'];


        $supply = Organizations::api()->list(array('id'=>implode(',',$supply_ids),'fields' => 'id,name','items' => 1000));
        $supply = ApiModel::getLists($supply);
              
        //获取绑定景区的名称
        //$landscape_ids = array_unique(ArrayColumn::i_array_column($list, 'landscape_id')); 
        $landscape_ids = $bindData['body']['landscape_id'];
        //echo  strlen(implode(',',$landscape_ids));die;
        $landscape = Landscape::api()->lists(array('ids'=>implode(',',$landscape_ids),'take_from_poi'=>0,'parent_id'=>1,'items' => 1000));
        $landscape = ApiModel::getLists($landscape);
               
        //分页
        $pagination = ApiModel::getPagination($data);
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目

        //加载视图
        $this->render('index',compact('list','pages','get','supply','landscape','type'));

    }
     /*
         * 设备删除
         * 15-3-9
         * xujuan
         */


   public function  actionDelEquip(){
       $data = Equipments::api()->delete($_POST);
       if ($data['code'] != 'succ') {
           echo json_encode(array('errors' => array('msg' => $data['message'])));
       } else {
           echo json_encode(array());
       }
   }
    /*
   * 设备编辑
* 15-3-9
* xujuan
*/

    public function actionEdit() {
        $get = $_GET;
        $data = Equipments::api()->show($get);
        $equipment = $data['body'];
        $landscape = Landscape::api()->detail(array('id' => $equipment['landscape_id']));
        $poi = Poi::api()->detail(array('id' => $equipment['poi_id'], 'landscape_id' => $equipment['landscape_id']));
        $equipment['poi'] = $poi['body'];
        $equipment['landscape'] = $landscape['body'];
        $this->render('edit', array('equipment' => $equipment));
    }



    public function actionUpEquip() {
        $data = Equipments::api()->update($_POST);
        if ($data['code'] != 'succ') {
            echo json_encode(array('errors' => array('msg' => $data['message'])));
        } else {
            echo json_encode(array());
        }
    }

    /*
     * 绑定供应商页面
     * 15-03-10
     * xujuan
     */
    public function actionSupply() {
        ini_set('error_reporting','E_ALL & ~E_NOTICE');
        //获取设备信息
        $get = $_GET;
        //获取设备信息
        $equipment = Equipments::api()->show($get);
        //获取景区数据
        $supply = Organizations::api()->show(array('id' => $equipment['body']['organization_id']));
        //景区名称
        if ($get['supply_name']) {
            $param['name'] = $get['supply_name'];
        }
        $param['current'] = isset($get['page']) ? $get['page'] : 1;
        $param['type'] = 'supply';
        $result = Organizations::api()->list($param);
        $supplyList = isset($result['body']['data']) ? $result['body']['data'] : array();

        $data['get'] = $get;
        $data['supplys'] = $supplyList;
        $data['supply'] = $supply['body'];

        //获取景区数据
        $landscape = Landscape::api()->detail(array('id' => $equipment['body']['landscape_id']));
        $data['landscape'] = $landscape['body'];

        $poi = Poi::api()->detail(array('id' => $equipment['body']['poi_id'], 'landscape_id' => $equipment['body']['landscape_id']));
        $data['poi'] = $poi['body'];

        //分页
        $pagination = ApiModel::getPagination($result);
        $data['pages'] =new CPagination($pagination['count']);
        $data['pages']->pageSize = 15; #每页显示的数目

        $data['equipment'] = $equipment['body'];

        //加载视图

        $this->render('supply', $data);
    }

    /*
     * 绑定景区操作
     * 15-03-10
     * xujuan
     */
    public function actionSaveESupply() {
        $post = $_POST;
        $param['id'] = $post['eid'];
        $param['type'] = 2;
        $param['statue'] = 1;
        $param['scene_id'] = $post['sid'];
        // 检查设备是否已经绑定机构
        $rs = Equipments::api()->detail(array('id'=>$post['eid']));
        $equipmentInfos = ApiModel::getData($rs);
        if(isset($equipmentInfos['organization_id']) && (!empty($equipmentInfos['organization_id']))){
            echo json_encode(array('errors' => array('msg' => "请先解除绑定！")));
        } else {
            $data = Equipments::api()->binding($param);
            if ($data['code'] != 'succ') {
                echo json_encode(array('errors' => array('msg' => $data['message'])));
            } else {
                echo json_encode(array('data' => array($data['body'])));
            }
        }
    }

    /*
     * 解除关联设备
     */
    public function actionRemoveESupply() {
        $post = $_POST;
        $param['id'] = $post['eid'];
        $param['type'] = 2;
        $param['statue'] = 0;
        $param['scene_id'] = 0;
        $data = Equipments::api()->binding($param);
        if ($data['code'] != 'succ') {
            echo json_encode(array('errors' => array('msg' => $data['message'])));
        } else {
            echo json_encode(array('data' => array($data['body'])));
        }
    }


    /*
     * 绑定景区页面
     * 15-03-10
     * xujuan
     */
    public function actionLandscape() {
        //获取设备信息
        $get = $_GET;
        //获取设备信息
        $equipment = Equipments::api()->show($get);
        //获取景区数据
        $landscape = Landscape::api()->detail(array('id' => $equipment['body']['landscape_id']));
        //景区名称
        if (isset($get['landscape_name'])) {
            $param['keyword'] = $get['landscape_name'];
        }
        $param['organization_id'] = $equipment['body']['organization_id'];
        $param['current'] = isset($get['page']) ? $get['page'] : 1;
        $data = Landscape::api()->lists($param);
        $landscapes = ApiModel::getLists($data);

        $poi = Poi::api()->detail(array('id' => $equipment['body']['poi_id'], 'landscape_id' => $equipment['body']['landscape_id']));

        $data['get'] = $get;
        $data['landscapes'] = $landscapes;
        $data['landscape'] = $landscape['body'];
        $data['poi'] = $poi['body'];


        //分页
        $pagination = ApiModel::getPagination($data);
        $data['pages'] = new CPagination($pagination['count']);
        $data['pages']->pageSize = 15; #每页显示的数目
        $data['equipment'] = $equipment['body'];

        //加载视图
        $this->render('landscape', $data);
    }

    /*
     * 绑定景区操作
     */
    public function actionSaveELandscape() {
        $post = $_POST;
        $param['id'] = $post['eid'];
        $param['type'] = 0;
        $param['statue'] = 1;
        $param['scene_id'] = $post['lid'];
        // 检查设备是否已经绑定景区
        $rs = Equipments::api()->detail(array('id'=>$post['eid']));
        $equipmentInfos = ApiModel::getData($rs);
        if(isset($equipmentInfos['landscape_id']) && (!empty($equipmentInfos['landscape_id']))){
            echo json_encode(array('errors' => array('msg' => "请先解除绑定！")));
        } else {
            $data = Equipments::api()->binding($param);
            if ($data['code'] != 'succ') {
                echo json_encode(array('errors' => array('msg' => $data['message'])));
            } else {
                echo json_encode(array('data' => array($data['body'])));
            }
        }
    }
    /*
     * 解除景区绑定
     */
    public function actionRemoveELandscape() {
        $post =  $_POST;
        $param['id'] = $post['eid'];
        $param['type'] = 0;
        $param['statue'] = 0;
        $param['scene_id'] = 0;
        $data = Equipments::api()->binding($param);
        if ($data['code'] != 'succ') {
            echo json_encode(array('errors' => array('msg' => $data['message'])));
        } else {
            echo json_encode(array('data' => array($data['body'])));
        }
    }

    public function actionScenic() {
        //获取设备信息
        $get = $_GET;
        $current = isset($get['page']) ? $get['page'] : 1;
        //获取设备信息
        $equipment = Equipments::api()->show($get);

        //获取景区数据
        $landscape = Landscape::api()->detail(array('id' => $equipment['body']['landscape_id']));

        $data = Poi::api()->lists(array('current' => $current, 'landscape_ids' => $equipment['body']['landscape_id']));
        $pois = ApiModel::getLists($data);
        $poi = Poi::api()->detail(array('id' => $equipment['body']['poi_id'], 'landscape_id' => $equipment['body']['landscape_id']));

        $data['get'] = $get;

        //分页
        $pagination = ApiModel::getPagination($data);
        $data['pages'] = new CPagination($pagination['count']);
        $data['pages']->pageSize = 15; #每页显示的数目

        $data['pois'] = $pois;
        $data['poi'] = $poi['body'];
        $data['equipment'] = $equipment['body'];
        $data['landscape'] = $landscape['body'];

        //加载视图
        $this->render('scenic', $data);
    }


    /*
     * 绑定景点操作
     */
    public function actionSaveEScenic() {
        $post =  $_POST;
        $param['id'] = $post['eid'];
        $param['type'] = 1;
        $param['statue'] = 1;
        $param['scene'] = 1;
        $param['scene_id'] = $post['pid'];       
        $param['landscape_id'] = $post['landscapeid'];       
        $data = Equipments::api()->binding($param);
        if ($data['code'] != 'succ') {
            echo json_encode(array('errors' => array('msg' => $data['message'])));
        } else {
            echo json_encode(array('data' => array($data['body'])));
        }
    }
    /*
     * 解除景点
     */
    public function actionRemoveEScenic() {
        $post =  $_POST;
        $param['id'] = $post['eid'];
        $param['type'] = 1;
        $param['statue'] = 0;
        $param['scene_id'] = 0;
        $data = Equipments::api()->binding($param);
        if ($data['code'] != 'succ') {
            echo json_encode(array('errors' => array('msg' => $data['message'])));
        } else {
            echo json_encode(array('data' => array($data['body'])));
        }
    }


}
