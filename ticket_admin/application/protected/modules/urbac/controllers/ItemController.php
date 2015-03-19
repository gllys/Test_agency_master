<?php
/**
 * 权限项管理
 * @author $Author: chengjian $
 * @copyright Copyright &copy; 2009-2011 uuzu
 * @version $Id: ItemController.php 57735 2011-11-18 12:18:52Z chengjian $
 *
 */
class ItemController extends Controller {
        public $layout='//layouts/column2';
	private $itemChildData = '';
	private $itemChildTree = array();
	private $updateItemModel = null;
	
	public function actionIndex() {
		$this->render('index');
	}

	// 创建角色
	public function actionCreateRole() {
		if(Yii::app()->request->isPostRequest) {
			$description = Yii::app()->request->getPost('role_name');
			if(!empty($description)) {
				$auth = Yii::app()->authManager;
				$name = 'role_'.(microtime(true)*100).rand(111,999);
				$auth->createRole($name, $description);
				$auth->addItemChild('system_admin', $name);
				$this->addAuthForMe($name);
				Tree::model()->refreshItemTree();
			}
		}
		$this->redirect('/urbac/item/');
	}
	
	// 修改角色描述
	public function actionSaveRoles() {
		if(Yii::app()->request->isPostRequest) {
			$item = Yii::app()->request->getPost('item', null);
			if(empty($item)) $this->_end(1, '没有选择组!');
			
			foreach($item as $name) {
				$this->modifyItem($name, $_POST['desc'][$name], 2);
			}
			Tree::model()->refreshItemTree();
			$this->_end(0, '保存成功!');
		}
	}
	
	// 删除角色
	public function actionDelRoles() {
		if(Yii::app()->request->isPostRequest) {
			$item = Yii::app()->request->getPost('item', null);
			if(empty($item)) $this->_end(1, '没有选择组!');
			
			foreach($item as $name) {
				Yii::app()->authManager->removeAuthItem($name);
			}
			Tree::model()->refreshItemTree();
			
			$this->_end(0, '删除成功!');
		}
	}
	
	// 创建任务
	public function actionCreateTask() {
		if(Yii::app()->request->isPostRequest) {
			$description = Yii::app()->request->getPost('task_name');
			if(!empty($description)) {
				$name = 'task_'.(microtime(true)*100).rand(111, 999);
				$auth = Yii::app()->authManager;
				$auth->createTask($name, $description);
				$auth->addItemChild('system_admin', $name);
				$this->addAuthForMe($name);
				Tree::model()->refreshItemTree();
			}
			$this->redirect('/urbac/item/createTask');
		}
		
		$tasks = Tree::model()->getAllTask();
		
		$this->render('task', array('tasks'=>$tasks));
	}
	
	// 管理任务 修改、删除
	public function actionManageTask($type) {
		if(!Yii::app()->request->isPostRequest) {
			$this->_end(1, '请求错误!');
		}
		$item = Yii::app()->request->getPost('item', null);
		if(empty($item)) {
			$this->_end(1, '列表为空!');
		}
		
		$auth = Yii::app()->authManager;
		
		if($type == 'save') {
			foreach($item as $name) {
				$this->modifyItem($name, $_POST['desc'][$name], 1);
			}
			Tree::model()->refreshItemTree();
			$this->_end(0, '保存成功!');
			
		} else if($type == 'del') {
			foreach($item as $name) {
				$auth->removeAuthItem($name);
			}
			Tree::model()->refreshItemTree();
			$this->_end(0, '删除成功!');
		}
		
		$this->_end(1, '操作失败!');
	}

	// 自动创建操作
	public function actionAutoCreate() {
		$auth = Yii::app()->authManager;
		// 提交
		if(Yii::app()->request->isPostRequest && isset($_POST['item'])) {
			$parent = $_POST['parent'];
			foreach($_POST['item'] as $item) {
				$auth->createOperation($item, $_POST['desc'][$item]);
				
				if(isset($parent[$item]) && !empty($parent[$item])) {
					$auth->addItemChild($parent[$item], $item);
					$this->addAuthForMe($item);
				}
			}
			
			Tree::model()->refreshItemTree();
			$this->redirect('/urbac/item/autoCreate');
		}
		
		$path = Yii::getPathOfAlias("application.controllers");
		$list = $this->getActionList($path, $path);

		$items = $auth->getAuthItems();
		$data = array();
		foreach($items as $item){
			$data[$item->name] = $item->name;
		}
		$list = array_diff($list, $data);
		$this->render("autocreate",array('list'=>$list));
	}
	
