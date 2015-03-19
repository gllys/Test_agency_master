<?php
/**
 * This is the bootstrap file for test application.
 * This file should be removed when the application is deployed for production.
 */
date_default_timezone_set('PRC');
// change the following paths if necessary
$yii    = dirname(__FILE__) . '/../framework/yii.php';
$config = dirname(__FILE__) . '/protected/config/main-test.php';

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', true);
if (strnatcmp(phpversion(), '5.4.0') < 0) {
	define('JSON_UNESCAPED_UNICODE', 256);
}

require_once($yii);
Yii::createWebApplication($config)->run();
