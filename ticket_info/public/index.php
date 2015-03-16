<?php
// error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
error_reporting(0);
ini_set('date.timezone','PRC');
//ini_set('display_errors', 'on');
define('APPLICATION_PATH', realpath('../'));
require (APPLICATION_PATH . '/../setting/ticket_info.php');
if (defined('XHPROF_OPEN') && XHPROF_OPEN) require (APPLICATION_PATH . '/xhprof.php');

try {
    $app = new Yaf_Application(APPLICATION_PATH . "/conf/base.ini");
    $app->bootstrap()->run();
}
catch(Exception $e) {
    echo $e->getMessage();
}
