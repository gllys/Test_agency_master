<?php
use common\huilian\utils\Header;
use common\huilian\utils\GET;

Yii::import('ext.phpPasswordHashingLib.passwordLib', true);

class ScenicController extends Controller {
    /*
     * 景区管理展示页
     */
 
    public function actionIndex() {
        //景区查询
        $param = $_REQUEST;
        $param['status'] = 1;   
        $param['current'] = isset($param['page']) ? $param['page'] : 1;
        
        $param['take_from_poi'] = 0;
        if(isset($_REQUEST['search']) && $_REQUEST['search'] == 1) {
            $param['fields'] = "name";
            $data = Landscape::api()->lists($param, true);  // 搜索添加缓存
            $lists = ApiModel::getLists($data);
            echo json_encode($lists);
            Yii::app()->end();
        } else {  
            if(isset($param['has_bind_org'])){
               if($param['has_bind_org']==='3'){unset($param['has_bind_org']);}
            }
		
            if(GET::required('partner_type') === null) {
            	unset($param['partner_type']);
            }
//             Header::utf8();
// var_dump($param);exit;
            $data = Landscape::api()->lists($param, 0);

            $lists = ApiModel::getLists($data);

            //景区级别
            $level = array(0 => '非A景区', 1 => 'A景区', 2 => 'AA景区', 3 => 'AAA景区', 4 => 'AAAA景区', 5 => 'AAAAA景区');

            //分页
            $pagination = ApiModel::getPagination($data);
            $pages = new CPagination($pagination['count']);
            $pages->pageSize = 15; #每页显示的数目
            $this->render('index', compact('lists', 'pages', 'level'));

        }
     }

    /*
     * 调取账号管理页面
     * @param id
     * created by ccq
     */

    public function actionReset() {
        $landscape_id = $_GET['landscape_id'];

        $lanRs = Landscape::api()->detail(array('id' => $landscape_id,'take_from_poi'=>0));
        if($lanRs['code'] == 'succ'){
            $scenicInfo = $lanRs['body'];
        }

        $result = SupplyUser::api()->search(array('landscape_id' => $landscape_id, 'is_super' => 0));
        if ($result['code'] == 'succ' && !empty($result['message'])) {
            $data['userInfo'] = $result['message'];
        } else {
            $data['message'] = '景区' . $scenicInfo['name'] . '，还未创建验票账号';
        }
        $data['get'] = $_GET;

        $this->renderPartial('reset', $data);
    }

    /*
     * 验票账号更新
     */

