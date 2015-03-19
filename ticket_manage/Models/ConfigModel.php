<?php
/**
 * 系统配置数据模型
 *
 * 2014-2-28
 *
 * @package models
 * @author cuiyulei
 **/
class ConfigModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'config';
	public $pk         = 'id';


	//可关联的,对应value为model前缀
	public $relateAble = array(
		'organization_main'    => 'Organizations',
		'organization_partner' => 'Organizations',
		'price_templates'      => 'PriceTemplates'
	);

	//关联的字段,对应value为表字段
	public $relateField = array(
		'organization_main'    => 'organization_main_id',
		'organization_partner' => 'organization_partner_id',
		'price_templates'      => 'price_templates_id'
	);

	static public $weekArray = array(
		'1' => '周一',
		'2' => '周二',
		'3' => '周三',
		'4' => '周四',
		'5' => '周五',
		'6' => '周六',
		'0' => '周日',
	);

	/**
	 * 获取星期
	 *
	 * @return mixed
	 * @author cuiyulei
	 **/
	static public function getWeekDay($day = '')
	{
		if(!empty($day)){
			return self::$weekArray[$day];
		}else{
			return self::$weekArray;
		}
	}

} // END 