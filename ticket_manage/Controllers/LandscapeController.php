<?php

/**
 * 机构管理控制器
 * 2014-1-2
 * @package controller
 * @author cuiyulei
 * */
class LandscapeController extends BaseController {

    //查找景区列表并审核
    /**
     * 拥有景区审核
     *
     * @return void
     * @author cuiyulei
     * */
    public function lists() {
        header("Content-type: text/html; charset=utf-8");
        $param = $this->get();
        $param['current'] = isset($param['p']) ? $param['p'] : 0;
        if (isset($param['province_ids'])) {
            $param['province_ids'] = join(',', $param['province_ids']);
        }
        //Landscape::api()->debug = true;
        $rs = Landscape::api()->lists($param, 0);
        $data['list'] = ApiModel::getLists($rs);

        //分页
        $pagination = ApiModel::getPagination($rs);
        $data['pagination'] = $this->getPagination($pagination);
//        print_r($data);
        //加载视图
        $data['get'] = $this->get();
        $this->load->view('landscape/lists', $data);
    }

    /**
     * 获取landscape的景区信息 景区版一级票务无删除情况
     *
     * @return void
     * @author fangshixiang
     * */
    public function info() {
        $landscapeId = $this->getGet('id');
        $common = $this->load->common('landscape');
        $data['scenicInfo'] = $common->getLandscapeDetail($landscapeId);
        $this->load->view('landscape/info', $data);
    }

    /**
     * 获取landscape的弹框
     *
     * @return void
     * @author cuiyulei
     * */
    public function getModalJumpCheck() {
        $landscapeId = $this->getGet('id');
        $landscapeModel = $this->load->model('landscapes');
        $landscapeLModel = $this->load->model('landscapeLastEdit');
        $param = array(
            'relate' => 'level',
            'with' => 'districts',
            'filter' => array('id' => $landscapeId)
        );

        $paramL = array(
            'relate' => 'level',
            'with' => 'districts',
            'filter' => array('landscape_id' => $landscapeId)
        );

        $landscape = $landscapeModel->commonGetList($param);
        $landscapeLast = $landscapeLModel->commonGetList($paramL);
        $data['landscape'] = $landscape['data'][0];
        $data['landscapeLast'] = $landscapeLast['data'][0];

        //加载视图
        $this->load->view('landscape/lists_tpl', $data);
    }

    /**
     * verify
     *
     * @return void
     * @author cuiyulei
     * */
    public function verify() {
        $post = $this->getPost();
        echo $this->doAction('landscape', 'verify', $post);
    }

    /**
     * 关联景区三维景拍
     *
     * @return void
     * @author fangshixiang
     * */
    public function unionPoi() {
        #获取景点信息
        $landscapeId = $this->getGet('id');
        $landscapeModel = $this->load->model('landscapes');
        $param = array(
            'relate' => 'organization',
            'filter' => array('id' => $landscapeId)
        );
        $landscape = $landscapeModel->commonGetList($param);
        $data['landscape'] = $landscape['data'][0];

        #获取景区三维景拍列表
        $data['poi'] = array();
        $data['pagination'] = '';
        $data['hashPois'] = array();
        $page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $post = $this->getPost();
        $filters = array();
        if (!empty($post['name'])) {
            $filters[] = 'name:like_' . $post['name'] . '%';
        }

        if (!empty($post['hash'])) {
            $filters[] = 'hash:equal_' . $post['hash'];
        }
        $param = array(
            'filter' => join(',', $filters),
            'page' => $page,
            'items' => 10
        );


        $result = $this->load->common('ItourismApi')->getSomeLocations($param, array('name', 'hash')); //接口获取
        if ($result) {
            $data['poi'] = $result['data'];
            $data['pagination'] = $this->getPagination($result['pagination']);
            $_data = $data['poi'];
            #得到关联机构，如果没有则未关联
            $hashs = arrayKey($_data, 'hash');
            $param = array(
                'relate' => 'organization',
            );
            if ($hashs) {
                $param['filter'] = array('location_hash|in' => $hashs);
            }
            $landscapeHashs = $landscapeModel->commonGetAll($param);
            #转换成hash对应景点,以后可能一对多
            $data['hashPois'] = arrayByKeys($landscapeHashs, 'location_hash');
        }
        //加载视图
        $this->load->view('landscape/union_poi_tpl', $data);
    }