    public function actionSaveUser() {
        if (Yii::app()->request->isPostRequest) {
            $post = $_POST;
            $chk_user = $this->CheckUser($post['account']);
            if (!empty($post['user_id'])) {

                $userRS = SupplyUser::api()->search(array('id' => $post['user_id']));
                if (!empty($userRS['message'])) {
                    $userAccount = $userRS['message']['account'];
                }

                if ($userAccount != $post['account']) {
                    if (!empty($chk_user)) {
                        $this->_end(1, '该账号已经存在');
                        exit;
                    }
                }

                $param = array(
                    'id' => $post['user_id'],
                    'account' => $post['account'],
                    'password' => password_hash(trim($post['password']), PASSWORD_BCRYPT, array('cost' => 8)),
                    'password_str' => $post['password'],
                    'status' => 1
                );
                $result = SupplyUser::api()->update($param);
            } else {
                //判断账号是否存在
                if (!empty($chk_user)) {
                    $this->_end(1, '该账号已经存在');
                    exit;
                }

                $param = array(
                    'account' => $post['account'],
                    'password' => password_hash(trim($post['password']), PASSWORD_BCRYPT, array('cost' => 8)),
                    'password_str' => $post['password'],
                    'mobile' => '18909981212',
                    'landscape_id' => $post['landscape_id'],
                    'status' => 1
                );
                $result = SupplyUser::api()->add($param);
            }
        }
        if ($result['code'] == 'succ') {
            $this->_end(0, '保存成功');
        } else {
            $this->_end(1, $result['message']);
        }
    }

//    /*
//     * 调取绑定页面
//     * @param id
//     * created by ccq
//     */
//    public function actionBind(){
//        $id = $_GET['id'];
//
//        $lanRs = Landscape::api()->detail(array('id' => $id));
//        if($lanRs['code'] == 'succ'){
//            $scenicInfo = $lanRs['body'];
//        }
//
//        $lists = Landorg::api()->lists(array('landscape_id' => $id));
//        if($lists['code'] == 'succ'){
//            $result = $lists['body']['data'];
//        }
//        //获取供应商名称
//        if(is_array($result)){
//            foreach($result as $key => $value){
//                $orgRs = Organizations::api()->show(array('id' => $value['organization_id']));
//                $result[$key]['organization_name'] = $orgRs['body']['name'];
//            }
//        }
//
//
//        $data['scenicInfo'] = $scenicInfo;
//        $data['lists'] = $result;
//        $this->renderPartial('bind',$data);
//    }
//
//    /*
//     * 弹窗bind内数据置换
//     */
//    public function actionSupplyLists(){
//        $id = $_POST['id'];
//        /*
//         * 搜索时候显示供应商列表并进行是否绑定的判断
//         */
//        $orgParam['type'] = 'supply';
//        $orgParam['name'] = $_POST['organization_name'];
//
//
//        $orgRs = Organizations::api()->list($orgParam);
//        if($orgRs['code'] == 'succ'){
//            $orgLists = $orgRs['body']['data'];
//            /*
//             * 判断机构与景区的绑定情况
//            */
//            if(is_array($orgLists)){
//                foreach($orgLists as $key => $value){
//                    $check = $this->Check(array('landscape_id' => $id,'organization_id' => $value['id']));
//                    if($check){
//                        $orgLists[$key]['bind'] = 1;
//                    }else{
//                        $orgLists[$key]['bind'] = 0;
//                    }
//                }
//            }
//        }
//
//        if(!empty($orgLists)){
//            $this->_end(0,$orgLists);
//        }else{
//            $this->_end(1,'暂无该机构信息');
//        }
//    }
//

    /*
     * 绑定供应商以及解绑页面
     */
    public function actionSupply() {
        $id = $_GET['id'];

        $lanRs = Landscape::api()->detail(array('id' => $id,'take_from_poi'=>0));
        if($lanRs['code'] == 'succ'){
            $scenicInfo = $lanRs['body'];
        }

        $lists = Landorg::api()->lists(array('landscape_id' => $id, 'items' => 1000));
        if ($lists['code'] == 'succ') {
            $result = $lists['body']['data'];
        }

        #获取供应商名称数组
        $orgNames = $this->OrganizationList();

        /*
         * 供应商列表
         */
        $orgParam = array(
            'type' => 'supply',
            'current' => isset($_REQUEST['page']) ? $_REQUEST['page'] : 1,
            'fields' => 'id,name,supply_type,landscape_id'
        );
        if (isset($_REQUEST['organization_name']) && !empty($_REQUEST['organization_name'])) {
            $orgParam['name'] = $_REQUEST['organization_name'];
        }
        $orgRs = Organizations::api()->list($orgParam);
        if ($orgRs['code'] == 'succ') {
            $orgLists = $orgRs['body']['data'];
            $pagination = $orgRs['body']['pagination'];
            $pages = new CPagination($pagination['count']);
            $pages->pageSize = 15; #每页显示的数目

            /*
             * 判断机构与景区的绑定情况
             */
            if (is_array($orgLists)) {
                $check = $this->Check($scenicInfo['id']);
                foreach ($orgLists as $key => $value) {
                    if (!empty($check) && isset($check[$value['id']]) && !empty($check[$value['id']])) {
                        $orgLists[$key]['bind'] = 1;
                    } else {
                        $orgLists[$key]['bind'] = 0;
                    }
                }
            }
        }

        $data = array(
        	'id' => $id,
            'scenicInfo' => $scenicInfo,
            'pages' => $pages,
            'orgLists' => $orgLists,
            'orgNames' => $orgNames,
            'lists' => $result,
            'org_name' => isset($_REQUEST['organization_name']) && !empty($_REQUEST['organization_name']) ? $_REQUEST['organization_name'] : ''
        );
        $this->render('supply', $data);
    }

