<?php
/**
 *
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class MessageReceiveModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'message_receive';
	public $pk         = 'id';

	//生成票
	public function addReceives($msgId, $data)
	{
		if($data){
			$insertSql = 'INSERT INTO '.$this->table.' (`message_id`,`to_organization_id`)';
			$addItmes  = array();
			foreach($data as $key => $value){
				$addItmes[] = "('{$msgId}','{$value}')";
			}
			$insertSql .= ' VALUES '.implode(',', $addItmes);
			$this->query($insertSql);
			return $this->affectedRows();
		}else{
			return false;
		}
	}
}