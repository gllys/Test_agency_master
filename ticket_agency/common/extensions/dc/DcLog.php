<?php
require_once("data.conf.php");
require_once $GLOBALS['THRIFT_ROOT'].'/packages/scribe/scribe.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TFramedTransport.php';
require_once $GLOBALS['THRIFT_ROOT'].'/protocol/TBinaryProtocol.php';

class DcLog{

	private static $instance;

	private $key;
	private $server_id;

	private $socket;
	private $transport;
	private $protocol;

	private $scribe_client;
	private $transport_opened;

	function __construct($key, $server_id){
		$this->init($key,$server_id);
	}

	function __destruct(){
		try{
			$this->transport->close();
		}catch(Exception $e){

		}
	}

	public static function getInstance($key, $server_id) {

		if(!self::$instance) {
			self::$instance = new self($key, $server_id);
		}

		return self::$instance;
	}

	//用户激活
	//                       平台帐号    角色ID    激活IP        来源广告ID     广告渠道ID
	public function activate($passport, $role_id, $register_ip, $register_ad, $register_ad_channel,
								//广告素材ID,			激活时间
								$register_ad_material, $activate_time){
		$args = func_get_args();
		$this->send_log(__FUNCTION__, $args);
	}

	//跟踪用户从注册到激活的流失情况
	//$time  系统当前时间戳，直接取 time()
	//$step  
	//        1 => 打开广告注册页面
	//        2 => 广告注册页面加载完成
	//        3 => 注册成功
	//        4 => 跳转平台
	//       11 => 创建界面加载前：从平台进入游戏，显示注册界面（选角色、昵称）
	//       12 => 创建界面加载完：步骤1页面全部加载完成时(as内部所有资源全部完成)，as调用后台php，写日志
	//       13 => 创建角色成功　：用户选择完角色和昵称，点击“确定”，提交数据时
	//       14 => 主界面加载成功：游戏主界面加载完成，as客户端第一次访问主协议时
	
	public function trace_activate($passport, $step, $client_ip, $time){
		$args = func_get_args();
		$this->send_log(__FUNCTION__, $args);
	}

	public function gift_gold_change($role_id, $level, $change_type, $channel, $item_id,

										$item_num, $gold, $change_time){
		$args = func_get_args();
		$this->send_log(__FUNCTION__, $args);
	}

	//黄金变化
	//变化类型：1 增加，0 减少
	//变化渠道：增加时，是黄金来源；减少时是消耗用途
	//							平台帐号    角色ID    当前等级  变化类型     变化渠道
	public function gold_change($passport, $role_id, $level, $change_type, $channel,
								//	道具ID     道具数量   消耗黄金 黄金余额  消耗时间
									$item_id, $item_num, $gold, $gold_left,$change_time){
		$args = func_get_args();
		$this->send_log(__FUNCTION__, $args);
	}

	//黄金兑换
	//							  平台帐号    订单号     角色ID    U币数量   黄金数量  兑换时间
	public function gold_exchange($passport, $order_id, $role_id, $umoney, $gold, $exchange_time){
		$args = func_get_args();
		$this->send_log(__FUNCTION__, $args);
	}

	public function login($passport, $role_id, $level, $register_ip, $register_ad, $login_time){
		$args = func_get_args();
		$this->send_log(__FUNCTION__, $args);
	}

	public function online_number($online_number, $calculate_time){
		$args = func_get_args();
		$this->send_log(__FUNCTION__, $args);
	}

	//用户升级日志
	//						平台帐号   角色ID    原等级   新等级    升级时间
	public function upgrade($passport,$role_id, $level1, $level2, $upgrade_time){
		$args = func_get_args();
		$this->send_log(__FUNCTION__, $args);
	}

	//每日用户等级、黄金快照
	//                             平台帐号    角色ID    等级    黄金 激活时间  最后登录时间(int) 日期 timestamp
	public function user_snapshot($passport, $rold_id, $level, $gold, $act_time, $last_login,      $date){
		$args = func_get_args();
		$this->send_log(__FUNCTION__, $args);
	}

