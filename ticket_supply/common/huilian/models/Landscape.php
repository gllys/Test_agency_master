<?php
/**
 * @link
 */

namespace common\huilian\models;

use Yii;
use Tickettemplate;
use Credit;
use Poi;
use Landscape as LandscapeAPI;

/**
 * 景区类
 * 本类关于景区方面的关系
 */
class Landscape 
{
	/**
	 * 获取某个景区的所有景点
	 * 本方法适用于一些通用的情况，如：选择一个景区后，获取该景区所有的景点。
	 * @param integer $landscapeId 景区主键
	 * @return array
	 */
	public static function pois($landscapeId) 
	{
		$params = [
			'organization_ids' => Yii::app()->user->org_id,
			'landscape_ids' => $landscapeId,
			'show_all' => 1,
		];
		$res = Poi::api()->lists($params);
		return empty($res['body']['data']) ? [] : $res['body']['data'];
	}
	
	/**
	 * 获取某个景区的所有景点
	 * 本方法适用于一些通用的情况，如：选择一个景区后，获取该景区所有的景点。
	 * @param integer $landscapeId 景区主键
	 * @return array ['景点主键' => '景点名称', ...]
	 */
	public static function poiNames($landscapeId) 
	{
		$pois = self::pois($landscapeId);
		$poiNames = [];
		foreach($pois as $v) {
			$poiNames[$v['id']] = $v['name']; 
		}
		return $poiNames;
	}
}

?>