<?php
require dirname(__FILE__) . '/Base.php';

class Test extends Util_Test
{
    protected $method = 'GET';
    protected $module = 'index';
    protected $controller = 'index';
    protected $action = 'index';
    protected $params = array();
}

$test = new Test();
