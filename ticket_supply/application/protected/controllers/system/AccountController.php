<?php

Yii::import('ext.phpPasswordHashingLib.passwordLib', true);

class AccountController extends Controller {

    public function actionIndex() {
        $info = '';
        $fields = array('id', 'account', 'name', 'password');
        $results = Users::model()->find('account=:account', array(':account' => Yii::app()->getUser()->account));
        foreach ($fields as $field) {
            $result[$field] = $results->$field;
        }
        //判断数据库是否提交
        if (isset($_POST['old']) && !empty($_POST['old'])) {
            $password = trim($_POST['old']);
            if (!password_verify($password, $result['password'])) {
                // 旧密码不正确
                echo json_encode(array('errors' => array('msg' => "原始密码错误！")));
            } else {
                // 修改数据库密码
                $results->password = password_hash($_POST['assword'], PASSWORD_BCRYPT, array('cost' => 8));
                if ($results->save()) {
                    echo 1;
                } else {
                    echo json_encode(array('errors' => array('msg' => "修改密码错误！")));
                }
            }
        } else {
            $this->render('index');
        }
    }
    


}
