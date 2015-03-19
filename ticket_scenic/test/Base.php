<?php

error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));
define('APPLICATION_PATH', realpath(dirname(__FILE__).'/../'));

require (APPLICATION_PATH . '/../setting/ticket_scenic.php');
$app = new Yaf_Application( APPLICATION_PATH . "/conf/base.ini");
