<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-6-3
 * Time: 下午1:39
 */
class Base_Action_Abstract extends Yaf_Action_Abstract{

    public function execute(){}

    public function errorLog($msg, $searchKey = '', $category = '', $logData = NULL){
        $module = strtolower($this->getRequest()->module);
        $method = ucfirst($this->getRequest()->action).'Action::execute';
        if($logData === NULL){
            $logData = $msg;
        }
        Util_Logger::getLogger($module)->error($method, $logData, '', $category, $searchKey);
        Lang_Msg::error($msg);
    }
}