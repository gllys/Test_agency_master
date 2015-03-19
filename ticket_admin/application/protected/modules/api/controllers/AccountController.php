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

    //分销商账号列表
    public function actionLists() {
        $params = array();
        $attrs = Users::model()->attributes;
        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $attrs)) {
                $params[$key] = $val;
            }
        }
        $rs = Users::model()->findAllByAttributes($params);
        $this->_end('succ', '成功', $rs);
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
    public function actionStatus() {
        $params = array();
        $rs = Users::model()->attributes;
        foreach ($_POST as $key => $val) {
            if (array_key_exists($key, $rs)) {
                $params[$key] = $val;
            }
        }
        $rs = Users::model()->findAllByAttributes(array('organization_id' => $params['organization_id'], 'landscape_id' => $params['landscape_id']));
        if(is_array($rs)){
            foreach ($rs as $value) {
                $userRs = Users::model()->findByPk($value['id']);
                $userRs->status = 0;
                $userRs->updated_at = date('Y-m-d h:i:s',time());
                $result = $userRs->update();
            }
            if($result){
                $this->_end('succ', '修改成功');
            }else{
                $this->_end('fail', '修改失败');
            }
        }else{
            $this->_end('succ', '无需修改');
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
        if(is_array($rs)){
            foreach ($rs as $value) {
                $userRs = Users::model()->findByPk($value['id']);
                if(substr($value['account'], 0, 2) != 'jq'){
                    $userRs->landscape_id = $params['landscape_id'];
                    $userRs->updated_at = date('Y-m-d h:i:s',time());
                    $userRs->status = 1;
                    $result = $userRs->update();
                }
            }
            if($result){
                $this->_end('succ', '修改成功');
            }else{
                $this->_end('fail', '修改失败');
            }
        }else{
            $this->_end('succ', '该机构为批发商机构，无需修改账号景区ID');
        }
    }


    public function _end($error, $msg, $params = array()) {
        echo CJSON::encode(array('code' => $error, 'message' => $msg, 'data' => $params));
        Yii::app()->end();
    }

}
