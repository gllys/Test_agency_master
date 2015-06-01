<?php
/**
 * @link
 */
namespace common\huilian\models;

use Channel as ChannelAPI;
use Exception;

/**
 * 渠道类
 * 订单的来源就是渠道
 */
class Channel {
	
	/**
	 * 渠道名称
	 * 如：订单表中来源类型对应的名字
	 * @return array
	 */
	public static function names() {
		return [
			0 => '票台',
			1 => '淘宝',
			2 => '八爪鱼',
			3 => '同程',
			4 => '途牛',
			5 => '驴妈妈',
			6 => '携程',
			7 => '景点通',
			8 => '度周末',
			9 => '途家',
			10 => '去哪儿',
			13 => '淘在路上',
			14 => '微景点',
		];
	}
	
	/**
	 * 获取某个渠道的名称
	 * 注意：
	 * 如果 $channelId不是numeric，如null(数据库存在source为null的情况)，或者其他非法值，则intval转为0，返回'票台'
	 * @param integer $channelId
	 * @return string
	 */
	public static function name($channelId) {
		return self::names()[intval($channelId)];
	}
	
	/**
	 * 正在使用中的渠道
	 */
	public static function used() {
		return [
			0 => '票台',
			1 => '淘宝',
			10 => '去哪儿',
			13 => '淘在路上',
		];
	}
	
	/**
	 * 获取所有渠道
	 * @return array
	 */
	public static function all() {
		$res = ChannelAPI::api()->lists(['show_all' => 1, ]);
		return empty($res['body']['data']) ? [] : $res['body']['data'];
	}
	
	/**
	 * 获取所有渠道
	 * @return array
	 */
	public static function allWithTemplate() {
		$res = ChannelAPI::api()->lists(['show_all' => 1, 'is_template_name_empty' => 0, ]);
		return empty($res['body']['data']) ? [] : $res['body']['data'];
	}
	
	/**
	 * 获取所有正在使用的有模板文件的渠道
	 * @return array
	 */
	public static function allUsedWithTemplate() {
		$res = ChannelAPI::api()->lists(['show_all' => 1, 'is_template_name_empty' => 0, 'status' => 1, ]);
		return empty($res['body']['data']) ? [] : array_reverse($res['body']['data']);
	}
	
	/**
	 * 获取所有没有模板的渠道
	 * @return array
	 */
	public static function allWithoutTemplate() {
		$res = ChannelAPI::api()->lists(['show_all' => 1, 'is_template_name_empty' => 1, ]);
		return empty($res['body']['data']) ? [] : $res['body']['data'];
	}
	
	/**
	 * 获取所有渠道名称
	 * @return array ['渠道主键' => '渠道名称', ...]
	 */
	public static function allNames() {
		$channels = self::all();
		$allNames = [];
		foreach($channels as $channel) {
			$allNames[$channel['id']] = $channel['name']; 
		}
		return $allNames;
	}
	
	/**
	 * 获取所有有模板的渠道名称
	 * @return array ['渠道主键' => '渠道名称', ...]
	 */
	public static function allNamesWithTemplate() {
		$channels = self::allWithTemplate();
		$allNames = [];
		foreach($channels as $channel) {
			$allNames[$channel['id']] = $channel['name'];
		}
		return $allNames;
	}
	
	/**
	 * 获取所有没有模板的渠道的名称
	 * @return array ['渠道主键' => '渠道名称', ...]
	 */
	public static function allNamesWithoutTemplate() {
		$channels = self::allWithoutTemplate();
		$allNames = [];
		foreach($channels as $channel) {
			$allNames[$channel['id']] = $channel['name'];
		}
		return $allNames;
	}
	
	/**
	 * 获取渠道
	 * 注意：
	 * 目前接口没有实现获取单一渠道的调用方法，如：detail，所以通过本方法特殊处理列表接口返回单条数据。
	 * 若日后接口处实现，单一渠道接口，可删除本方法，保存调用方式一致。
	 * @param integer $id 渠道主键
	 * @return array
	 * @throws Exception 无法获得接口渠道数据
	 */
	public static function get($id) {
		$params = [
			'id' => $id,
		];
		$res = ChannelAPI::api()->lists($params);
		if(empty($res['body']['data'])) {
			throw new Exception('无法获得接口渠道数据');
		}
		return current($res['body']['data']);
	}
}


?>