    /*
     * 电子票务账号页面
     */

    public function actionAccount() {
        $id = $_GET['land_id'];

        $lanRs = Landscape::api()->detail(array('id' => $id,'take_from_poi'=>0));
        if($lanRs['code'] == 'succ'){
            $scenicInfo = $lanRs['body'];
        }

        $result = SupplyUser::api()->lists(array(
            'sell_role' => 'scenic',
            'landscape_id' => intval($id),
            'organization_id' => intval($_GET['org_id'])
        ));
        $accountLists = empty($result['data']) ? array() : $result['data'];

        $data = array(
            'scenicInfo' => $scenicInfo,
            'accountLists' => $accountLists
        );
        $this->render('account', $data);
    }

    /*
     * 更新景区相关信息
     */

    public function actionSaveScenic() {
        if (Yii::app()->request->isPostRequest) {

            //图片处理
            if ($_POST['images']['id'] && $_POST['images']['url']) { #更新图片
                Landscapeimage::api()->update(array('id' => $_POST['images']['id'], 'landscape_id' => $_POST['id'], 'url' => $_POST['images']['url']));
            } else if ($_POST['images']['url']) { #创建图片
                Landscapeimage::api()->add(array('landscape_id' => $_POST['id'], 'url' => $_POST['images']['url']));
            }

            unset($_POST['images']);
            $param = $_POST;
            $param['phone'] = implode('-',$_POST['phone']);
            $param['feature'] = implode(',',$_POST['feature']);
            $param['biography'] = UbbToHtml::Entry($_POST['biography'], time());
            $param['user_id'] =Yii::app()->user->uid;
            $param['user_name'] =Yii::app()->user->display_name;

            $param['take_from_poi']=0;
            $param['biography'] = $_POST['description'];

            $rs = Landscape::api()->update($param);
            if ($rs['code'] == 'succ') {
                $this->_end(0, '更新成功！');
            } else {
                $this->_end(1, $rs['message']);
            }
        }
    }

    /*
     * 绑定新增用户页面
     */

    public function actionUser() {
        if ($_GET['org_type'] == 1) {
            $result = $this->CheckLandscape($_GET['organization_id']);
            if (!$result) {
                $data['scenicInfo'] = $result;
            }
        }
        $data['get'] = $_GET;
        $this->renderPartial('user', $data);
    }

    /**
     * 更新景区
     */
    public function actionUpdate() {
        $data = array(
            'id' => $_REQUEST['landscape_id'],
            'biography' => $_REQUEST['biography'],
            'take_from_poi'=>0
        );
        $update = Landscape::api()->update($data);
        echo json_encode($update);
    }

    //编辑景区
    public function actionEdit() {
        $data['feature'] = array('温泉','滑雪','乐园','海滨海岛','漂流','古迹','山水','赏花','采摘');
        $id = $_GET['id'];

        $lists = Landscape::api()->detail(array('id' => $id,'take_from_poi'=>0));
        if($lists['code'] == 'succ'){
            $info = $lists['body'];
        }

        $data['info'] = isset($info) ? $info : array();

        $this->render('edit', $data);
    }

    /*
     * 创建电子票务账号
     */

