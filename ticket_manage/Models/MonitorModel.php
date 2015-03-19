<?php
/**
 *
 *
 * 2014-07-28
 *
 * @author grg
 * @version 1.0
 */
class MonitorModel extends BaseModel
{
	// 定义要操作的表名
	public $db     = 'jg';
	public $table  = 'monitors';
	public $pk     = 'id';

	public function getMonitorByTypeId($type_id)
	{
		return $this->getList(array('type_id' => $type_id), '', '', 'id, name');
	}

	public function getMonitorById($id)
	{
		return $this->getOne(array('id' => $id));
	}

}
