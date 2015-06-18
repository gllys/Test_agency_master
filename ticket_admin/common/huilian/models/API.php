<?php
/**
 * @link
 */
namespace common\huilian\models;

use common\huilian\utils\TwoDimensionalArray;
use ApiModel;
use CPagination;

/**
 * API类
 * 本类处理一个关于接口的封装、优化等方面的工作
 * 
 * @author LRS
 */
class API {
	
	/**
	 * 同时查询
	 * 当出现如下情况时，适用本方法：
	 * 一个接口查询无法获取所需的所有数据，且该接口不为此做出更改时，
	 * 一般通过foreach对上诉获得结果集进行其它接口查询时，效率低下。针对这个问题，利用接口可以传递多个值的特性，一次查询出所需的所有数据。
	 * 然后再分配给原来的数组的。通过这样做，提高性能。实际上就是把foreach循环调用API，改为一次调用API。
	 * 注意：
	 * - 参数$callback上，采用通用参数格式，如：`Organizations::list`，如果参数的首字符不是`\`，则需要添加`\`，即最外层命名空间。
	 *   但是$callback的具体实现上，依据目前接口的规则。即Organizations::api()获取对象，然后通过对象来调用方法
	 * - 接口类通过__call方法实现list方法调用，因此此处实现时，不能简单的采用调用回调函数的方式（如：利用call_user_func_array等），需遵循该特性
	 * - 接口list方法通过有默认的分页功能，因此需要在查询的时候传递items参数，以免返回的数据缺失。
	 * - $newKey存入的数据都是二维数组的格式，因为有可能需要返回的是二维数组，但数据库之存入一维的情况，例如:`1,2,3`, `1`
	 * 
	 * @param array $rows 结果集数组，一般从该数组中提取某个键值组成新数组，然后统一调用接口
	 * @param string $column 列名
	 * @param string $callback 接口， 一般为  `Organizations::list`
	 * @param string $param 查询参数，用于接口查询的参数
	 * @param string $compareColumn 用于比对的列，是接口返回的数组的列名
	 * @param string $newKey 键名，用于存接口返回的数据
	 * @return array
	 */
	public static function simultaneous(array $rows, $column, $callback, $param, $compareColumn, $newKey) {
		if(strncasecmp($callback, '\\', 1) !== 0) {
			$callback = '\\' . $callback;
		}
		
		$columns = TwoDimensionalArray::columns($rows, $column);
		$ids = implode(',', TwoDimensionalArray::columns($rows, $column));
		
		list($class, $func) = explode('::', $callback);
		
		$obj = $class::api();
		$query = $obj->$func([$param => $ids, 'items' => count($columns), ]);
        $items = $query['body']['data'] ;
       	foreach($rows as $k => $row) {
			$rows[$k][$newKey] = [];
		}
		if(!empty($items)) {
			foreach($rows as $k => $row) {
				foreach($items as $i => $j) {
					if(in_array($j[$compareColumn], explode(',', $row[$column]))) {
						$rows[$k][$newKey][] = $items[$i];
					}
				}
			}
		}

		return $rows;
	}
	
	/**
	 * 获取接口列表数据
	 * 备注：
	 * - 本方法默认包含分页功能，这是实际中常用的状态。
	 * - 参数$callback上，采用通用参数格式，如：`Organizations::list`，如果参数的首字符不是`\`，则需要添加`\`，即最外层命名空间。
	 * @param string $callback 接口， 一般为  `Organizations::list`
	 * @param array $params 传递给接口的参数
	 * @param integer $pageSize 分页类型
	 * @return array ['lists' => '列表数组', 'pages' => '分页对象', ]
	 */
	public static function lists($callback, $params, $pageSize = 15) {
		if(strncasecmp($callback, '\\', 1) !== 0) {
			$callback = '\\' . $callback;
		}
		list($class, $func) = explode('::', $callback);
		$obj = $class::api();
		$res = $obj->$func($params);
		$lists = ApiModel::getLists($res);
		
		$pagination = ApiModel::getPagination($res);
		$pages = new CPagination($pagination['count']);
		$pages->pageSize = $pageSize; #每页显示的数目
		
		return ['lists' => $lists, 'pages' => $pages, ];
	}
}


?>