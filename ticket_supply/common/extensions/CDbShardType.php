<?php
abstract class CDbShardType{	
	public function getTableName($baseTableName, $shardValue){
		if(strpos($baseTableName,'{{')!==false) $userPrefix = true;
		else $userPrefix = false;
		$baseTableName = trim($baseTableName,"{}");
		
		$tableName = $this->calculateFullName($baseTableName, $shardValue);
		if($userPrefix) $tableName = "{{".$tableName."}}";
		return $tableName;
	}
	public function getDbName($baseDbName, $shardValue){
		return $this->calculateFullName($baseDbName, $shardValue);
	}
	
	abstract public function calculateFullName($baseName, $shardValue);
	abstract public function calculateDynamicName($shardValue);
}

/**
 * 按md5值分表
 *
 */
class shardTypeMd5 extends CDbShardType{
	/**
	 * 设置取md5后值的前几位
	 *
	 * @var int
	 */
	public $len = 1;
	
	/**
	 * 设置取md5后值从第几位开始取
	 *
	 * @var unknown_type
	 */
	public $start = 0;
	
	/**
	 * 基本表名和动态表名之前的连接符
	 *
	 * @var string
	 */
	public $connector = '_';
	
	/**
	 * 动态表名的位置，可能的值有after和before
	 *
	 * @var string
	 */
	public $position = 'after';
	
	public function calculateFullName($baseName, $shardValue){
		$dynamicTableName = $this->calculateDynamicName($shardValue);		
		if($this->position=='after') return $baseName.$this->connector.$dynamicTableName;
		elseif($this->position=='before') return $dynamicTableName.$this->connector.$baseName;
		else throw new CException("position应该为before和after其中之一");
	}
	public function calculateDynamicName($shardValue){
		$md5 = md5($shardValue);
		return substr($md5, $this->start, $this->len);
	}
}

/**
 * 按子串分表
 *
 */
class shardTypeSubstr extends CDbShardType{
	/**
	 * 动态表名长度
	 *
	 * @var int
	 */
	public $len=1;
	
	/**
	 * 从第几个位置开始取动态表名
	 *
	 * @var int
	 */
	public $start=0;
	
	/**
	 * 基本表名和动态表名之前的连接符
	 *
	 * @var string
	 */
	public $connector = '_';
	
	/**
	 * 动态表名的位置，可能的值有after和before
	 *
	 * @var string
	 */
	public $position = 'after';
	
	public function calculateFullName($baseName, $shardValue){
		$dynamicTableName = $this->calculateDynamicName($shardValue);
		if($this->position=='after') return $baseName.$this->connector.$dynamicTableName;
		elseif($this->position=='before') return $dynamicTableName.$this->connector.$baseName;
		else throw new CException("position应该为before和after其中之一");
	}
	public function calculateDynamicName($shardValue){
		return substr($shardValue, $this->start, $this->len);
	}
}

/**
 * 按日期分表
 *
 */
class shardTypeDate extends CDbShardType{
	/**
	 * 动态表名长度
	 *
	 * @var int
	 */
	public $dateFormat='Ymd';	
	
	
	/**
	 * 基本表名和动态表名之前的连接符
	 *
	 * @var string
	 */
	public $connector = '_';
	
	/**
	 * 动态表名的位置，可能的值有after和before
	 *
	 * @var string
	 */
	public $position = 'after';
	
	public function calculateFullName($baseName, $shardValue){
		$dynamicTableName = $this->calculateDynamicName($shardValue);
		if($this->position=='after') return $baseName.$this->connector.$dynamicTableName;
		elseif($this->position=='before') return $dynamicTableName.$this->connector.$baseName;
		else throw new CException("position应该为before和after其中之一");
	}
	
	public function calculateDynamicName($shardValue){
		return date($this->dateFormat, $shardValue);
	}
}

/**
 * 按余数分表
 *
 */
class shardTypeMod extends CDbShardType{
	/**
	 * 动态表名长度
	 *
	 * @var int
	 */
	public $dividend='16';	
	
	
	/**
	 * 基本表名和动态表名之前的连接符
	 *
	 * @var string
	 */
	public $connector = '_';
	
	/**
	 * 动态表名的位置，可能的值有after和before
	 *
	 * @var string
	 */
	public $position = 'after';
	
	public function calculateFullName($baseName, $shardValue){
		$dynamicTableName = $this->calculateDynamicName($shardValue);
		if($this->position=='after') return $baseName.$this->connector.$dynamicTableName;
		elseif($this->position=='before') return $dynamicTableName.$this->connector.$baseName;
		else throw new CException("position应该为before和after其中之一");
	}
	
	public function calculateDynamicName($shardValue){
		return $shardValue % $this->dividend;
	}
}

/**
 * 按两次余数分表
 *
 */
class shardTypeDoubleMod extends CDbShardType{
	/**
	 * 动态表名长度
	 *
	 * @var int
	 */
	public $dividend1='16';	
	
	/**
	 * 动态表名长度
	 *
	 * @var int
	 */
	public $dividend2='16';	
	
	
	/**
	 * 基本表名和动态表名之前的连接符
	 *
	 * @var string
	 */
	public $connector = '_';
	
	/**
	 * 动态表名的位置，可能的值有after和before
	 *
	 * @var string
	 */
	public $position = 'after';
	
	public function calculateFullName($baseName, $shardValue){
		$dynamicTableName = $this->calculateDynamicName($shardValue);
		if($this->position=='after') return $baseName.$this->connector.$dynamicTableName;
		elseif($this->position=='before') return $dynamicTableName.$this->connector.$baseName;
		else throw new CException("position应该为before和after其中之一");
	}
	
	public function calculateDynamicName($shardValue){
		return $shardValue % $this->dividend1 % $this->dividend2;
	}
}

/**
 * 按crc32值分表
 *
 */
class shardTypeCrc32 extends CDbShardType{
	/**
	 * 动态表名数量
	 *
	 * @var int
	 */
	public $dividend='100';	
	
	/**
	 * 基本表名和动态表名之前的连接符
	 *
	 * @var string
	 */
	public $connector = '_';
	
	/**
	 * 动态表名的位置，可能的值有after和before
	 *
	 * @var string
	 */
	public $position = 'after';
	
	public function calculateFullName($baseName, $shardValue){
		$dynamicTableName = $this->calculateDynamicName($shardValue);		
		if($this->position=='after') return $baseName.$this->connector.$dynamicTableName;
		elseif($this->position=='before') return $dynamicTableName.$this->connector.$baseName;
		else throw new CException("position应该为before和after其中之一");
	}
	public function calculateDynamicName($shardValue){
		$crc = sprintf("%u",crc32($shardValue));
		return fmod($crc,$this->dividend);
	}
}