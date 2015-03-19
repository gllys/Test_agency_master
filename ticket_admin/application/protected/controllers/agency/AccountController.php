<?php

Yii::import('ext.phpPasswordHashingLib.passwordLib', true);

class AccountController extends Controller {

    public function actionIndex() {

        $this->render('index');
    }


    public function actionAddagency() {

        $this->render('addagency');
    }


    public function actionRes() {
        $data = array();
        if(isset($_GET) && !empty($_GET)){
            $param['type'] = 'agency';
            $param['items'] = 15;
            $param['current'] = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
            //获取条件
            if(isset($_REQUEST['province_id']) && $_REQUEST['province_id'] != '__NULL__'){
                $param['province_id'] = $_REQUEST['province_id'];
                $data['province_set'] = $_REQUEST['province_id'];
            }

            if(isset($_REQUEST['city_id']) && $_REQUEST['city_id'] != '__NULL__'){
                $param['city_id'] = $_REQUEST['city_id'];
                $data['city_set'] = $_REQUEST['city_id'];
            }

            if(isset($_REQUEST['district_id']) && $_REQUEST['district_id'] != '__NULL__'){
                $param['district_id'] = $_REQUEST['district_id'];
                $data['district_set'] = $_REQUEST['district_id'];
            }

            if(isset($_REQUEST['name']) && !empty($_REQUEST['name']) && !is_null($_REQUEST['name'])){
                $param['name'] = $_REQUEST['name'];
                $data['agency_name']  = $_REQUEST['name'];
            }

            $orgRs = Organizations::api()->list($param);

            //组织数据
            if($orgRs['code'] == 'succ'){
                $data['lists'] = $orgRs['body']['data'];
                $agencyList = $orgRs['body']['data'];
                foreach ($agencyList as $key => $agency) {
                    $city = Districts::model()->findByPk($agency['city_id']);
                    if ($city['name'] == '市辖区' || $city['name'] == '县') {
                        $city = Districts::model()->findByPk($agency['province_id']);
                    }
                    $data['lists'][$key]['city_name'] = $city['name'];
                    //判断是否已经有过绑定
                    $agencyRes = Credit::api()->lists(array('distributor_id' => $agency['id'],'supplier_id' => Yii::app()->user->org_id));
                    $agencyData = ApiModel::getLists($agencyRes);
                    if($agencyData){
                        $data['lists'][$key]['is_bind'] = 1;
                    }else{
                        $data['lists'][$key]['is_bind'] = 0;
                    }
                }
                $data['pages'] = new CPagination($orgRs['body']['pagination']['count']);
                $data['pages']->pageSize = $param['items'];
            }
        }
        $this->render('res', $data);
    }


    public function actionSaveAgency() {


        $Users = new Users;
        $Users->account = $_POST['account'];
        $Users->password = password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 8));
        $Users->mobile = $_POST['mobile'];
        $Users->save();
        $onlyOne = $Users->attributes['id'];
        $data = Organizations::api()->reg($_POST);
    }

    public function actionAdd() {
        $fields = array('id', 'account', 'name', 'password');
        $results = Users::model()->find('account=:account', array(':account' => Yii::app()->getUser()->account));
        foreach ($fields as $field) {
            $result[$field] = $results->$field;
        }

        if (Yii::app()->request->isPostRequest) {
            #查看分销商账号是否已存在
            $rs = AgencyUser::api()->search(array('account'=>$_POST['agencyname']));
            if($rs['message']){
                $this->_end(3, '用户名已经存在，请输入其他用户名');//code 2 用户名已经存在
            }

            #查看用户名是否长度满足
            if(strlen($_POST['agencyname']) < 3){
                $this->_end(1, '请输入有效的用户名，用户名最短为3个字符');
                exit;
            }

            #添加机构
            $param = $_POST;
			if(!empty($param['phoneditribute']) && !empty($param['phonenum'])) {
				$param['telephone'] = $param['phoneditribute'].$param['phonenum'];
				if(!empty($param['phoneextension'])) {
					$param['telephone'] .= '-'.$param['phoneextension'];
				}
				unset($param['phoneditribute']);
				unset($param['phonenum']);
				unset($param['phoneextension']);
			}
			if(!empty($param['faxditribute']) && !empty($param['faxnum'])) {
				$param['fax'] = $param['faxditribute'].$param['faxnum'];
				if(!empty($param['faxextension'])) {
					$param['fax'] .= '-'.$param['faxextension'];
				}
				unset($param['faxditribute']);
				unset($param['faxnum']);
				unset($param['faxextension']);
			}
            $param['type'] = 'agency';
            $param['status'] = 1;
            $param['uid'] = $result['id']; //操作人id；
            $param['supply_id'] = yii::app()->user->org_id;
            if($param['province_id']=='__NULL__' || $param['city_id']=='__NULL__' || $param['district_id']=='__NULL__'){
                $this->_end(2, '请选择所在地！');//code 2 地区不能为空
            }else{
                 $data = Organizations::api()->bind_agency($param);
            }
            if (!ApiModel::isSucc($data)) {
                $this->_end(1, $data['message']);
            }
            $orgId = $data['body']['id'];
            //添加个人信息
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 8)) ;
            $result = array(
                'account'=>$_POST['agencyname'],
                'password'=>  $password,
                'repassword'=>  $password,
                'organization_id'=>$orgId,
                'mobile'    => $_POST['mobile'],//update by wychao Fix PWII-620
                'status'=>1,
                'is_super'=>1,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s'),
            );
            //AgencyUser::api()->debug = true;
            $rs = AgencyUser::api()->add($result);
            if ($rs['code']!="succ") {
                $this->_end(1, $rs['message']);
            }else{
                $this->_end(0, $rs['message']);
            }
        }
    }

    public function actionAdduser() {

        if (Yii::app()->request->isPostRequest) {
            //添加个人信息
            $result = new Users;
            $result->account = $_POST['name'];
            $result->password = password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 8));
            $result->organization_id = $_POST['organization_id'];

            $result->is_super = 1;
            $result->created_at = date('Y-m-d H:i:s');
            $result->updated_at = date('Y-m-d H:i:s');
            if ($result->save() > 0) {
                $data['message'] = '用户添加成功！';
                $this->_end(0, $data['message']);
            } else {
                $data = $result->getErrors();
                $data['account'][0] .=",请到员工管理中添加用户！";
                $this->_end(1, $data['account'][0]);
            }
        }
    }

}
