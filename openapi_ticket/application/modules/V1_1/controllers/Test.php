<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-15
 * Time: 上午11:36
 * Used: 测试使用
 */
class TestController extends Base_Controller_ApiDispatch
{

    //若访问：~/V1/Test/index2?sign=debug
    //因对应控制器中已有相应动作，所有优先访问控制器中的动作，
    //而不会访问动作分发器下的动作
    public function index2Action(){
        echo 'TestController indexAction';
        var_dump($this->actions);
    }

    public function test2Action(){

    }


}
