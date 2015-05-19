<?php
/**
 * This is the bootstrap file for test application.
 * This file should be removed when the application is deployed for production.
 */
date_default_timezone_set('PRC');

// change the following paths if necessary
$path = dirname(__FILE__);

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
defined('JSON_UNESCAPED_UNICODE') or define('JSON_UNESCAPED_UNICODE', 256);
require_once($path . '/../framework/yii.php');
Yii::createWebApplication($path . '/protected/config/main.php')->run();