    /**
     * 取消景区关联三维景拍
     *
     * @return void
     * @author fangshixiang
     * */
    public function cancelPoi() {
        echo $this->common->cancelPoi($this->getPost());
    }

    /**
     * 添加景区关联三维景拍
     *
     * @return void
     * @author fangshixiang
     * */
    public function addPoi() {
        echo $this->common->addPoi($this->getPost());
    }

    /**
     * 查看景点的子景区
     *
     * @return void
     * @author fangshixiang
     * */
    public function childLists() {
        #获取景点信息
        $landscapeId = $this->getGet('id');
        $landscapeModel = $this->load->model('landscapes');
        $data['landscape'] = $landscape = $landscapeModel->getOne(array('id' => $landscapeId));
        #获取子景点信息
        $model = $this->load->model('poi');
        $param = array(
            'filter' => array('organization_id' => $landscape['organization_id'], 'deleted_at' => NULL, 'landscape_id' => $landscapeId,),
            //以后可能加景区查询
        );
        $data['list'] = $model->commonGetAll($param);
        //加载视图
        $this->load->view('landscape/child_lists_tpl', $data);
    }

    /**
     * 添加设备
     *
     * @return void
     * @author cuiyulei
     * */
    public function addEquip() {
        $this->load->view('equipment/add');
    }

    /**
     * 编辑设备
     *
     * @return void
     * @author cuiyulei
     * */
    public function editEquip() {
        $get = $this->getGet();
        $data = Equipments::api()->show($get);
        $equipment = $data['body'];
        $landscape = Landscape::api()->detail(array('id' => $equipment['landscape_id']));
        $poi = Poi::api()->detail(array('id' => $equipment['poi_id'], 'landscape_id' => $equipment['landscape_id']));
        $equipment['poi'] = $poi['body'];
        $equipment['landscape'] = $landscape['body'];
        $this->load->view('equipment/edit', array('equipment' => $equipment));
    }

    /**
     * 管理设备
     *
     * @return void
     * @author cuiyulei
     * */
    public function equipments() {
        $get = $this->getGet();
        $times = explode(' - ', $get['update_time']);
        if (isset($get['landscape']) && $get['landscape'] != "全部")
            $param['is_bind'] = $get['landscape'] == "yes" ? 1 : 0;
        if (isset($get['poi']) && $get['poi'] != "全部")
            $param['is_fix'] = $get['poi'] == "yes" ? 1 : 0;
        if (isset($get['update_time']) && count($times) > 1) {
            $param['s_time'] = strtotime($times[0] . " 00:00:00");
            $param['e_time'] = strtotime($times[1] . " 23:59:59");
        }
        $param['p'] = $get['p'] ? $get['p'] : 1;
        $data = Equipments::api()->lists($param);
        $list = ApiModel::getLists($data);

        //分页
        $page = ApiModel::getPagination($data);
        $pagination = $this->getPagination($page);


        //加载视图
        $this->load->view('equipment/lists', array('list' => $list, 'pagination' => $pagination, 'get' => $get));
    }

    public function supply() {
        //获取设备信息
        $get = $this->getGet();
        //获取设备信息
        $equipment = Equipments::api()->show($get);
        //获取景区数据
        $supply = Organizations::api()->show(array('id' => $equipment['body']['organization_id']));
        //景区名称
        if ($get['supply_name']) {
            $param['name'] = $get['supply_name'];
        }
        $param['current'] = isset($get['p']) ? $get['p'] : 1;
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

        $page = ApiModel::getPagination($result);
        $data['pagination'] = $this->getPagination($page);
        $data['equipment'] = $equipment['body'];

        //加载视图
        $this->load->view('equipment/supply', $data);
    }

