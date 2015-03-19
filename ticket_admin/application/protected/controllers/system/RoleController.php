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
        $this->render('add');
    }

    //编辑权限
    public function actionEdit($id) {
        header("Content-type: text/html; charset=utf-8");
        $model = Role::model()->findByPk($id);
        $this->render('edit', compact('model'));
    }

    //删除权限
    public function actionDel($id) {
        if (Yii::app()->request->isPostRequest) {
            $model = Role::model()->findByPk($id);
            $model->status = 0;
            $model->save();
        }
    }

    //权限更正
    public function actionSaveRole() {
        if (Yii::app()->request->isPostRequest) {
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $id = $_POST['id'];
                $model = Role::model()->findByPk($id);
                if (!$model || $model['organization_id'] != Yii::app()->user->org_id) {
                    $this->_end(1,'您没有权限编辑此用户');
                    exit;
                }

                if (Yii::app()->request->isPostRequest) {
                    $_POST['permissions'] = json_encode($_POST['permissions']);
                    $model->attributes = $_POST;
                    if ($model->save()) {
                        $this->_end(0,'保存成功');
                    }else{
                        $this->_end(1,'保存失败，请刷新后重试');
                    }
                }
            }else{
                $_POST['permissions'] = empty($_POST['permissions'])?array():$_POST['permissions'];
                $_POST['permissions'] = json_encode($_POST['permissions']);
                $_POST['organization_id'] = Yii::app()->user->org_id;
                $_POST['created_dataline'] = time();
                $model = new Role();
                $model->attributes = $_POST;
                if ($model->save()) {
                    $this->_end(0,'保存成功');
                }else{
                    $this->_end(1,'保存失败，请刷新后重试');
                }
            }

        }
    }

}
