<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-26
 * Time: 下午2:21
 */
class ReverseAction extends Yaf_Action_Abstract{

    /**
     * 冲正申请回调接口，提供给内部系统调用
     */
    public function execute(){

        Lang_Msg::error(json_encode("去哪儿暂不支持撤销核销服务！"));
    }
}