    /**
     * 设备绑定景区界面
     *
     * @return void
     * @author cuiyulei
     * */
    public function landscape() {
        //获取设备信息
        $get = $this->getGet();
        //获取设备信息
        $equipment = Equipments::api()->show($get);
        //获取景区数据
        $landscape = Landscape::api()->detail(array('id' => $equipment['body']['landscape_id']));
        //景区名称
        if ($get['landscape_name']) {
            $param['keyword'] = $get['landscape_name'];
        }
        $param['organization_id'] = $equipment['body']['organization_id'];
        $param['current'] = isset($get['p']) ? $get['p'] : 1;
        $data = Landscape::api()->lists($param);
        $landscapes = ApiModel::getLists($data);

        $poi = Poi::api()->detail(array('id' => $equipment['body']['poi_id'], 'landscape_id' => $equipment['body']['landscape_id']));

        $data['get'] = $get;
        $data['landscapes'] = $landscapes;
        $data['landscape'] = $landscape['body'];
        $data['poi'] = $poi['body'];
        $page = ApiModel::getPagination($data);
        $data['pagination'] = $this->getPagination($page);
        $data['equipment'] = $equipment['body'];

        //加载视图
        $this->load->view('equipment/landscape', $data);
    }

    public function account() {
        $get = $this->getGet();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest'
            && $get['id'] == 0) {
            $this->g_Account();
            exit;
        }
        $data['id'] = $get['id'];
        $data['organization_id'] = $get['org_id'];
        if ($get['id'] > 0 && $get['org_id'] > 0) {
            $rs = Landscape::api()->detail(array('id' => $data['id']));
            $data['landscape'] = isset($rs['body']) ? $rs['body'] : array();
            $rs = SupplyUser::api()->lists(array(
                'sell_role' => 'scenic',
                'landscape_id' => intval($get['id']),
                'organization_id' => intval($get['org_id'])
            ), false);
            $data['data'] = empty($rs['data']) ? array() : $rs['data'];
        }

