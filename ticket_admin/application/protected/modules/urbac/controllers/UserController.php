<?php
class UserController extends Controller {
	
        public $layout='//layouts/column2';
	// 组用户列表
	public function actionIndex($name) {
		$this->hasItem($name);
		$users = $this->getUsers($name);
		$allUser = Member::model()->findAll('status=1');
		$this->render('index', array('users'=>$users, 'allUser'=>$allUser, 'name'=>$name));
	}
	
	public function actionManageUser() {
		
		$allUser = Member::model()->findAll();
		$this->render('manage_user', array('allUser'=>$allUser));
	}
	
	// 给组分配用户用户
	public function actionSaveUser($name) {
		if(!$this->hasItem($name, false)) $this->_end(1, '分组错误.');
		
		$item = Yii::app()->request->getPost('item');
		if(!empty($item)) {
			$users = $this->getUsers($name);
			
			$del = array_diff($users, $item);
			$add = array_diff($item, $users);
			
			foreach($add as $itemName) {
				Yii::app()->authManager->assign($name, $itemName);
			}
			foreach($del as $itemName) {
				Yii::app()->authManager->revoke($name, $itemName);
			}
			$this->_end(0, '操作成功.');
		}
		$this->_end(1, '没有选择用户!');
	}
	
	// 添加用户
	public function actionAddUser() {
		$account = Yii::app()->request->getPost('account');
		
		if(!empty($account)) {
			$username = Yii::app()->request->getPost('username', $account);
			
			$criteria = new CDbCriteria;
			$criteria->compare('account', $account);
			$model = Member::model();
			
			$member = $model->find($criteria);
			if(!empty($member)) $this->_end(1, '用户已存在!');
			
			$model->attributes = array(
				'account'=> $account,
				'username'=> $username,
				'display_name'=> $username,
				'create_time'=> time()
			);
			$model->setIsNewRecord(true);
			$model->save() ? $this->_end(0, '添加成功.') : $this->_end(1, '添加失败!');
		}
		$this->_end(1, '用户帐号不能为空！');
	}
	
	public function actionDelUser() {
		$item = Yii::app()->request->getPost('item');
		
		if(!empty($item)) {
			foreach($item as $itemName) {
				$criteria=new CDbCriteria;
				$criteria->compare('account', $itemName);
				
				$user = Member::model()->find($criteria);
				if(!empty($user)) {
					$user->status = 0;
					$user->save();
				}
			}
			
			$this->_end(0, '停用成功.');
		}
		$this->_end(1, '没有选择用户!');
	}
	
	public function actionOpenUser() {
		$item = Yii::app()->request->getPost('item');
		
		if(!empty($item)) {
			foreach($item as $itemName) {
				$criteria=new CDbCriteria;
				$criteria->compare('account', $itemName);
				
				$user = Member::model()->find($criteria);
				if(!empty($user)) {
					$user->status = 1;
					$user->save();
				}
			}
			
			$this->_end(0, '启用成功.');
		}
		$this->_end(1, '没有选择用户!');
	}
	
	// 授权
	public function actionRelation($name) {
		$roleDescription = $this->hasItem($name);
		
		$itemTree = Tree::model();
		
		// 当前登录用户所拥有的权限
		$operations = $itemTree->getMyOperations();
		$roles = $itemTree->getRoleToRole($name);
		
		
		// 当前角色拥有的操作权限
		$hasOperations = $itemTree->getOwnOperationsOfRole($name);
		$hasRoles = $itemTree->getChildrensOfRole($name);
		
		$allTask = $itemTree->getAllTask(); //所有的任务
		$tasks = array();
		$tree = array();
		
		foreach($allTask as $taskName=> $taskDesc) {
			$tasks[$taskName] = $itemTree->getOperationsOfRole($taskName);
			
			// 取交集，分组我拥有的权限
			$tasks[$taskName] = array_intersect_key($tasks[$taskName], $operations);
			
			$tree[$taskName] = array('name'=> $taskName, 'description'=> $taskDesc);
			
			foreach($tasks[$taskName] as $opName => $description) {
				$arr = explode('.', strtolower($opName));
				$action = array_pop($arr);
				$controller = array_pop($arr);
				
				$temp = array('name'=> $opName, 'description'=> $description, 'has'=> isset($hasOperations[$opName]) ? true : false);
				if($action == 'index') {
					$tree[$taskName]['child'][$controller][$action] = $temp;
					$temp['description'] = '首页';
				}
				$tree[$taskName]['child'][$controller]['child'][$action] = $temp;
			}
		}
		
		$this->render('relation',array('name'=>$name, 'description'=>$roleDescription, 'tree'=>$tree, 'roles'=>$roles, 'hasRoles'=>$hasRoles));
	}
	
	// 给角色授权
	public function actionChangeRelation($name) {
		if(Yii::app()->request->isPostRequest) {
			if(!isset($_POST['operations'])) $_POST['operations'] = array();
			if(!isset($_POST['roles'])) $_POST['roles'] = array();
	
			$itemTree = Tree::model();
			// 当前登录用户所拥有的权限
			$operations = $itemTree->getMyOperations();
			$roles = $itemTree->getRoleToRole($name);
			
			// 当前角色拥有的操作权限
			$hasOperations = $itemTree->getOwnOperationsOfRole($name);
			$hasRoles = $itemTree->getChildrensOfRole($name);
			
			// 过滤掉当前登录用户 不具有的权限项
			$roles = array_intersect_key($_POST['roles'], $roles);
			$operations = array_intersect_key($_POST['operations'], $operations);
			
			// 角色拥有的，但提交表单没有的，需要删除
			$toRemoveChildren = array_diff_key($hasOperations, $operations);
			$toRemoveChildren += array_diff_key($hasRoles, $roles);
			
			// 角色不拥有的，但提交表单有的，需要添加
			$toAddChildren = array_diff_key($operations, $hasOperations);
			$toAddChildren += array_diff_key($roles, $hasRoles);
			
			$auth = Yii::app()->authManager;
			foreach($toRemoveChildren as $opName => $v) {
				$auth->removeItemChild($name, $opName);
			}
			foreach($toAddChildren as $opName => $v){
				$auth->addItemChild($name, $opName);
			}
			$itemTree->refreshItemTree();
		}
		$this->redirect('/urbac/user/relation/name/'.$name);
	}
	
	private function getUsers($name) {
		$criteria=new CDbCriteria;
		$criteria->compare('itemname', $name);
		$list = KfAuthAssignment::model()->findAll($criteria);
		$users = array();
		foreach($list as $item) {
			$users[] = $item->userid;
		}
		return $users;
	}
	
}
?>