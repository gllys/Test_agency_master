<?php
/**
 * CUdpLogRoute class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
class CUdpLogRoute extends CLogRoute
{
	/**
	 * udp服务器ip地址
	 *
	 * @var string
	 */
	public $host;
	
	
	/**
	 * udp日志服务器端口
	 *
	 * @var int
	 */
	public $port;

	protected function processLogs($logs)
	{
		$fp = fsockopen("udp://".$this->host, $this->port, $errno, $errstr);
		if ($fp) {
		    fwrite($fp, $this->formatLogMessage($log[0],$log[1],$log[2],$log[3]));
		    fclose($fp);
		}
	}
}
