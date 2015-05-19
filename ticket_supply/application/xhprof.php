<?php
if (!defined('XHPROF_OPEN')) define('XHPROF_OPEN', 0);
if (!defined('XHPROF_TIME')) define('XHPROF_TIME', 0.5);
class Xhprof
{
	private static $st = 0;

	/**
	 * [start description]
	 * @return [type] [description]
	 */
	public static function start()
	{
		if (!function_exists('xhprof_enable')) {
			exit("xhprof_enable not exists!");
		} 
		if (!is_dir(XHPROF_ROOT)) {
			exit(XHPROF_ROOT . " not exists!");
		} 
		// xhprof_enable();
		xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
		self::$st = microtime(true);
		register_shutdown_function(array(__CLASS__, 'end'));
	}

	/**
	 * [end description]
	 * @return [type] [description]
	 */
	public static function end()
	{
		$et = microtime(true);
		$data = xhprof_disable();

		include_once XHPROF_ROOT."xhprof_lib/utils/xhprof_lib.php";
		include_once XHPROF_ROOT."xhprof_lib/utils/xhprof_runs.php";
		$runs = new XHProfRuns_Default();
		$runId = $runs->save_run($data, "hx");

		$cost = $et - self::$st;
                if($cost<XHPROF_TIME){
                    return false;
                }
		$host = $_SERVER['HTTP_HOST'];
                $log = XHPROF_ROOT . 'xhprof_html/logs/'.$host.date('Ymd').'.html';
		$title = $_SERVER['REQUEST_URI'];

                $url = strpos($title,'?') ? "http://{$host}/{$title}&_debug_=huilian123456":"http://{$host}/{$title}?_debug_=huilian123456";
		$content = 'Time: '.date('H:i:s')."\tCost: {$cost}\t\tUri({$host}):<a href='/index.php?run={$runId}&source=hx' target='_blank'>{$title}</a> [<a href='/callgraph.php?run={$runId}&source=hx' target='_blank'>callgraph</a>] [<a href='{$url}' target='_blank'>跳转致页面</a>] <br/>";
		file_put_contents($log, $content, FILE_APPEND);
	}
}
if(XHPROF_OPEN) Xhprof::start();
