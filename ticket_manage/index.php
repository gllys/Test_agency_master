<?php
error_reporting(E_ALL & ~E_NOTICE); 
$xhprof_enable = false;
if(isset($_GET['x'])){
  xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
  $xhprof_enable = true;
}
/**
 * 入口文件
 *
 * PI的目标高性质
 * 追求极致性能-优化到每个字节、每K内存、每毫秒执行时间
 * 严格要求质量-高标准、高要求、高质量
 * 配置文件详见Configs目录
 *
 * 2012-12-12 1.0 lizi 创建
 *
 * @author  lizi
 * @version 1.0
 */
// 是否显示错误信息:建议在开发时打开,上线时关闭
$debug = substr($_SERVER['REQUEST_URI'], -5) == 'debug' ? TRUE : FALSE;
define('PI_DEBUG', $debug);

//当前项目的根目录
define('PI_PROJECT_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);

// 当前项目应用程序的根目录
define('PI_APP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);

// 加载项目配置文件
require_once(PI_APP_ROOT.'Configs/Config.php');
require_once(PI_APP_ROOT.'Configs/Menu.php');
require_once(PI_APP_ROOT.'Configs/ApiMapping.php');
require_once(PI_APP_ROOT.'Libraries/password.php');

// DEBUG
if(PI_DEBUG)
{
	define('PI_BEGIN_TIME',      microtime(TRUE));
	define('PI_MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
	if(PI_MEMORY_LIMIT_ON) define('PI_START_MEMS', memory_get_usage());
	ini_set('display_errors', 1);
	error_reporting(E_ALL & ~E_NOTICE);
}
else
{
	error_reporting(0);
}

// 加载主体
require_once(PI_CORE_ROOT.'PI.php');

//数据错误日志
register_shutdown_function('appExceptionHandler');

// 核心框架常量配置
define('PI_DEFAULT_CONTROLLER', 'login');            // 默认控制器文件
define('PI_DEFAULT_ACTION',     'index');           // 默认方法
// 运行
try
{
	$app = new PI();
	$app->run();
}
catch(Exception $e)
{
	dump($e->getMessage());
}

/* End */
if($xhprof_enable){
	$data = xhprof_disable();   //返回运行数据
	  
	// xhprof_lib在下载的包里存在这个目录,记得将目录包含到运行的php代码中
	include_once "/data/web/xhprof/xhprof_lib/utils/xhprof_lib.php";  
	include_once "/data/web/xhprof/xhprof_lib/utils/xhprof_runs.php";  
	echo 1 ;
	$objXhprofRun = new XHProfRuns_Default(); 

	// 第一个参数j是xhprof_disable()函数返回的运行信息
	// 第二个参数是自定义的命名空间字符串(任意字符串),
	// 返回运行ID,用这个ID查看相关的运行结果
	$run_id = $objXhprofRun->save_run($data, "xhprof");
	echo "<a href='http://xhprof.ihuilian.com/xhprof_html/?run=".$run_id."' target='_blank' />查看</a>";
}