	public function actionCreateOperation() {
		if(isset($_POST['operation_name']) && !empty($_POST['operation_name'])) {
			$desc = empty($_POST['operation_desc']) ? $_POST['operation_name'] : $_POST['operation_desc'];
			Yii::app()->authManager->createOperation($_POST['operation_name'], $desc);
			Tree::model()->refreshItemTree();
		}
		$this->redirect('/urbac/item/autoCreate');
	}
	
	// 管理已添加的操作
	public function actionManageAction() {
		$auth = Yii::app()->authManager;
		if(Yii::app()->request->isPostRequest && isset($_POST['item'])) {
			$type = Yii::app()->request->getParam('type');
			
			if($type == 'save') {
				foreach($_POST['item'] as $name) {
					$this->modifyItem($name, $_POST['desc'][$name]);
					
					if(isset($_POST['oldparent'][$name]) && !empty($_POST['oldparent'][$name])) {
						$auth->removeItemChild($_POST['oldparent'][$name], $name);
					}
					if(isset($_POST['parent'][$name]) && !empty($_POST['parent'][$name])) {
						$auth->addItemChild($_POST['parent'][$name], $name);
					}
				}
				Tree::model()->refreshItemTree();
				$this->_end(0, '保存成功!');
			} else {
				foreach($_POST['item'] as $name) {
					$auth->removeAuthItem($name);
				}
				Tree::model()->refreshItemTree();
				$this->_end(0, '删除成功!');
			}
			$this->_end(0, '操作成功!');
		}
		
		$items = $auth->getAuthItems(0);
		$this->render("manageaction", array('list'=>$items));
	}

	private function getActionList($basePath, $path){
		$d = dir($path);
		$list = array();
		while (false !== ($entry = $d->read())) {
			$fullPath = $path.'/'.$entry;
			if(is_dir($fullPath) && $entry != '.' && $entry != '..') {
				$subList = $this->getActionList($basePath, $fullPath);
				$list = array_merge($list, $subList);
			} elseif(is_file($fullPath) && substr($fullPath,-14)=="Controller.php") {
				$id = trim(substr($fullPath,strlen(trim($basePath,"/")."/"),-14),'/');
				$contrArr = explode("/", $id);
				$contrArr[sizeof($contrArr)-1] = ucfirst($contrArr[sizeof($contrArr)-1]);
				$controller = implode(".", $contrArr);

				$class = substr($entry,0,-4);
				include_once($fullPath);
				$object = new $class($id);
				$ref = new ReflectionClass($class);
				$methods = $ref->getMethods();
				foreach($methods as $method){
					if($method->name=='actions') {
						$actions = array_keys($object->actions());
						foreach($actions as $action){
							$access = $controller.'.'.ucfirst($action);
							$list[$access] = $access;
						}
					} elseif(substr($method->name, 0, 6)=='action') {
						$action = substr($method->name, 6);
						$access = $controller.'.'.ucfirst($action);
						$list[$access] = $access;
					}
				}
			}
		}
		$d->close();
		return $list;
	}
	/**
	 * 更新
	 */
	private function modifyItem($name, $description, $type = 0) {
		if(empty($name)) return;
		
		if($this->updateItemModel === null) {
			$this->updateItemModel = new AuthItemForm('update');
		}
		$auth = Yii::app()->authManager;
		$model = $this->updateItemModel;
		$model->unsetAttributes();
		
		$model->name = $name;
		$model->description = empty($description) ? $name : $description;
		$model->type = $type;
		
		$item = $auth->getAuthItem($name);
		
		if($model->validate()) {
			$item->description = $model->description;
			$item->name = $model->name;
			$auth->saveAuthItem($item, $name);
		}
	}
	
	// 在添加操作、任务、角色的时候， 自我给我分配权限
	private function addAuthForMe($name) {
		$auth = Yii::app()->authManager;
		$assignments = $auth->getAuthAssignments(Yii::app()->user->id);
		
		foreach($assignments as $item) {
			if(!$auth->hasItemChild($item->itemname, $name)) $auth->addItemChild($item->itemname, $name);
		}
	}
	
}