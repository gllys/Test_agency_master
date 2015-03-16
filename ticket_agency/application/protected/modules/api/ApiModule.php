<?php

class ApiModule extends CWebModule {

    public function init() {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'api.models.*',
            'api.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action) {
        $_POST = $_POST + $_GET;
        if (!$this->checkVerify($_POST, $_POST['sign'])) {
            echo CJSON::encode(array('code' => 'fail','message'=>'校验码错误')); //校验码错误
            Yii::app()->end();
        }
        return true;
    }

    private function checkVerify($data, $sign) {
        unset($data['sign']);
        ksort($data);
        if(empty(Yii::app()->params['agency-url'])||empty(Yii::app()->params['agency-url']['sign'])){
            echo CJSON::encode(array('code' => 'fail','message'=>'校验码不存在')); //校验码错误
            Yii::app()->end();
        }
        $v = md5(http_build_query($data) .Yii::app()->params['agency-url']['sign']);
        return $v == $sign;
    }

}
