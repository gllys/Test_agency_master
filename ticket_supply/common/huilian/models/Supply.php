<?php
/**
 * @link
 */

namespace common\huilian\models;

use Yii;
use Tickettemplate;
use Credit;
use Landscape;

/**
 * 供应商类
 * 本类基于当前供应商封装一些常用的方法
 */
class Supply 
{
	
	/**
	 * 获取当前供应商所有的产品信息
	 * @return array
	 */
	public static function products() 
	{
		$params = [
			'or_id' => Yii::app()->user->org_id,
			'state' => '1,2',
			'show_all' => 1,
			'show_items'=>0,
		];
		$res = Tickettemplate::api()->lists($params);
		return empty($res['body']['data']) ? [] : $res['body']['data'];
	}
	
	/**
	 * 获取当前供应商所有的产品的名称
	 * 本方法适用于一些表单控件挑选产品
	 * @return array [产品主键 => '产品名称', ...]
	 */
	public static function productNames() 
	{
		$params = [
				'or_id' => Yii::app()->user->org_id,
				'state' => '1,2',
				'show_all' => 1,
				'fields' => 'id,name',
				'show_items'=>0,
		];
		$res = Tickettemplate::api()->lists($params);
		$products = empty($res['body']['data']) ? [] : $res['body']['data'];
		
		$productNames = [];
		foreach($products as $v) {
			$productNames[$v['id']] = $v['name'];
		}
		return $productNames;
	}
	
	/**
	 * 获取当前供应商所有 的分销商
	 * 本方法适用于一些表单控件挑选分销商
	 * @return array
	 */
	public static function agencyNames() 
	{
		$params = [
			'supplier_id' => Yii::app()->user->org_id,
			'show_all' => 1,
		];
		$res = Credit::api()->lists($params);
		$agencies = empty($res['body']['data']) ? [] : $res['body']['data'];
		
		$agencyNames = [];
		foreach($agencies as $v) {
			$agencyNames[$v['distributor_id']] = $v['distributor_name'];
		}
		return $agencyNames;
	}
	
	/**
	 * 获取该供应商所有景区的名称
	 * @return array ['景区主键' => '景区名称', ...]
	 */
	public static function landscapeNames() {
		$params = [
			'organization_id' => Yii::app()->user->org_id,
			'items' => 10000,
		];
		$res = Landscape::api()->lists($params);
		$landscapes = empty($res['body']['data']) ? [] : $res['body']['data'];
		
		$landscapeNames = [];
		foreach($landscapes as $v) {
			$landscapeNames[$v['id']] = $v['name'];
		}
		return $landscapeNames;
	}
}

?>