<?php
/**
 *
 *
 * 2014-07-18
 *
 * @author grg
 * @version 1.0
 */
class HelpModel extends BaseModel
{
	// 定义要操作的表名
	public $db     = 'fx';
	public $table  = 'helps';
	public $pk     = 'id';

	public function getHelpByTypeId($type_id)
	{
		return $this->getList(array('type_id' => $type_id), '', '', 'id, name');
	}

	public function getHelpById($help_id)
	{
		return $this->getOne(array('id' => $help_id), '', 'name, info');
	}

}
