<?php

Class Tree extends CApplicationComponent{
	public $redisKey = 'bbs_child_item_tree';
	private static $_model = null;
	
	public static function model($className = __CLASS__) {
		if(self::$_model === null) {
			self::$_model = new $className();
			self::$_model->redisKey = $_SERVER['HTTP_HOST'].'_child_item_tree';
		}
		return self::$_model;
	}
	
	/**
	 * 获取关系树
	 * return Array
	 */
	public function getTree() {
		if(Yii::app()->redis->get($this->redisKey) == '') {
			$tree = $this->createItemTree();
			Yii::app()->redis->set($this->redisKey, CJSON::encode($tree));
		}
		
		return CJSON::decode(Yii::app()->redis->get($this->redisKey));
	}
	
	// 获取我拥有的所有操作
	public function getMyOperations() {
		$auth = Yii::app()->authManager;
		$roles = $auth->getAuthItems(2, Yii::app()->user->id);
		$opreations = $auth->getAuthItems(0, Yii::app()->user->id);
		
		foreach($roles as $item) {
			$temp = $this->getOperationsOfRole($item->name);
			$opreations = array_merge($opreations, $temp);
		}
		return $opreations;
	}
	
	// 获取我拥有的所有角色
	public function getMyRoles() {
	header("Content-type:text/html;charset=utf-8");
		$myRoles = Yii::app()->authManager->getAuthItems(2, Yii::app()->user->id);
		$roles = array();
		foreach($myRoles as $item) {
			$temp = $this->getChildrensOfRole($item->name);
			$temp[$item->name] = $item->description ;
			$roles = array_merge($roles, $temp);
		}
		return $roles;
	}
	
	// 获取所有任务
	public function getAllTask() {
		$tree = $this->getTree();
		$return = array();

		foreach($tree['system_admin']['child'] as $item) {
			if($item['type'] == 1) {
				$return[$item['name']] = $item['desc'];
			}
		}
		
		return $return;
	}
	
	// 获取所有角色
	public function getAllRole() {
		$tree = $this->getTree();
		$return = array();

		foreach($tree['system_admin']['child'] as $item) {
			if($item['type'] == 2) {
				$return[$item['name']] = $item['desc'];
			}
		}
		
		return $return;
	}
	
	/**
	 * 获取 某个角色 或 任务 拥有的操作, 包含其 子类 拥有的操作
	 * @param String $roleName 角色name
	 * @param Array $tree 父子关系树
	 * return Array itemname=>description
	 */
	public function getOperationsOfRole($roleName, $tree = null) {
		if($tree===null) $tree = $this->getTree();
		$return = array();

		foreach($tree as $item) {
			if($item['type'] == 0) continue;
			
			if($item['name'] == $roleName) {
				$return = $this->getOperationsOfTree($item['child']);
			} else {
				$temp = $this->getOperationsOfRole($roleName, $item['child']);
				$return = array_merge($return, $temp);
			}
		}
		
		return $return;
	}
	
	/**
	 * 获取 某个角色 或 任务 拥有的操作，不包含其子类的操作
	 * @param String $roleName 角色name
	 * @param Array $tree 父子关系树
	 * return Array itemname=>description
	 */
	public function getOwnOperationsOfRole($roleName, $tree=null) {
		if($tree===null) $tree = $this->getTree();
		$return = array();
		foreach($tree as $item) {
			if($item['type'] == 0) continue;
			
			if($item['name'] == $roleName) {
				foreach($item['child'] as $key => $child) {
					if($child['type'] == 0) $return[$key] = $child['desc'];
				}
				break;
			}

			$temp = $this->getOwnOperationsOfRole($roleName, $item['child']);
			$return = array_merge($return, $temp);
			if(!empty($return)) break;
		}
		
		return $return;
	}
	
	/**
	 * 获取某棵树的所有 叶子
	 * @param Array $tree 树
	 * return Array itemname=>description
	 */
	public function getOperationsOfTree($tree = null) {
		if($tree===null) $tree = $this->getTree();
		$return = array();

		foreach($tree as $item) {
			if($item['type'] > 0) {
				$temp = $this->getOperationsOfTree($item['child']);
				$return = array_merge($return, $temp);
			} else {
				$return[$item['name']] = $item['desc'];
			}
		}
		
		return $return;
	}
	
	/**
	 * 获取某个角色的所拥有的所有 子角色
	 * @param String $roleName 角色name
	 * @param Array $tree 父子关系树
	 * return Array 角色名=>角色描述
	 */
	public function getChildrensOfRole($roleName, $tree = null) {
		if($tree===null) $tree = $this->getTree();
		$return = array();
		
		foreach($tree as $item) {
			if($item['type'] != 2) continue;
			if($item['name'] == $roleName) {
				foreach($item['child'] as $v) {
					if($v['type'] == 1) $return[$v['name']] = $v['desc'];
				}
			}
			
			$temp = $this->getChildrensOfRole($roleName, $item['child']);
			$return = array_merge($return, $temp);
		}
		
		return $return;
	}
	
	/**
	 * 获取某角色的所有 父角色
	 * @param String $roleName 角色name
	 * @param Array $tree 父子关系树
	 * return Array 角色名=>角色描述
	 */
	public function getParentsOfRole($roleName, $tree = null) {
		if($tree===null) $tree = $this->getTree();
		$return = array();

		foreach($tree as $item) {
			if($item['type'] != 2) continue;
			
			if($item['name'] == $roleName) {
				$return[$item['name']] = $item['desc'];
			} else {
				$temp = $this->getParentsOfRole($roleName, $item['child']);
				if(!empty($temp)) {
					$return[$item['name']] = $item['desc'];
					$return = array_merge($return, $temp);
				}
			}
		}
		
		return $return;
	}
	
	/**
	 * 我拥有管理权限的角色中，能作为当前角色子类的 其他角色
	 * @param String $roleName 角色名称
	 * @return Array 角色名=>角色描述
	 */
	public function getRoleToRole($roleName) {
		$parentRoles = $this->getParentsOfRole($roleName);
		$myRoles = $this->getMyRoles();
		
		return array_diff($myRoles, $parentRoles);
	}
	
	// 刷新父子关系树
	public function refreshItemTree() {
		Yii::app()->redis->set($this->redisKey, '');
		$this->getTree();
		return true;
	}
	
	// 检测某个权限项是否存在
	public function checkItemExists() {
		
	}
	
	// 读取数据, 生成父子关系树
	private function createItemTree($name = 'system_admin', $description = '系统管理员', $type = 2) {
		$list = Yii::app()->authManager->getItemChildren($name);
		$tree = array();
		
		foreach($list as $item) {
			$tree[$name]['desc'] = $description;
			$tree[$name]['name'] = $name;
			$tree[$name]['type'] = $type;
			
			$temp = array();
			$temp['desc'] = $item->description;
			$temp['name'] = $item->name;
			$temp['type'] = $item->type;

			if($item->type > 0) {
				$temp['child'] = $this->createItemTree($item->name, $item->description, $item->type);
			}
			
			$tree[$name]['child'][$item->name] = isset($temp['child'][$item->name]) ? $temp['child'][$item->name] : $temp;
		}
		
		return $tree;
	}
	
	
}
