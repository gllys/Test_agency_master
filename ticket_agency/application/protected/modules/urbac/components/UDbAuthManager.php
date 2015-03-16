<?php
class UDbAuthManager extends CDbAuthManager {
	
	public function getAuthItems() {
		$command=$this->db->createCommand()
				->select('name,type,description,t1.bizrule,t1.data')
				->from(array(
					$this->itemTable.' t1',
					$this->assignmentTable.' t2'
				))
				->where('name=itemname AND type=:type AND userid=:userid', array(
					':type'=>$type,
					':userid'=>$userId
				));
				
		$items=array();
		foreach($command->queryAll() as $row)
		{
			if(($data=@unserialize($row['data']))===false)
				$data=null;
			$items[$row['name']]=new CAuthItem($this,$row['name'],$row['type'],$row['description'],$row['bizrule'],$data);
		}
		var_dump($items);
		return $items;
	}
	
}