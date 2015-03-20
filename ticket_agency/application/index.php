<?php
date_default_timezone_set('PRC');
$xhprof_enable = false;
if (isset($_GET['x'])) {
	xhprof_enable(true);
	$xhprof_enable = true;
}
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
$app = Yii::createWebApplication($config);
Yii::import('application.extensions.PHPExcel');
require_once "PHPExcel.php";
require_once "PHPExcel/Autoloader.php";
Yii::registerAutoloader(array('PHPExcel_Autoloader','Load'), true);
$app->run();

if ($xhprof_enable) {
	$data = xhprof_disable(); //返回运行数据
	// xhprof_lib在下载的包里存在这个目录,记得将目录包含到运行的php代码中
	include_once "/data/web/xhprof/xhprof_lib/utils/xhprof_lib.php";
	include_once "/data/web/xhprof/xhprof_lib/utils/xhprof_runs.php";

	$objXhprofRun = new XHProfRuns_Default();

	// 第一个参数j是xhprof_disable()函数返回的运行信息
	// 第二个参数是自定义的命名空间字符串(任意字符串),
	// 返回运行ID,用这个ID查看相关的运行结果
	$run_id = $objXhprofRun->save_run($data, "xhprof");
	echo "<a href='http://xhprof.ihuilian.com/xhprof_html/?run=" . $run_id . "' target='_blank' />查看</a>";
}
