<?php

error_reporting(7);
//ini_set('display_errors', 'on');
define('APPLICATION_PATH', realpath('../'));
require (APPLICATION_PATH . '/../setting/ticket_organization.php');

try {
    $app = new Yaf_Application(APPLICATION_PATH . "/conf/base.ini");
    $app->bootstrap()->run();
}
catch(Exception $e) {
    echo $e->getMessage();
}
