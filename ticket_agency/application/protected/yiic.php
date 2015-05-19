<?php
date_default_timezone_set('PRC');
// change the following paths if necessary
$yiic=dirname(__FILE__).'/../../framework/yii.php';
$config=dirname(__FILE__).'/config/console.php';
require_once($yiic);
$app = Yii::createConsoleApplication($config);
// adding PHPExcel autoloader
Yii::import('application.vendors.*');
require_once "PHPExcel/PHPExcel.php";
require_once "PHPExcel/PHPExcel/Autoloader.php";
Yii::registerAutoloader(array('PHPExcel_Autoloader','Load'), true);
$app->run();

