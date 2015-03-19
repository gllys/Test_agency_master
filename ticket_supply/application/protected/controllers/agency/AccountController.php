<?php

Yii::import('ext.phpPasswordHashingLib.passwordLib', true);

class AccountController extends Controller {

    public function actionIndex() {

        $this->render('index');
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
                $this->_end(1, '用户名已经存在');
            }
            
            #添加机构
            $param = $_POST;
            $param['type'] = 'agency';
            $param['status'] = 1;
            $param['uid'] = $result['id']; //操作人id；
            $param['supply_id'] = yii::app()->user->org_id;

            if($param['province_id']=='__NULL__' || $param['city_id']=='__NULL__' || $param['district_id']=='__NULL__'){
                $this->_end(1, '地区不可为空！');
            }else{
                 $data = Organizations::api()->bind_agency($param);
            }
            if (!ApiModel::isSucc($data)) {
                $this->_end(1, $data['message']);
            }
            $orgId = $data['body']['id'];
            //添加个人信息
            $result = array(
                'account'=>$_POST['agencyname'],
                'password'=>  password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 8)),
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
