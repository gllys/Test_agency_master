<?php
class UHtml{
	public static function listData($data, $name, $value){
		$arr = array();
		foreach ($data as $row){
			if(!isset($row[$name]) || !isset($row[$value])) throw new CException('数组中的每一行必须包含"'.$name.'"和"'.$value.'"');
			$key = $row[$name];
			$v = $row[$value];
			$arr[$key] = $v;
		}
		return $arr;
	}
}