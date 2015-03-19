<?php
/**
 * 服务控制器：主要为api、rpc使用，
 *
 * 2013-10-29
 *
 * @package controller
 * @author cuiyulei
 **/
class ServiceController extends Controller
{
	
	const OUTPUT_JSON = 'json';
	const OUTPUT_XML  = 'xml';
	const OUTPUT_CSV  = 'csv';

	protected $_outputType = 'json';

	protected $_responseCode;

	/**
	 * 输出
	 *
	 * @param mixed $output 输出内容 
	 *
	 * @return void
	 * @author cuiyulei
	 **/
	protected function _output($output)
	{
		switch ($this->_outputType) {
			case self::OUTPUT_JSON:
				$output = json_encode($output);			
				break;
			case self::OUTPUT_XML:
				$output = simplexml_load_string($output)->asXML(); 
				break;
			default:
				break;
		}
		echo $output;
	}

	
	/**
	 * 纪录访问日志
	 * @param string $action  访问方法
	 * @param mixed $msg 纪录信息
	 *
	 * return void
	 */
	public function log($action, $msg, $secondDir = '')
	{
		 //设置日志目录
		 $logDir = PI_APP_PATH.'/logs'.($secondDir ? "/$secondDir" : '');
		 if(!file_exists($logDir)) mkdir($logDir, 0777,true);

		 //设置日志保存文件
		 $logFile = $logDir.'/'.date('Y-m-d').'.php';
		 $flag    = fopen($logFile, 'a+');
		 $msg     = date('H:i:s').'-['. $action .']: '. (is_array($msg) ? json_encode($msg) : $msg)."\n";
		 fwrite($flag, $msg);
		 fclose($flag);
	}

} // END class