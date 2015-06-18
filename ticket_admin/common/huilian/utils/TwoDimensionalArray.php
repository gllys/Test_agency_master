<?php
/**
 * @link
 */

namespace common\huilian\utils;

/**
 * TwoDimensionalArray.php
 * 二维数组处理类
 */
 
class TwoDimensionalArray {
	
	/**
	 * 计算二维数组某列的和
	 * @param Array $arr 二维数组
	 * @param String $column 一维数组中要求和的字段名
	 */
	public static function sumColumn($arr = array(), $column) {
		$sum = 0;
		if(is_array($arr)) {
			foreach($arr as $son) {
				if(isset($son[$column])) {
					$sum += $son[$column];
				}else {
					return $sum;
				}
			}
		}else
			return $sum;
		return $sum;
	}
	
	/**
	 * 挑选出二维数组中的一维数组某元素符合 $k = $v 的那些一维数组的集合
	 */
	public static function pickArr(Array $arr, $k, $v) {
		$pickArr = array();
		foreach($arr as $pick) {
			if($pick[$k] == $v)
				array_push($pickArr, $pick);
		}
		return $pickArr;
	}
	
	/**
	 * 去除二维数组中的一维数组某元素符合 $k = $v 的一维数组，返回余下的数组
	 */
	public static function excludeArr(Array $arr, $k, $v) {
		$excludeArr = [];
		foreach($arr as $pick) {
			if($pick[$k] != $v)
				array_push($excludeArr, $pick);
		}
		return $excludeArr;
	}
	
	/**
	 * 挑选出二维数组中的一维数组某元素组成一个新的数组
	 */
	public static function columns(array $arr, $k) {
		$columns = [];
		foreach($arr as $row) {
			array_push($columns, $row[$k]);
		}
		return $columns;
	}
	
	/**
	 * 挑选出二维数组中的一维数组某元素组成一个新的数组，然后在连接该数组,该数组的元素用引号括起来
	 * @param string $quote 引号，默认是单引号
	 * @param string $glue 连接符，默认,
	 * @return string
	 */
	public static function implodeQuoteColumns(array $arr, $k, $quote= '"', $glue = ',') {
		$columns = self::columns($arr, $k);
		return $quote . implode($quote.$glue.$quote, $columns) . $quote;
	}
	
	/**
	 * 挑选二维数组中某一行的key和value等于给定的$k, $v
	 * @return Mixed Null or Array
	 */
	public static function itemRow(array $arr, $item, $v) {
		foreach($arr as $row) {
			if($row[$item] == $v)
				return $row;
		}
	}
	
	/**
	 * 为二维数组的每一行添加新元素
	 */
	public static function addColumn(array $arr, $k, $v) {
		foreach($arr as $j => $row) {
			$arr[$j][$k] = $v;
		} 
		return $arr;
	}
	
	
}
 
?>