<?php

class AccountController extends CController {

    public function actionAdd() {
        $result = new Users;
        $result->attributes = $_POST;
        try {
            if ($result->save()) {
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

    public function _end($error, $msg, $params = array()) {
        echo CJSON::encode(array('code' => $error, 'message' => $msg,'data'=>$params));
        Yii::app()->end();
    }

}
