<?php

Yii::import('ext.phpPasswordHashingLib.passwordLib', true);

class StaffController extends Controller {

    public function actionIndex() {
        $criteria = new CDbCriteria();
        $criteria->order = 'status DESC,id DESC';
        $criteria->compare('organization_id', Yii::app()->user->org_id);
        $criteria->compare('landscape_id', 0);
        $lists = Users::model()->findAll($criteria);
        $this->render('index', compact('lists'));
    }

    public function actionAccountexist() {
        $model = Users::model()->findByAttributes(array('account' => $_POST['account']));
        if ($model) {
            $this->_end(0, '用户名已被使用，请输入其他用户名');
        }
        $this->_end(1, '该账号不存在');
    }

    //新增加员工
    public function actionAdd() {
        $this->render('add');
    }

    //编辑员工信息
    public function actionEdit($id) {
        header("Content-type: text/html; charset=utf-8");
        $user = Users::model()->findByPk($id);
        $this->render('edit', compact('user'));
    }

    /*
     * updated_by chencq
     * 保存用户信息
     */
    public function actionSaveStaff() {
        if (Yii::app()->request->isPostRequest) {
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $id = $_POST['id'];
                $user = Users::model()->findByPk($id);
                $user->name = $_POST['name'];
                $user->mobile = $_POST['mobile'];
                $user->status = isset($_POST['status']) ? $_POST['status'] : 1;
                $user->updated_at = date('Y-m-d H:i:s');
                if (isset($_POST['password']) && (!empty($_POST['password']))) {
                    $user->password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT, array('cost' => 8));
                }
                if ($user->update()) {
                    if ($id === Yii::app()->user->uid) {
                        Yii::app()->user->display_name = $_POST['name'];
                    }
                    if (isset($_POST['role_id'])) {
                        $roleUser = RoleUser::model()->findByAttributes(array('uid' => $user->id));
                        if ($roleUser) {
                            $roleUser->attributes = array('role_id' => $_POST['role_id']);
                            $result = $roleUser->save();
                        } else {
                            $roleUser = new RoleUser();
                            $roleUser->attributes = array('uid' => $user->id, 'role_id' => $_POST['role_id'], 'updated_at' => date('Y-m-d H:i:s'));
                            $result = $roleUser->save();
                        }
                    }else{
                        $this->_end(0,'编辑成功！');
                        exit;
                    }
                }
                if($result){
                    $this->_end(0,'编辑成功');
                }else{
                    $this->_end(1,'编辑失败，请刷新页面重试');
                }
            }else{
                try {
                    $_POST['password'] = password_hash(trim($_POST['password']), PASSWORD_BCRYPT, array('cost' => 8));
                    $_POST['organization_id'] = Yii::app()->user->org_id;
                    $_POST['created_by'] = Yii::app()->user->org_id;
                    $_POST['created_at'] = $_POST['updated_at'] = date('Y-m-d H:i:s');

                    //查看是否是景区身份
                $criteria = new CDbCriteria();
                $criteria->order = 'status DESC,id DESC'; 
                $criteria->select = "sell_role";
                $criteria->compare('id', Yii::app()->user->uid);
                $lists = Users::model()->find($criteria);

                if($lists->sell_role=="scenic"){
                    $_POST['sell_role'] =  $lists->sell_role;
                }

                    $model = new Users();
                    $model->attributes = $_POST;

                    if ($model->save()) {
                        $roleUser = new RoleUser();
                        $roleUser->attributes = array('uid' => $model->id, 'role_id' => $_POST['role_id'], 'updated_at' => date('Y-m-d H:i:s'));
                        $roleUser->save();
                        $this->_end(0, '添加员工成功');
                    }
                   /* if(preg_match("/[\x7f-\xff]/",$_POST['account'])){
                        $this->_end(1, '帐号不能输入中文');
                    
                    }else{
                        $this->_end(1, '用户名已被使用，请输入其他用户名');
                    }*/

                    if(preg_match("/\W+/",$_POST['account'])){
                        $this->_end(1, '帐号只能输入英文和数字'); 
                    }else{
                       $this->_end(1, '用户名已被使用，请输入其他用户名'); 
                    }
                    
                } catch (Exception $e) {
                    $this->_end(0, $e . getmessage());
                }
            }
        }
    }

    //删除员工
    public function actionDel() {
        if (Yii::app()->request->isPostRequest) {
            $id = $_POST['id'];
            $count = Users::model()->deleteByPk($id);
            if ($count > 0) {
                $this->_end(0, '删除员工成功');
            } else {
                $this->_end(1, '删除员工失败');
            }
        }
    }

}
