<?php
/**
 * UScribeLogRoute class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

class UScribeLogRoute extends CLogRoute
{
	/**
	 * scribe服务器ip地址
	 *
	 * @var string
	 */
	public $host='127.0.0.1';
	
	
	/**
	 * scribe服务器端口
	 *
	 * @var int
	 */
	public $port='1464';


	/**
	 * category前缀
	 *
	 * @var string
	*/
	public $categoryPrefix = "";
	

	private $socket;
	private $transport;
	private $protocol;
	private $scribe_client;
	private $transport_opened;

	public function init()
	{
		$GLOBALS['THRIFT_ROOT'] = dirname(__FILE__).DIRECTORY_SEPARATOR.'phpscribe';
		require_once dirname(__FILE__).'/phpscribe/packages/scribe/scribe.php';
		require_once dirname(__FILE__).'/phpscribe/transport/TSocket.php';
		require_once dirname(__FILE__).'/phpscribe/transport/TFramedTransport.php';
		require_once dirname(__FILE__).'/phpscribe/protocol/TBinaryProtocol.php';

		parent::init();
		$this->socket			= new TSocket($this->host, $this->port);
		$this->transport		= new TFramedTransport($this->socket);
		$this->protocol			= new TBinaryProtocol($this->transport, false, false);
		$this->scribe_client	= new scribeClient($this->protocol, $this->protocol);
		$this->transport_opened = FALSE;
	}

	protected function processLogs($logs)
	{
		foreach($logs as $item){
			$log['category']	= $this->categoryPrefix.$item[2];
			$log['message']		= $this->formatLogMessage($item[0],$item[1],$item[2],$item[3]);
			if(FALSE === $this->transport_opened){
				@$this->transport_opened = $this->transport->open();				
			}

			if($this->transport_opened){
				$entry		= new LogEntry($log);
				$messages	= array($entry);
				try{
					$this->scribe_client->Log($messages);
				}catch(Exception $e){}
			}
		}
	}
	protected function formatLogMessage($message,$level,$category,$time)
	{
		$ip = $this->getServerIp();
		return @date('Y/m/d H:i:s',$time)." [$level] [$category] [$ip] $message\n";
	}

	protected function getServerIp($withV6=false){
		if (isset($_SERVER)) {
			if($_SERVER['SERVER_ADDR']) {
				$server_ip = $_SERVER['SERVER_ADDR'];
			}else {
				$server_ip = $_SERVER['LOCAL_ADDR'];
			}
		}else{
			$server_ip = getenv('SERVER_ADDR');
		}
		 
		if(empty($server_ip)){
			preg_match_all('/inet'.($withV6 ? '6?' : '').' addr: ?([^ ]+)/', `/sbin/ifconfig`, $ips);
			$server_ip = "";
			foreach($ips[1] as $item){
				if($item!='127.0.0.1') $server_ip = $item;
				break;
			}
		}
		return $server_ip;
	}
}
