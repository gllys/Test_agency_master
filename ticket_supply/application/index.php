<?php
date_default_timezone_set('PRC');
if (!function_exists('boolval')) {
	function boolval($var) {
		return (bool)$var;
	}
}
// change the following paths if necessary
$yii    = dirname(__FILE__) . '/../framework/yii.php';
$config = dirname(__FILE__) . '/protected/config/main.php';
// remove the following lines when in production mode
//defined('YII_DEBUG') or define('YII_DEBUG', true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
defined('JSON_UNESCAPED_UNICODE') or define('JSON_UNESCAPED_UNICODE', 256);
require_once($yii);
Yii::createWebApplication($config)->run();

