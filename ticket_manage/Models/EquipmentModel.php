<?php
/**
 * equipment model
 *
 * 2014-3-13
 *
 * @package model
 * @author cuiyulei
 **/
class EquipmentModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'equipment';
	public $pk         = 'id';

	//可关联的,对应value为model前缀
	public $relateAble = array(
		'landscape' => 'landscapes',
		'poi'       => 'poi',
		'admin'     => 'admin'
	);

	//关联的字段,对应value为表字段
	public $relateField = array(
		'landscape' => 'landscape_id',
		'poi'       => 'poi_id',
		'admin'     => 'create_by'
	);


} // END class