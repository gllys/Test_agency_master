<?php
class UAuthItemDataProvider extends CDataProvider{
	
	public function fetchData(){
		$auth = Yii::app()->authManager;
		$items = $auth->getAuthItems();
		$data = array();
		foreach($items as $item){
			if($item->type==0) $type = '操作';
			elseif($item->type==1) $type = '任务';
			elseif($item->type==2) $type = '角色';
			$row = new stdClass();
			$row->primaryKey = $item->name;
			$row->name=$item->name;
			$row->type=$type;
			$row->bizRule=$item->bizRule;
			$row->data=$item->data;
			$row->description=$item->description;
			$data[] = $row;
		}
		return $data;		
	}
	
	protected function fetchKeys(){
		$keys=array();
		foreach($this->getData() as $i=>$data)
		{
			$keys[] = $data->name;
		}
		return $keys;
	}
	
	protected function calculateTotalItemCount(){
		return count($this->getData());
	}
}