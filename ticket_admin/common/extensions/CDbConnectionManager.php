<?php
Yii::import("common.extensions.CDbShardType",true);
class CDbConnectionManager extends CApplicationComponent{
	public $config = array();
	public function getDbConfig($model, $shardValue, $useSlave=false){
		if(!isset($this->config[$model])) return null;		
		$config = $this->config[$model];
		
		$shardingType = $config['shardType'];
		$shardManager = $this->getShardManager($shardingType);
		$dynamicName = $shardManager->calculateDynamicName($shardValue);
//		echo "dynamicName=".$dynamicName;exit;
		if($useSlave) $hostConfig = $config['slaveConfig'];
		else $hostConfig = $config['masterConfig'];
		$host = $this->getDbHost($hostConfig, $dynamicName);
		if($host == null) throw new CHttpException(500, '找不到数据库配置!');
		$port = empty($host['port'])?3306:$host['port'];
		
		$connectionString = $config['driver'].":host=".$host['host'].";dbname=".$shardManager->getDbName($config['baseDbName'], $shardValue).";port=".$port;
		return array(
			'connectionString'=>$connectionString,
			'username'=>$host['username'],
			'password'=>$host['password']
		);
	}

	
	protected function getDbHost($hosts, $dynamicName){
		foreach ($hosts as $key=>$host){
			if(strpos($key,"-")!==false){ //范围
				list($start, $end) = explode("-", $key, 2);
				if($start <= $dynamicName && $dynamicName <= $end){//命中
					return $host;
					break;
				}
			}elseif($key == $dynamicName){//命中
				return $host;
				break;
			}
		}
		return null;
	}
	
	protected function getShardManager($config){
		$className = $config['class'];
		unset($config['class']);
		$shardManager = new $className();
		if(!$shardManager instanceof CDbShardType){
			throw new Exception('shardingType必须是CDbShardType的子类');
		}
		foreach($config as $name=>$value)
			$shardManager->$name=$value;
		
		return $shardManager;
	}
}