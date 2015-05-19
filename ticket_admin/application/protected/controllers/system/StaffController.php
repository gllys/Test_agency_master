<?php

Yii::import('ext.phpPasswordHashingLib.passwordLib', true);

class StaffController extends Controller {

	const PAGE_SIZE = 20;
	
    public function actionIndex() {
        $criteria = new CDbCriteria();
        $criteria->order = 'status DESC,id DESC';
        $criteria->condition = 'is_delete=0';

		// 分页信息
		$criteria->limit = self::PAGE_SIZE;
		$criteria->offset = ((isset($_GET['page']) ? $_GET['page'] : 0)-1)*20;
        
		$data['lists'] = Users::model()->findAll($criteria);
        $data['pages'] = new CPagination(Users::model()->count());
        $data['pages']->pageSize = self::PAGE_SIZE;
        $this->render('index', $data);
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
                $user->account = $_POST['account'];
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
                    $_POST['created_at'] = $_POST['updated_at'] = date('Y-m-d H:i:s');

                    $model = new Users();
                    $model->attributes = $_POST;

                    if ($model->save()) {
                        $roleUser = new RoleUser();
                        $roleUser->attributes = array('uid' => $model->id, 'role_id' => $_POST['role_id'], 'updated_at' => date('Y-m-d H:i:s'));
                        $roleUser->save();
                        $this->_end(0, '添加员工成功');
                    }
                    $this->_end(1, $model->getErrors());
                } catch (Exception $e) {
                    $this->_end(1, $e->getMessage());
                }
            }
        }
    }

    //删除员工
    public function actionDel() {
        if (Yii::app()->request->isPostRequest) {
	        $transaction = Yii::app()->db->beginTransaction();
			try{
				$id = $_POST['id'];
				$user = Users::model()->findByPk($id);
				$user->is_delete = 1;
				$user->deleted_at = date('Y-m-d h:i:s', time());;
				if($user->save()) {
					$roleUser = RoleUser::model()->findByAttributes(array('uid'=>$id));
					$roleUser->is_delete = 1;
					$roleUser->deleted_at = date('Y-m-d h:i:s', time());;
					if($roleUser->save()) {
						$transaction->commit();
						$this->_end('succ', '删除员工成功！');
					} else {
						$transaction->rollback();
						$this->_end('fail', $roleUser->getErrors());
					}
				} else {
					$transaction->rollback();
					$this->_end('fail', $user->getErrors());
				}
			} catch(Exception $ex){
				$transaction->rollback();
				$this->_end('fail', $user->getErrors());
			}
        }
    }

}
