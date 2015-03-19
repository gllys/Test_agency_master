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
            $this->_end(0, '该账号已经存在');
        }
        $this->_end(1, '该账号不存在');
    }

    //新增加员工
    public function actionAdd() {
        $showError = false;
        if (Yii::app()->request->isPostRequest) {
            try {
                $_POST['password'] = password_hash(trim($_POST['password']), PASSWORD_BCRYPT, array('cost' => 8));
                $_POST['organization_id'] = Yii::app()->user->org_id;
                $_POST['created_by'] = Yii::app()->user->org_id;
                $_POST['created_at'] = $_POST['updated_at'] = date('Y-m-d H:i:s');

                $model = new Users();
                $model->attributes = $_POST;

                if ($model->save()) {
                    $roleUser = new RoleUser();
                    $roleUser->attributes = array('uid' => $model->id, 'role_id' => $_POST['role_id'], 'updated_at' => date('Y-m-d H:i:s'));
                    $roleUser->save();
                    // $this->redirect('/system/staff/edit/?id=' . $model->id);
                    $this->_end(0, '添加员工成功');
                }
                $this->_end(1, '该账号已存在');
            } catch (Exception $e) {
                $this->_end(0, $e . getmessage());
            }
        }
        $this->render('add', compact('showError'));
    }

    //编辑员工信息
    public function actionEdit($id) {
        header("Content-type: text/html; charset=utf-8");
        $showError = false;
        $user = Users::model()->findByPk($id);
        if (Yii::app()->request->isPostRequest) {
            $user->name = $_POST['name'];
            $user->mobile = $_POST['mobile'];
            $user->account = $_POST['account'];
            $user->status = isset($_POST['status']) ? $_POST['status'] : 1;
            $user->updated_at = date('Y-m-d H:i:s');
            if ($_POST['password']) {
                $user->password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT, array('cost' => 8));
            }
            if ($user->save()) {
                if ($id === Yii::app()->user->uid) {
                    Yii::app()->user->display_name = $_POST['name'];
                }
                if (isset($_POST['role_id'])) {
                    $roleUser = RoleUser::model()->findByAttributes(array('uid' => $user->id));
                    if ($roleUser) {
                        $roleUser->attributes = array('role_id' => $_POST['role_id']);
                        $roleUser->save();
                    } else {
                        $roleUser = new RoleUser();
                        $roleUser->attributes = array('uid' => $user->id, 'role_id' => $_POST['role_id'], 'updated_at' => date('Y-m-d H:i:s'));
                        $roleUser->save();
                    }
                }
                echo "<script>alert('编辑员工成功');document.location='/system/staff/';</script>";
                Yii::app()->end();
            }
            $showError = true;
        }


        $this->render('edit', compact('user', 'rolearr', 'showError'));
    }

    //删除员工
    public function actionDel($id) {
        if (Yii::app()->request->isPostRequest) {
            $count = Users::model()->deleteByPk($id);
            // $model = Users::model()->findByPk($id);
            // $model->status = 0;
            // $model->save();
            if ($count > 0) {
                echo "删除成功";
            } else {
                echo "删除失败";
            }
        }
    }

}
