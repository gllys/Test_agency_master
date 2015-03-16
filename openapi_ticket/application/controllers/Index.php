<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends Base_Controller_Abstract
{
    public function indexAction() {
    	header("HTTP/1.0 404 Not Found");exit();
    }

}
