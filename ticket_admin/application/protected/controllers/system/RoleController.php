<?php

class RoleController extends Controller {

    //权限列表
    public function actionIndex() {
        $criteria = new CDbCriteria();
        $criteria->compare('status', 1);
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
    public function actionDel() {
        if (Yii::app()->request->isPostRequest && isset($_POST['id'])) {
			$id = $_POST['id'];
			$roleUser = RoleUser::model()->findByAttributes(array('role_id'=>$id, 'is_delete'=>0));
			if($roleUser) {
				$this->_end('fail', '无法删除，此角色仍有关联用户');
			} else {
				$role = Role::model()->findByPk($id);
				$role->status = 0;
				if($role->save()) {
					$this->_end('succ', '删除角色成功');
				} else {
					$this->_end('fail', $role->getErrors());
				}
			}
        }
    }

    //权限保存
    public function actionSaveRole() {
        if (Yii::app()->request->isPostRequest) {
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $id = $_POST['id'];
                $model = Role::model()->findByPk($id);

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
	
	// 检查权限名
	public function actionNameExist() {
        $model = Role::model()->find('name=:name and id!=:id and status=1', array(':name'=>$_POST['name'], ':id'=>$_POST['id']));
        if ($model) {
            $this->_end('fail', '该权限名已经存在');
		} else {
			$this->_end('succ', '');
		}
    }


}