        $this->load->view('landscape/account', $data);

    }

    private function g_Account() {
        $param = $this->getPost();
        if ($param['landscape_id'] > 0) {
            $account = 'jq' . base_convert(date('ymdHis'), 10, 16);
            $password = $this->create_password();
            //添加景区用户
            $result = array(
                'account' => $account,
                'password' => password_hash($password, PASSWORD_BCRYPT, array('cost' => 8)),
                'password_str' => $password,
                'landscape_id' => intval($param['landscape_id']),
                'organization_id' => intval($param['organization_id']),
                'status' => 1,
                'is_super' => 1,
                'sell_role' => 'scenic',
                'mobile' => '13800138123',
                'created_at' => date('Y-m-d H:i:s')
            );
            $result['updated_at'] = $result['created_at'];

            $rs = SupplyUser::api()->add($result, false);
            if (!ApiModel::isSucc($rs)) {
                $this->_end(1, '电子票务账号添加不成功');
            }
            else {
                $this->_end(0, '电子票务账号添加成功', array(
                    'account' => $account,
                    'password_str' => $password
                ));
            }
        }
        else {
            $this->_end(2, '电子票务账号添加不成功');
        }
    }

    /**
     * 设备绑定子景点界面
     *
     * @return void
     * @author cuiyulei
     * */
    public function scenic() {
        //获取设备信息
        $get = $this->getGet();
        $current = isset($get['p']) ? $get['p'] : 1;
        //获取设备信息
        $equipment = Equipments::api()->show($get);
        //获取景区数据
        $landscape = Landscape::api()->detail(array('id' => $equipment['body']['landscape_id']));
        $data = Poi::api()->lists(array('current' => $current, 'landscape_ids' => $equipment['body']['landscape_id']));
        $pois = ApiModel::getLists($data);
        $poi = Poi::api()->detail(array('id' => $equipment['body']['poi_id'], 'landscape_id' => $equipment['body']['landscape_id']));

        $data['get'] = $get;
        $data['pois'] = $pois;
        $data['poi'] = $poi['body'];
        $page = ApiModel::getPagination($data);
        $data['landscape'] = $landscape['body'];
        $data['pagination'] = $this->getPagination($page);
        $data['equipment'] = $equipment['body'];

        //加载视图
        $this->load->view('equipment/scenic', $data);
    }

    public function saveESupply() {
        $post = $this->getPost();
        $param['id'] = $post['eid'];
        $param['type'] = 2;
        $param['statue'] = 1;
        $param['scene_id'] = $post['sid'];
        $data = Equipments::api()->binding($param);
        if ($data['code'] != 'succ') {
            echo json_encode(array('errors' => array('msg' => $data['message'])));
        } else {
            echo json_encode(array('data' => array($data['body'])));
        }
    }

    /**
     * 删除关联设备
     *
     * @return json
     * @author cuiyulei
     * */
    public function removeESupply() {
        $post = $this->getPost();
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

    /**
     * 景区关联设备
     *
     * @return json
     * @author cuiyulei
     * */
    public function saveELandscape() {
        $post = $this->getPost();
        $param['id'] = $post['eid'];
        $param['type'] = 0;
        $param['statue'] = 1;
        $param['scene_id'] = $post['lid'];
        $data = Equipments::api()->binding($param);
        if ($data['code'] != 'succ') {
            echo json_encode(array('errors' => array('msg' => $data['message'])));
        } else {
            echo json_encode(array('data' => array($data['body'])));
        }
    }

    /**
     * 删除关联设备
     *
     * @return json
     * @author cuiyulei
     * */
    public function removeELandscape() {
        $post = $this->getPost();
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

    /**
     * 景点关联设备
     *
     * @return json
     * @author cuiyulei
     * */
    public function saveEScenic() {
        $post = $this->getPost();
        $param['id'] = $post['eid'];
        $param['type'] = 1;
        $param['statue'] = 1;
        $param['scene_id'] = $post['pid'];
        $data = Equipments::api()->binding($param);
        if ($data['code'] != 'succ') {
            echo json_encode(array('errors' => array('msg' => $data['message'])));
        } else {
            echo json_encode(array('data' => array($data['body'])));
        }
    }

    /**
     * 删除子景点关联设备
     *
     * @return json
     * @author cuiyulei
     * */
    public function removeEScenic() {
        $post = $this->getPost();
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

    /**
     * 保存设备
     *
     * @return void
     * @author cuiyulei
     * */
    public function saveEquip() {
        $data = Equipments::api()->add($this->getPost());
        if ($data['code'] != 'succ') {
            echo json_encode(array('errors' => array('msg' => $data['message'])));
        } else {
            echo json_encode(array('data' => array($data['body'])));
        }
    }

    /**
     * 更新设备
     *
     * @return void
     * @author cuiyulei
     * */
    public function upEquip() {
        $data = Equipments::api()->update($this->getPost());
        if ($data['code'] != 'succ') {
            echo json_encode(array('errors' => array('msg' => $data['message'])));
        } else {
            echo json_encode(array());
        }
    }

    /**
     * 删除设备
     *
     * @return void
     * @author cuiyulei
     * */
    public function delEquip() {
        $data = Equipments::api()->delete($this->getPost());
        if ($data['code'] != 'succ') {
            echo json_encode(array('errors' => array('msg' => $data['message'])));
        } else {
            echo json_encode(array());
        }
    }

    /**
     * 绑定供应商页
     *
     * @return void
     * @author fangshixiang
     * */
    public function bindSupply() {
        //获取设备信息
        $get = $this->getGet();
        //获取景区
        $rs = Landscape::api()->detail(array('id' => $get['id']));
        $data['scenicInfo'] = ApiModel::api()->getData($rs);

        //得到供应商列表
        $param = $this->get();
        $param['current'] = isset($param['p']) ? $param['p'] : 0;
        $param['type'] = 'supply';
        //景区名称
        if ($get['supply_name']) {
            $param['name'] = $get['supply_name'];
        }
        unset($param['id']);
        $rs = Organizations::api()->list($param);
        //Organizations::api()->debug() ;

        $data['list'] = ApiModel::getLists($rs);

        #分页
        $pagination = ApiModel::getPagination($rs);
        $data['pagination'] = $this->getPagination($pagination);
        $data['get'] = $this->get();
        $this->load->view('landscape/supply', $data);
    }

    /**
     * 保存绑定供应商
     *
     * @return void
     * @author fangshixiang
     * */
    public function saveBindSupply() {
        $param = $this->getPost();

        //查看该机构和景区账号是否已经存在
        $rs = SupplyUser::api()->search(array('organization_id' => $param['organization_id'], 'landscape_id' => $param['landscape_id']));
        if (!$rs['data']) {
            $account = 'piaotai_' . date('ymdHis');
            $password = $this->create_password();
            //添加景区用户
            $result = array(
                'account' => $account,
                'password' => password_hash($password, PASSWORD_BCRYPT, array('cost' => 8)),
                'password_str' => $password,
                'repassword' => password_hash($password, PASSWORD_BCRYPT, array('cost' => 8)),
                'organization_id' => $param['organization_id'],
                'landscape_id' => $param['landscape_id'],
                'status' => 1,
                'is_super' => 0,
                'mobile' => '13800138123',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $rs = SupplyUser::api()->add($result, false);
            if (!ApiModel::isSucc($rs)) {
                $this->_end('景区用户添加不成功，请重新绑定');
            }
        }

        //绑定供应商
        $param['user_id'] = $_SESSION['backend_userinfo']['id'];
        $param['user_name'] = $_SESSION['backend_userinfo']['name'];
        
        $rs = Landorg::api()->add($param);
        //Landorg::api()->debug = true;
        if (ApiModel::api()->isSucc($rs)) {
            echo 1;
        } else {
            // $this->_end($rs['message']);
            $this->_end("stata");
        }
    }

    /**
     * 保存绑定供应商
     *
     * @return void
     * @author xuejian
     * */
    public function setSupSupply() {
        $param = $this->getPost();
        $param['user_id'] = $_SESSION['backend_userinfo']['id'];
        $param['user_name'] = $_SESSION['backend_userinfo']['name'];
        file_put_contents("D:/log/log.txt", print_r($param, true), FILE_APPEND);
        //Landorg::api()->debug = true;
        $rs = Landscape::api()->update($param);
        if (ApiModel::api()->isSucc($rs)) {
            echo 1;
        } else {
            $this->_end($rs['message']);
        }
    }
    
    public function create_password($pw_length = 8) {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $pw_length; $i++) {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * 解除绑定供应商
     *
     * @return void
     * @author fangshixiang
     * */
    public function saveUnbindSupply() {
        $param = $this->getPost();

        //判断与该景区绑定的供应商是否存在为下架的票
        $paramT['scenic_id'] = $param['landscape_id'];
        $paramT['state'] = 1;
        $paramT['or_id'] = $param['organization_id'];
        $baseRs = Tickettemplatebase::api()->lists($paramT);
        $templateRs = TicketTemplate::api()->lists($paramT);
        if(!empty($baseRs['body']['data']) || !empty($templateRs['body']['data'])){
            echo json_encode(array('error' => '该机构下还有本景区未下架的门票,请下架后再进行绑定解除！'));
            exit;
        }

        //解除供应商与景区之间的绑定
        $param['user_id'] = $_SESSION['backend_userinfo']['id'];
        $param['user_name'] = $_SESSION['backend_userinfo']['name'];
        $rs = Landorg::api()->del($param);
        if (ApiModel::api()->isSucc($rs)) {
            echo 1;
        } else {
            echo json_encode(array('error' => $rs['message']));
        }
    }

    //管理权限的绑定与解绑
    public function bindAdmin() {
        $param = $this->getPost();
        //判断是否有上架门票
        if ($param['type'] == 'unbind') {
            $paramT['scenic_id'] = $param['id'];
            $paramT['state'] = 1;
            $paramT['or_id'] = $param['organization_id'];
            $baseRs = Tickettemplatebase::api()->lists($paramT);
            $templateRs = TicketTemplate::api()->lists($paramT);
            if(!empty($baseRs['body']['data']) || !empty($templateRs['body']['data'])){
                echo json_encode(array('error' => '该机构下还有本景区未下架的门票,请下架后再进行绑定解除！'));
                exit;
            }
        }
        //更新景区数据的数组
        $paramL['user_id'] = $_SESSION['backend_userinfo']['id'];
        $paramL['id'] = $param['id'];
        $paramL['user_name'] = $_SESSION['backend_userinfo']['name'];
        if($param['type'] == 'unbind'){    
            $paramL['organization_id'] = 0;   
        }else{
            $paramL['organization_id'] = $param['organization_id']; 
        }
        $rs = Landscape::api()->update($paramL,0);

        //判断更新是否成功
        if (ApiModel::api()->isSucc($rs)) {
            $paramO['id'] = $param['organization_id'];
            $paramO['uid'] = $_SESSION['backend_userinfo']['id'];
            if($param['type'] == 'unbind'){
                $paramO['landscape_id'] = 0 ;
                //成功，则更新所有该景区及供应商下的相关账号的状态，更改为禁用
                $userRs = SupplyAccount::api()->status(array('organization_id' => $param['organization_id'], 
                                                                'landscape_id' => $param['id']));
                if($userRs['code'] != 'succ'){
                    echo json_encode(array('error' => $userRs['message']));
                    exit;
                }
                $ticketRs = TicketsAccount::api()->status(array('organization_id' => $param['organization_id'], 
                                                                'landscape_id' => $param['id']));
                if($ticketRs['code'] != 'succ'){
                    echo json_encode(array('error' => $ticketRs['message']));
                    exit;
                }

            }else{
                $paramO['landscape_id'] = $param['id'];
                //绑定景区后，同时更新该景区账号的一个状态
                $orgShow = Organizations::api()->show(array('id' => $param['organization_id']));
                if($orgShow['body']['type'] == 'supply' && $orgShow['body']['supply_type'] == 1){
                    $userRs = SupplyAccount::api()->landscape(array('organization_id' => $param['organization_id'], 
                                                                    'landscape_id' => $param['id']));

                    if($userRs['code'] != 'succ'){
                        echo json_encode(array('error' => $userRs['message']));
                        exit;
                    }
                }
            }
            $orgRs = Organizations::api()->edit($paramO,0);
            if(ApiModel::api()->isSucc($orgRs)){
                echo 1;
            }else{
                 echo json_encode(array('error' => $orgRs['message']));
                 exit;
            }
        } else {
            echo json_encode(array('error' => $rs['message']));
        }
    }

    //判断是否景区用户并且是否有绑定的景区
    public function checkLandscape() {
        $param = $this->getPost();
        $rs = Landscape::api()->detail($param,0);
        if(ApiModel::api()->isSucc($rs) && !empty($rs['body'])){
            $this->_end($rs['body']);
        }
    }

    //判断景区是否有过绑定
    public function checkLandorg(){
        $param = $this->getPost();
        $rs = Landorg::api()->lists($param);
        if(ApiModel::api()->isSucc($rs)){
            if(!empty($rs['body']['data'])){
                $paramL['id'] = $rs['body']['data'][0]['landscape_id'];
                $landRs = Landscape::api()->detail($paramL);
                if(ApiModel::api()->isSucc($landRs)){
                    $info = ApiModel::api()->getData($landRs);
                    echo json_encode(array('error' => $info));
                }
            }else{
                echo 1;
            }
        }
    }


    //景区账号启用与禁用
    public function update_status(){
        $post = $this->getPost();

        //主账号相关信息
        $param = array();
        $param['id'] = $post['id'];
        $param['status'] = $post['status'];
        $rs = SupplyAccount::api()->update($param);
        if($rs['code'] == 'succ'){
            if($post['status'] != 1){
                //子账号相关信息
                $param = array();
                $param['organization_id'] = $post['organization_id'];
                $param['landscape_id'] = $post['landscape_id'];
                $result = TicketsAccount::api()->status($param);
                if($result['code'] == 'succ'){
                    echo 1;
                }
            }else{
                echo 1;
            }   
        }else{
            $this->_end($rs['message']);
        }
    }

    public function add() {
// $data = $this->organizationCommon->getBankList();
        $scenicCommon = $this->load->common('scenic');
        $data = $scenicCommon->getScenicRelation();

        $this->load->view('landscape/add', $data);
    }

    public function edit() {
        header("Content-type: text/html; charset=utf-8");
        $get = $this->getGet();
        $oid = intval($get['id']);
        if ($oid <= 0) {
            exit('ID 不能为空');
        }

        $rs = Landscape::api()->detail(array('id' => $get['id']));
        $data['scenicInfo'] = ApiModel::api()->getData($rs);
        $province_id = $data['scenicInfo']['province_id'];
        $city_id = $data['scenicInfo']['city_id'];
        $district_id = $data['scenicInfo']['district_id'];
        $data['provinceInfo'] = empty($province_id) ? array() : $this->load->model('districts')->getOne(array('id' => $province_id));
        $data['cityInfo'] = empty($city_id) ? array() : $this->load->model('districts')->getOne(array('id' => $city_id));
        $data['districtInfo'] = empty($district_id) ? array() : $this->load->model('districts')->getOne(array('id' => $district_id));
        $data['areaInfo'] = $this->load->model('districts')->getDistricts($data['scenicInfo']['district_id'], 'level');
        $data['levelInfo'] = $this->load->model('landscapeLevels')->getList();

        $data['pageType'] = 'edit';
        $this->load->view('landscape/add', $data);
    }

    public function save() {
        $post = $this->getPost();
//图片处理
        if ($post['images']['id'] && $post['images']['url']) { #更新图片
            Landscapeimage::api()->update(array('id' => $post['images']['id'], 'landscape_id' => $post['id'], 'url' => $post['images']['url']));
        } else if ($post['images']['url']) { #创建图片
            $rs = Landscapeimage::api()->add(array('landscape_id' => $post['id'], 'url' => $post['images']['url']));
        }

//景区更新
        if ($post['province_id'] == '__NULL__') {
            $post['province_id'] = 'NULL';
            //$post['province_id'] = 0;
        }

        if ($post['city_id'] == '__NULL__') {
            $post['city_id'] = 'NULL';
            //$post['city_id'] = 0;
        }

        if ($post['district_id'] == '__NULL__') {
            $post['district_id'] = 'NULL';
            //$post['district_id'] = 0;
        }
        //print_r($post);exit;
        unset($post['images']);
        $rs = Landscape::api()->update($post);
        if (ApiModel::api()->isSucc) {
            echo 1;
        } else {
            return $this->_end($rs['message']);
        }
        exit();
    }

    /**
     * 员工管理
     *
     * @return void
     * @author cuiyulei
     * */
    public function staff() {
//获取参数
        $get = $this->getGet();
        $oid = intval($get['id']);
        $UserModel = $this->load->model('users');

//组织查询条件
        $param['filter']['organization_id'] = $oid;
        $param['filter']['deleted_at'] = null;

//获取数据
        $UserList = $UserModel->commonGetList($param);
        $data = $UserList;
        $data['id'] = $get['id'];
        $organization = $this->load->model('organizations')->getOne(array('id' => $oid));
        $data['organization'] = $organization;

//加载视图
        $this->load->view('landscape/staff', $data);
    }

    /**
     * 添加员工
     *
     * @return void
     * @author cuiyulei
     * */
    public function addStaff() {
        $get = $this->getGet();
        $oid = intval($get['id']);
        $organization = $this->load->model('organizations')->getOne(array('id' => $oid));
        $data['organization'] = $organization;
        $data['type'] = 'add';
        $this->load->view('landscape/staff_add', $data);
    }

    /**
     * 编辑员工
     *
     * @return void
     * @author cuiyulei
     * */
    public function editStaff() {
        $id = $this->getGet('id');
        $userModel = $this->load->model('users');
        $info = $userModel->getOne(array('id' => $id));
        $data['info'] = $info;
        $data['type'] = 'edit';
        $oid = intval($info['organization_id']);
        $organization = $this->load->model('organizations')->getOne(array('id' => $oid));
        $data['organization'] = $organization;
        $this->load->view('landscape/staff_add', $data);
    }

    /**
     * 操作员工
     *
     * @return void
     * @author cuiyulei
     * */
    public function doStaff() {
        $post = $this->getPost();
        if (!$post['id']) {
            echo json_encode(array('data' => 'fail'));
            exit;
        }
        $ids = implode(',', $post['id']);
        $UserCommon = $this->load->common('user');
        if ($post['type'] == 'del') {
            $UserModel = $this->load->model('users');
            $UserModel->update(array('deleted_at' => date('Y-m-d H:i:s', time())), "id IN ($ids)");
        } elseif ($post['type'] == 'status') {
            $UserCommon->editStatus($ids);
        }
        echo json_encode(array('data' => 'success'));
        exit;
    }

    /**
     * 保存员工
     *
     * @return void
     * @author cuiyulei
     * */
    public function saveStaff() {
        $post = $this->getPost();
        if ($post['type'] == 'edit') {
            unset($post['type']);
            if (empty($post['password'])) {
                unset($post['password']);
            }
            $this->doAction('user', 'userEdit', $post);
        } else {
            unset($post['type']);
            $post['created_by'] = $_SESSION['backend_userinfo']['id'];
            $post['organization_id'] = $post['organization_id'];
            $post['created_at'] = date('Y-m-d H:i:s');
            $post['updated_at'] = date('Y-m-d H:i:s');
            echo $this->doAction('user', 'userAdd', $post);
            exit;
        }
    }

}

// END class
