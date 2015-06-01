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

        //渠道分发
        $config = Yaf_Registry::get('config');
        if(isset($params['source'])){
            if(isset($config['agency_dispatch'][$params['source']])){
                $req->module = $config['agency_dispatch'][$params['source']];
            }else{
                $req->module = 'V1_1';
            }
        }

        //指定动作及其文件类对应的路径
        $this->actions = array(
            $req->action => 'modules/'.$req->module.'/actions/'.$partner.'/'.$action.'.php',
        );

        Util_Logger::getLogger('common')->info(__METHOD__,
            array('params' => $params, 'action' => $this->actions), '', 'apidispatch'
        );
    }

}