	//实时用户等级、黄金快照,根据开服时间，在开服前一周内，每15分钟发一次，一周后停止
	//                             平台帐号    角色ID    等级    黄金 激活时间  最后登录时间(int) 日期 timestamp
	public function user_snapshot_rt($passport, $rold_id, $level, $gold, $act_time, $last_login,      $date){
		$args = func_get_args();
		$this->send_log(__FUNCTION__, $args);
	}
//================ 以下为平台日志 ====================//
	



	private function init($key, $server_id){
		$this->socket			= new TSocket(__DATA_CENTER_HOST__, __DATA_CENTER_PORT__);
		$this->transport		= new TFramedTransport($this->socket);
		$this->protocol			= new TBinaryProtocol($this->transport, false, false);
		$this->scribe_client	= new scribeClient($this->protocol, $this->protocol);
		$this->transport_opened = FALSE;

		$this->key				= $key;
		$this->server_id		= $server_id;
	}

	//发送日志到本地scribe server
	//category,分类
	//$message,array 数据
	public function send_log($category, $message){

		$log['category']	= $category;
		$log['message']		= $this->key.__LOG_SEPARATOR__.$this->server_id.__LOG_SEPARATOR__.implode(__LOG_SEPARATOR__,$message)."\n";
		if(FALSE === $this->transport_opened){
			$this->transport_opened = $this->transport->open();
			#var_dump($this->transport_opened);
		}

		//本地scribe连接失败，写到日志
		// /etc/init.d/scribed 启动脚本增加功能，启动时启动php进程，此php进程负责扫描未发送日志，发送到本地scribe
		if(FALSE === $this->transport_opened){
			$this->write_log($log);
		}else{
			$entry		= new LogEntry($log);
			$messages	= array($entry);
			try{
				$this->scribe_client->Log($messages);
			}catch(Exception $e){
				$this->write_log($log);
				$this->log_error($e->getMessage());
				#$this->transport_opened = $this->transport->open();
			}
		}
	}

	//当本地scribe server无法发送时，先写到本地文件，等scribe server启动后，再写入到scribe server
	private function write_log($log){
		$path = __DATA_BUFFER_PATH__.DIRECTORY_SEPARATOR.date("Ymd").DIRECTORY_SEPARATOR.$log['category'];

		if(!is_dir($path)){
			mkdir($path, 0777, true);
		}

		$file = $path.DIRECTORY_SEPARATOR.$log['category'].".log";

		$fp = fopen($file, "a");
		if(FALSE !== $fp){
			if(flock($fp, LOCK_EX)){
				$message = $log['message'];
				fwrite($fp, $message);
				flock($fp, LOCK_UN);
			}
			fclose($fp);
		}
	}

	private function log_error($error){
		$path = __DATA_BUFFER_PATH__.DIRECTORY_SEPARATOR.date("Ymd");
		mkdir($path);
		$fp = fopen($path.DIRECTORY_SEPARATOR."error.log", "a");
		fwrite($fp, "[".date("Y-m-d H:i:s")."] ".$error."\n");
		fclose($fp);
	}
}

#var_dump(get_included_files());
//demo & test
/*
$DCLog = DcLog::getInstance("xewij",98329);
while(1){
	$DCLog ->activate("passport".sprintf("%03d",$i), "ROLE_ID_".$i, "23.23.23.23","AD_".$i,"AD_channel_id".$i, "AD_material_id".$i,time());

	$DCLog ->upgrade("passport".sprintf("%03d",$i), "ROLE_ID_".$i, $i-1, $i ,time());

	$DCLog ->gold_change("passport".sprintf("%03d",$i), "ROLE_ID_".$i, $i, $i % 1, $i,
								//	道具ID     道具数量   消耗黄金 黄金余额  消耗时间
									"item_id_".$i, $i, 10*$i, 10000 - $i * 10 ,time());


}
unset($DCLog);
*/
?>
