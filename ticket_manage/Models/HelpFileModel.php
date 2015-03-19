<?php
/**
 *
 *
 * 2014-08-07
 *
 * @author grg
 * @version 1.0
 */
class HelpFileModel extends BaseModel
{
	// 定义要操作的表名
	public $db     = 'fx';
	public $table  = 'help_files';
	public $pk     = 'id';

	public function getHelpFile($get)
	{
		return $this->getList($get, '', '', 'id, `name`, `file_id`, `desc`');
	}

}
