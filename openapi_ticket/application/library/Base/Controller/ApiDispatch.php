<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-19
 * Time: 下午6:24
 * Used: 动作分发器
 */
class Base_Controller_ApiDispatch extends Base_Controller_Ota{

    public $actions;

    public function init($need_sign = true){
        parent::init($need_sign);

        $req = $this->getRequest();
        $params =$this->getParams();
        $action = $req->controller.ucfirst($req->action);
        $partner = isset($params['partner']) ? empty($params['partner']) : '';

        if(isset($params['source'])){
            if($params['source'] == 1)
                $req->module = 'Taobao';
            if($params['source'] == 10)
                $req->module = 'Qunar';
        }

        //指定动作及其文件类对应的路径
        $this->actions = array(
            $req->action => 'modules/'.$req->module.'/actions/'.$partner.'/'.$action.'.php',
        );

        self::echoLog('body', var_export($params, true), 'apidispatch_bee.log');
        self::echoLog('body', var_export($this->actions, true), 'apidispatch_bee.log');
    }

}