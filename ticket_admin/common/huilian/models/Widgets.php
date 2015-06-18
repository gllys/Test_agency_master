<?php
/**
 * @link
 */
namespace common\huilian\models;

use Yii;

/**
 * 部件类
 * 本类主要封装一些常用的部件，如view层中的分页等
 */
class Widgets {
	
	/**
	 * 分页部件
	 * @param mixed $pages 
	 */
	public static function pagenation($pages) {
		if (!empty($pages)) {
			$controller = Yii::app()->getController(); //获取当前控制器
			$controller->widget('common.widgets.pagers.ULinkPager', [
				'cssFile' => '',
				'header' => '',
				'prevPageLabel' => '上一页',
				'nextPageLabel' => '下一页',
				'firstPageLabel' => '',
				'lastPageLabel' => '',
				'pages' => $pages,
				'maxButtonCount' => 5, //分页数量
			]);
		}
	}
	
}

?>