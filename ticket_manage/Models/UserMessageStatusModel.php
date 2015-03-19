<?php
/**
 *
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class UserMessageStatusModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'user_message_status';
	public $pk         = 'id';

	public function deleteMsgs($uid, $msgIds)
	{
		if($msgIds){
			$insertSql = 'INSERT INTO '.$this->table.' (user_id,message_id,user_type,status)';
			$addItmes  = array();
			foreach($msgIds as $key => $value){
				$addItmes[] = '(\''.$uid.'\',\''.$value.'\',\'org\',\'deleted\')';
			}
			$insertSql .= 'VALUES '.implode(',', $addItmes).' ON DUPLICATE KEY UPDATE status=\'deleted\'';
			$this->query($insertSql);
			return $this->getAddID();
		}else{
			return false;
		}
	}
}