    public function actionSaveAccount() {
        $param = $_POST;
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
        } else {
            $this->_end(0, '电子票务账号添加成功', array(
                'account' => $account,
                'password_str' => $password
            ));
        }
    }

    /*
     * 账号状态更新
     */

    public function actionUpdateStatus() {
        $post = $_POST;
        $supply = array('id' => $post['id'], 'status' => $post['status']);
        if ($post['status'] != 1) {
            //子账号相关信息
            $param['organization_id'] = $post['organization_id'];
            $param['landscape_id'] = $post['landscape_id'];
            $result = $this->UpdateAllUsers($param);  #批量更新票务子账号
            if ($result) {
                $rs = SupplyUser::api()->update($supply);
            } else {
                $this->_end(1, '更新失败，请刷新页面重试');
                exit;
            }
        } else {
            $rs = SupplyUser::api()->update($supply);
        }
        if ($rs['code'] == 'succ') {
            $this->_end(0, '更新成功');
        } else {
            $this->_end(1, '更新失败，请刷新页面重试');
        }
    }

    /*
     * 管理权限的绑定与解绑
     */

    public function actionBindAdmin() {
        $post = $_POST;
        $update_lan = array(
            'user_name' => Yii::app()->user->display_name,
            'user_id' => Yii::app()->user->uid,
            'id' => $post['landscape_id']
        );
        $update_org = array(
            'id' => $post['organization_id'],
            'uid' => Yii::app()->user->uid
        );
        //解绑
        if ($post['type'] == 'unbind') {
            #检查是否存在为下架的门票
            $chk_tik = array(
                'scenic_id' => $post['landscape_id'],
                'or_id' => $post['organization_id'],
                'state' => 1
            );
            $chk_rs = $this->CheckTickets($chk_tik);
            if ($chk_rs) {
                $this->_end(1, '此供应商还有该景区未下架的产品，请将相关产品下架后再解除绑定');
                exit;
            }

            $update_lan['organization_id'] = 0;
            $update_org['landscape_id'] = 0;
            $lanRs = Landscape::api()->update($update_lan);
            if ($lanRs['code'] != 'succ') {
                $this->_end(1, $lanRs['message']);
            } else {
                $this->_end(0, '解绑成功');
            }
        } else {
            $update_lan['organization_id'] = $post['organization_id'];
            $update_org['landscape_id'] = $post['landscape_id'];

            $lanRs = Landscape::api()->updateOrganizationId($update_lan);
            if ($lanRs['code'] != 'succ') {
                $this->_end(1, $lanRs['message']);
            } else {
                $userRs = SupplyUser::api()->landscape(array('organization_id' => $post['organization_id'],
                    'landscape_id' => $post['landscape_id']));
                if ($userRs['code'] != 'succ') {
                    $this->_end(1, $userRs['message']);
                } else {
                    $this->_end(0, '绑定成功，请点击维护进行电子票务账号的更新');
                }
            }
        }
    }

    /*
     * 景区绑定供应商
     */

    public function actionSaveBind() {
        $post = $_POST;
        $param = array(
            'user_id' => Yii::app()->user->uid,
            'user_name' => Yii::app()->user->display_name,
            'organization_id' => $post['organization_id'],
            'landscape_id' => $post['landscape_id']
        );
        if ($post['type'] == 'bind') {
            $param['created_by'] = Yii::app()->user->account;
            $result = Landorg::api()->add($param);
        } else {
            $chk_tik = array(
                'scenic_id' => $post['landscape_id'],
                'or_id' => $post['organization_id'],
                'state' => 1
            );
            $chk_rs = $this->CheckTickets($chk_tik);
            if ($chk_rs) {
                $this->_end(1, '此供应商还有该景区未下架的产品，请将相关产品下架后再解除绑定');
                exit;
            }
            $result = Landorg::api()->del($param);
        }
        if (ApiModel::api()->isSucc($result)) {
            $this->_end(0, '保存成功');
        } else {
            $this->_end(1, $result['message']);
        }
    }

    /*
     * 获取景区类型机构与景区的绑定情况
     * @return landscape_name
     */

    public function actionCheckLandscape() {
        $organization_id = $_POST['organization_id'];
        $result = Landscape::api()->lists(array('organization_id' => $organization_id,'is_manage'=>1));
        if ($result['code'] == 'succ') {
            if (!empty($result['body']['data']) && is_array($result['body']['data'])) {
                foreach ($result['body']['data'] as $value) {
                    $landId = $value['id'];
                }
                if(isset($landId)){
                    $rs = Landscape::api()->detail(array('id' => $landId,'take_from_poi'=>0));
                    if($rs['code'] == 'succ'){
                        $this->_end(1,$rs['body']['name']);
                    }else{
                        $this->_end(0,'没有绑定景区');
                    }
                }
            } else {
                $this->_end(0, '没有绑定景区');
            }
        }
    }

    /*
     * 自定义函数--判断该机构是否已经与景区有所绑定
     */

    public function Check($landscape_id) {
        $param = array(
            'landscape_id' => $landscape_id,
            'fields' => 'organization_id',
            'items' => 1000
        );
        $landOrg = array();
        $result = Landorg::api()->lists($param);
        if ($result['code'] == 'succ') {
            if (!empty($result['body']['data']) && is_array($result['body']['data'])) {
                foreach ($result['body']['data'] as $value) {
                    $landOrg[$value['organization_id']] = $value['organization_id'];
                }
            }
        }
        return $landOrg;
    }

    //密码生成
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

    /*
     * 账号重复验证
     */

    public function CheckUser($account = '') {
        $result = SupplyUser::api()->search(array('account' => $account));
        if ($result['code'] == 'succ') {
            if (!empty($result['message'])) {
                return $result['message']['account'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
     * 电子票务账号批量禁用
     * 用于解除管理权限与禁用电子票务账号下
     */

    public function UpdateAllUsers($param = array()) {
        $result = TicketAccount::api()->status($param);
        if ($result['code'] == 'succ') {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 查询是否存在未下架门票
     */

    public function CheckTickets($param = array()) {
        $templateRs = Tickettemplate::api()->lists($param);
        $param['source_type'] = 1;
        $baseRs = Tickettemplatebase::api()->reserve_list($param);
        if ($baseRs['code'] == 'succ' && $templateRs['code'] == 'succ') {
            if (!empty($baseRs['body']['data']) || !empty($templateRs['body']['data'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
     * 批量对电子票务账号进行禁用
     */

    public function UpdateAllTicketUser($param = array()) {
        $param['sell_role'] = 'scenic';
        $userRs = SupplyAccount::api()->status($param);
        if ($userRs['code'] == 'succ') {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 获取供应商名称
     */

    public function OrganizationList() {
        $param = array(
            'type' => 'supply',
            'fields' => 'id,name,supply_type',
            'items' => 2000
        );
        $result = Organizations::api()->list($param);
        if ($result['code'] == 'succ') {
            return $result['body']['data'];
        }
    }

    /*
     * 切换景区供应商
     */

    public function actionSwitchadmin() {
        if (Yii::app()->request->isPostRequest) {
            //Landscape::api()->debug = true;
            $result = Landscape::api()->updateOrganizationId($_POST);
            if (ApiModel::isSucc($result)) {
                $this->_end(0, '切换供应商成功！');
            } else {
                $this->_end(1, '切换供应商失败！');
            }
        }
        $this->renderPartial('switchadmin');
    }

    /*
     * 获取景区名称
     */
//    public function LandscapeList() {
//        $param = array(
//            'fields' => 'id,name',
//            'items' => 5000
//        );
//        $LandList = array();
//        $result = Landscape::api()->lists($param);
//        if($result['code'] == 'succ'){
//            if(!empty($result['body']['data']) && is_array($result['body']['data'])){
//                foreach($result['body']['data'] as $value){
//                    $LandList[$value['id']] = $value['name'];
//                }
//            }
//        }
//        return $LandList;
//    }

    public function actionNew(){
        $feature = array('温泉','滑雪','乐园','海滨海岛','漂流','古迹','山水','赏花','采摘');
        $this->render('new',compact('feature'));
    }

    public function actionNewScenic(){

        $param = $_POST;
        $param['phone'] = implode('-',$_POST['phone']);
        $param['feature'] = implode(',',$_POST['feature']);
        $param['biography'] = UbbToHtml::Entry($_POST['biography'], time());
        $param['user_id'] =Yii::app()->user->uid;
        $param['user_name'] =Yii::app()->user->display_name;

       $resu = Landscape::api()->add($param);  //新增景区
        //print_r($param);exit;
        if($resu['code'] =='succ'){
            $rst = Landscapeimage::api()->add(array('landscape_id' => $resu['body']['id'], 'url' => $_POST['images']['url']));
            if($rst['code'] =='succ'){
                $this->_end(1,'新建景区成功！');
            }else{
                $this->_end(0,$rst['message']);
            }
        }else{
            $this->_end(0,$resu['message']);
        }

    }
}
