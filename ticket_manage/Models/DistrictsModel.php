<?php
/**
 *
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class DistrictsModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'districts';
	public $pk         = 'id';
	public $hasDeleted = TRUE;

	/**
	 * 获取包含地区数据的多个数据，专供with参数，务乱用
	 *
	 * @param array $result 获取到的单个数据,得包含地区id信息
	 * @return array
	 */
	public function getListForWith($result, $ids)
	{
		$newids = array();
		foreach($ids as $id){
			$newids[] = $this->getDeepIds($id);
		}

		$tmpIds = array_flatten($newids);
		if($tmpIds){
			$lists = $this->getList('id in('.implode(',', $tmpIds).')');
			if($lists){
				$newLists = array();
				foreach($lists as $listVal){
					$newLists[$listVal['id']] = $listVal;
				}

				foreach($result as $key => $value){
					$relateIds = array();
					if(!is_null($value['district_id'])){
						$relateIds = $this->getDeepIds($value['district_id']);
						foreach($newLists as $k => $v){
							if(in_array($k, $relateIds)){
								$result[$key]['districts'][] = $v;
							}
						}
					}
				}
			}
		}
		return $result;
	}

	/**
	 * 获取包含地区数据的单个数据，专供with参数，务乱用
	 *
	 * @param array $result 获取到的单个数据,得包含地区id信息
	 * @return array
	 */
	public function getOneForWith($result, $id)
	{
		$ids = $this->getDeepIds($id);
		if($ids){
			$lists = $this->getList('id in('.implode(',', $ids).')');
			if($lists){
				if(!is_null($result['district_id'])){
					$result['districts'] = $lists;
				}
			}
		}
		return $result;
	}

	//获取当前ID及上几级的id集合
	public function getDeepIds($id)
	{
		if($id == 0){
			return array();
		}

		$ids = str_split($id, 2);
		$fix = 0;
		foreach($ids as $value){
			if($value == '00'){
				$fix++;
			}
		}

		$districts = array();
		for($i = 2; $i >= $fix; $i--){
			$current     = intval($id / pow(100, $i)) * pow(100, $i);
			$districts[] = $current;
		}

		return $districts;
	}

	public function getIds($id) {
		$list = array();
		$num = $id%10000;
		$list[] = $id - $num;
		if($num>0) {
			$num = $id%100;
			$list[] = $id - $num;
			if($num>0)
				$list[] = $id;
		}
		return $list;
	}

	public function getDistricts($id,$key='id') {
		$id = intval($id);
		$data = array();
		if($id>0) {
			$ids = $this->getIds($id);
			$data = $this->setKeyField($key)->getList("id in ('".implode("','", $ids)."')");
		}
		return $data;
	}

	//获取当前ID下下一级的内容
	public function findChildById($id)
	{
		$district = $this->getOne('id='.$id.' AND deleted_at IS NULL');
		if($district){
			$filter = 'parent_id='.$id.' AND level=\''.($district['level']+1).'\' AND deleted_at IS NULL';
			return $this->getList($filter);
		}else{
			return false;
		}
	}
}