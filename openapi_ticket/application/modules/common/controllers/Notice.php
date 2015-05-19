<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-9
 * Time: 下午2:45
 */

//做统一的动作分发使用
class NoticeController extends Base_Controller_ApiDispatch{


    public function init(){
        parent::init(false);

        self::echoLog('body', var_export($this->body, true), 'common_order.log');
    }

}
