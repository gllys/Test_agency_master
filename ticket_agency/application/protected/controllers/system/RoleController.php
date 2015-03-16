<?php

class RoleController extends Controller {

    //权限列表
    public function actionIndex() {
        $criteria = new CDbCriteria();
        $criteria->compare('status', 1);
        $criteria->compare('organization_id', Yii::app()->user->org_id);
        $criteria->order = 'id DESC';
        $list = Role::model()->findAll($criteria);
        $this->render('index', compact('list'));
    }

    //添加权限
    public function actionAdd() {
        header("Content-type: text/html; charset=utf-8");
        #post请求赋值
        $showError = false;
        if (Yii::app()->request->isPostRequest) {
            $_POST['permissions'] = empty($_POST['permissions'])?array():$_POST['permissions'];
            $_POST['permissions'] = json_encode($_POST['permissions']);
            $_POST['organization_id'] = Yii::app()->user->org_id;
            $_POST['created_dataline'] = time();
            $model = new Role();
            $model->attributes = $_POST;
            if ($model->save()) {
                 echo "<script>alert('添加角色成功');document.location='/system/role/';</script>";
            }
            $showError = true;
        }
        $this->render('add', compact('showError'));
    }

    //编辑权限
    public function actionEdit($id) {
        header("Content-type: text/html; charset=utf-8");
        $model = Role::model()->findByPk($id);
        if (!$model || $model['organization_id'] != Yii::app()->user->org_id) {
            exit('您没有权限编辑此用户');
        }

        $showError = false;
        if (Yii::app()->request->isPostRequest) {
            $_POST['permissions'] = json_encode($_POST['permissions']);
            $model->attributes = $_POST;
            if ($model->save()) {
                echo "<script>alert('编辑角色成功');document.location='/system/role/';</script>";
                Yii::app()->end();
            }
            $showError = true;
        }
        $this->render('edit', compact('model', 'showError'));
    }

    //删除权限
    public function actionDel($id) {
        if (Yii::app()->request->isPostRequest) {
            $model = Role::model()->findByPk($id);
            $model->status = 0;
            $model->save();
        }
    }

}
