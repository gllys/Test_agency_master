<?php

class AccountController extends CController {

    public function actionAdd() {
        $result = new Users;
		$_POST['repassword'] = $_POST['password'];
        $result->attributes = $_POST;
        try {
            if ($result->save()) {
                if ($_SERVER['REQUEST_TIME'] < 1422720001) { // 2015-02-01 00:00:01
                    Yii::log("添加用户:\n" . print_r($_SERVER, true), 'info', 'account');
                    Yii::log("添加用户:\n" . print_r($_REQUEST, true), 'info', 'account');
                }
                $this->_end('succ', '用户添加成功');
            }
        } catch (Exception $ex) {
            
        }
        $this->_end('fail', print_r($result->errors, true));
    }

    //查看分销商账号是否已经存在
    public function actionSearch() {
        $params = array();
        $attrs = Users::model()->attributes;
        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $attrs)) {
                $params[$key] = $val;
            }
        }
		
        $rs = Users::model()->findByAttributes($params);
        $this->_end('succ', $rs);
    }

    //得到用户等级
    public function actionRole() {
        $id = $_POST['id'] ;
        $user = Users::model()->findByPk($id);
        #超级管理员
        if ($user['is_super']) {
            $this->_end('succ', array('name'=>'超级管理员'));
        }
        
        $userRole =  RoleUser::model()->findByAttributes(array('uid' => $id));
        if(!$userRole){
            $this->_end('succ', array('name'=>'未分配角色'));
        }
        $role = Role::model()->findByPk($userRole['role_id']);
        $this->_end('succ', $role);
    }
    
	/**
	 * @author xuejian
	 * @desc 	//得到多个用户的角色名称
	 * @return json返回 获取uid与
	 */
    public function actionRoles() {
        $attrs = Users::model()->attributes;
		$ucriteria = new CDbCriteria();
        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $attrs)) {
				$ucriteria->addCondition($key."=:".$key);
				$ucriteria->params[":".$key] = $val;
            }
        }
		
		// 建立查询criteria的sql
		
		// ids设置
		$ids = null;
		if(isset($_POST['ids'])) {
			$ids = explode(',', $_POST['ids']);
			$ucriteria->addInCondition('id', $ids);
		}
		
		// 是否传入column过滤fields
		$ucriteria->select = array('id', 'is_super');
		
		// 一定查询成功吗
        $urs = Users::model()->findAll($ucriteria);
		// 结果返回数据保存
		$datas = array();
		if($urs && is_array($urs)) {
			foreach ($urs as $key=>$val) {
				if($val['is_super']) {
					$datas[$val['id']] = array("uid"=>$val['id'], "name"=>"超级管理员".$val['is_super']);
				}
			}
		} else{
			$this->_end('fail', "未获得用户角色数据，请确认传入参数".print_r($_POST, true));
		}

		// 查询用户和角色的关联表
		$rucriteria = new CDbCriteria();
		if(!empty($ids)) {
			$rucriteria->addInCondition("uid", $ids);
		}
		$rucriteria->select = array('uid', 'role_id');
		$rurs = RoleUser::model()->findAll($rucriteria);
		if($rurs && is_array($rurs)) {
			foreach ($rurs as $key=>$val) {
				$role = Role::model()->findByPk($val['role_id']);
				if($role) {
					$datas[$val["uid"]] = array("uid"=>$val["uid"], "name"=>$role["name"]);
				}
			}
		} 
		
		$this->_end('succ', "成功", $datas);
    }
	
    //供应商账号列表
    public function actionLists() {
        $attrs = Users::model()->attributes;

		// 建立查询criteria的sql
		$criteria = new CDbCriteria();
        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $attrs)) {
				$criteria->addCondition($key."=:".$key);
				$criteria->params[":".$key] = $val;
            }
        }
				
		// 是否传递ids选项\
		if(isset($_POST['ids'])) {
			$ids = explode(',', $_POST['ids']);
			$criteria->addInCondition('id', $ids);
		}
		
		// 是否传入column过滤fields
		$fields = null;
		if(isset($_POST['fields'])) {
			$fields = explode(',', $_POST['fields']);
			$criteria->select = $fields;
		}
		// 一定查询成功吗
        $rs = Users::model()->findAll($criteria);
		if($rs && is_array($rs)) {
			$datas = $this->filterFields($rs, $attrs, $fields);
	        $this->_end('succ', '成功', $datas);;
		} else {
	        $this->_end('fail', '失败');
		}
    }

    /*
     * 电子票务账号列表页
     */
    public function actionTicketLists() {
        $params = array();
        $rs = Users::model()->attributes;
        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $rs)) {
                $params[$key] = $val;
            }
        }

        $rs = Users::model()->findAllByAttributes($params);
        if(!empty($rs)){
            $this->_end('succ','成功',$rs);
        }else{
            $this->_end('fail',$rs->errors);
        }
    }

    //供应商修改
    public function actionUpdate() {
        $params = array();
        $rs = Users::model()->attributes;
        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $rs)) {
                $params[$key] = $val;
            }
        }

        $rs = Users::model()->findByPk($params['id']);
        $rs->name  = isset($params['name']) ? $params['name'] : $rs['name'];
        $rs->account  = isset($params['account']) ? $params['account'] : $rs['account'];
        $rs->mobile   = isset($params['mobile']) ? $params['mobile'] : $rs['mobile'];
        if(isset($params['password']) && !empty($params['password']) && !empty($params['password'])){
            $rs->password = $params['password'];
        }
        if(isset($params['password_str']) && !empty($params['password_str']) && !empty($params['password_str'])){
            $rs->password_str = $params['password_str'];
        }
        $rs->status   = isset($params['status']) ? intval($params['status']) : intval($rs['status']);
        if ($rs->update()) {
            if ($_SERVER['REQUEST_TIME'] < 1422720001) { // 2015-02-01 00:00:01
                Yii::log("修改用户:\n" . print_r($_SERVER, true), 'info', 'account');
                Yii::log("修改用户:\n" . print_r($_REQUEST, true), 'info', 'account');
            }
            $this->_end('succ', '用户添加成功');
        }else{
            $this->_end('fail', $rs->errors);

        }
    }

    //批量修改供应商账号
    public function actionStatus(){
        $params = array();
        $attrs = Users::model()->attributes;
        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $attrs)) {
                $params[$key] = $val;
            }
        }
        $rs = Users::model()->findAllByAttributes($params);
        if(!empty($rs) && is_array($rs)){
            $i = 0;
            foreach ($rs as $value) {
                $userRs = Users::model()->findByPk($value['id']);
                $userRs->updated_at = date('Y-m-d h:i:s',time());
                $userRs->status = 0;
                $result = $userRs->update();
                if($result){
                    $i++;
                }
            }
            if($i > 0){
                $this->_end('succ','保存成功');
            }
        }else{
            $this->_end('succ','无账号需要修改');
        }
    }


    //批量修改供应商账号
    public function actionLandscape() {
        $params = array();
        $rs = Users::model()->attributes;
        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $rs)) {
                $params[$key] = $val;
            }
        }
        $rs = Users::model()->findAllByAttributes(array('organization_id' => $params['organization_id'], 'sell_role' => 'scenic'));
        if(is_array($rs) && !empty($rs)){
            $i = 0;
            foreach ($rs as $value) {
                $userRs = Users::model()->findByPk($value['id']);
                if(substr($value['account'], 0, 2) != 'jq'){
                    //$userRs->landscape_id = $params['landscape_id'];
                    $userRs->updated_at = date('Y-m-d h:i:s',time());
                    $userRs->status = 1;
                    $result = $userRs->update();
                    if($result){
                        $i++;
                    }
                    if($i > 0){
                        $this->_end('succ', '修改成功');
                    }else{
                        $this->_end('fail', '修改失败');
                    }
                }else{
                    $this->_end('succ', '无需修改');
                }
            }
        }else{
            $this->_end('succ', '该机构为批发商机构，无需修改账号景区ID');
        }
    }


    public function _end($error, $msg, $params = array()) {
        echo CJSON::encode(array('code' => $error, 'message' => $msg, 'data' => $params));
        Yii::app()->end();
    }
	
	/* 过滤$rs中不是fields的值 */
	private function filterFields($rs, $attrs=array(), $fields=array()) {
		$datas = array();
		if((!is_array($fields)) || empty($fields)) {
			$fields = array_keys($attrs);
		}
		$i = 0;
		foreach ($rs as $val) {
			foreach($fields as $attr) {
				if (array_key_exists($attr, $attrs)) {
					$datas[$val['id']][$attr] = $rs[$i][$attr];
				}
			}
			$i ++;
		}
		
		return $datas;
	}

}
