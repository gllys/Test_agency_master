<?php

class AccountController extends CController {

    public function actionAdd() {
        $result = new Users;
        $result->attributes = $_POST;
        if (isset($_POST['sell_role'])) {
            $result->sell_role = $_POST['sell_role'];
        }
        try {
            if ($result->save()) {
                $this->_end('succ', '用户添加成功');
            }
        } catch (Exception $ex) {
            
        }
        $this->_end('fail', print_r($result->errors, true));
    }

    //查看供应商账号是否已经存在
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

    //供应商账号列表
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
        $rs->id = $params['id'];
        $rs->account = isset($params['account']) ? $params['account'] : $rs['account'] ; 
        $rs->mobile = isset($params['mobile']) ? $params['mobile'] : $rs['mobile'] ; 
        $rs->status = isset($params['status']) ? $params['status'] : $rs['status'] ; 
        $rs->user_name = isset($params['name']) ? $params['name'] : $rs['name'] ; 
        $rs->password = isset($params['password']) ? $params['password'] : $rs['password'] ;
        if ($rs->update()) {
            $this->_end('succ', '用户添加成功');
        }else{
            $this->_end('fail', $rs->errors);

        }
    }

    // //添加及修改权限
    // public function actionRoleUserAdd(){
    //     $params = array();
    //     $roleUser = new RoleUser;
    //     $roleUser->attributes = $_POST;
    //     try {
    //         if ($roleUser->save()) {
    //             $this->_end('succ', '修改权限更改成功');
    //         }
    //     } catch (Exception $ex) {
            
    //     }
    //     $this->_end('fail', print_r($roleUser->errors, true));
    // }

    // //获取权限列表
    // public function actionRoleLists(){
    //     $params = array();
    //     $attrs = RoleUser::model()->attributes;
    //     foreach ($_POST as $key => $val) {
    //         if (array_key_exists($key, $attrs)) {
    //             $params[$key] = $val;
    //         }
    //     }
    //     $rs = RoleUser::model()->findAllByAttributes($params);
    //     $this->_end('succ', '成功', $rs);
    // }

    public function _end($error, $msg, $params = array()) {
        echo CJSON::encode(array('code' => $error, 'message' => $msg, 'data' => $params));
        Yii::app()->end();
    }



           


}
