<?php
/**
 *
 *
 * 2014-07-28
 *
 * @author grg
 * @version 1.0
 */
class MonitorRelationshipModel extends BaseModel
{
	// 定义要操作的表名
	public $db     = 'jg';
	public $table  = 'monitor_relationship';
	public $pk     = 'id';

	public function getHelpType()
	{
		return $this->getList('deleted_at is null', '', '', 'id, name, type');
	}

	public function getHelpTypeById($help_type_id)
	{
		return $this->getOne(array('id' => $help_type_id), '', 'name, type');
	}

}
