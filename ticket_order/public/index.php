<?php

error_reporting(7);
ini_set('display_errors', 'on');
date_default_timezone_set('PRC');
define('APPLICATION_PATH', realpath('../'));
require (APPLICATION_PATH . '/../setting/ticket_order.php');
if (defined('XHPROF_OPEN') && XHPROF_OPEN) require (APPLICATION_PATH . '/xhprof.php');

try {
    $app = new Yaf_Application(APPLICATION_PATH . "/conf/base.ini");
    $app->bootstrap()->run();
}
catch(Exception $e) {
    echo $e->getMessage();
}
