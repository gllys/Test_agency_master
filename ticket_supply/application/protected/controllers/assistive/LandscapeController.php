<?php
/**
 * @link
 */
use common\huilian\utils\Header;
use common\huilian\models\Landscape;

/**
 * 辅助模块-景区控制器
 */
class LandscapeController extends Controller
{
	/**
	 * 返回某个景区所有景点JOSN格式的数据
	 * @param integer $landscape_id 景区主键
	 */
	public function actionPois($landscape_id) {
		echo json_encode(Landscape::poiNames($landscape_id), JSON_UNESCAPED_UNICODE);
	}

	/**
	 * 返回某个景区所有景点的option代码
	 * 本方法主要解决返回json格式，用javascript组装数据上的繁琐问题。要不，也可以用上面的返回JSON方法，在前台组装。
	 * @param integer $landscape_id 景区主键
	 */
	public function actionPoiOptions($landscape_id) {
		$poiNames = Landscape::poiNames($landscape_id);
		foreach($poiNames as $k => $v) {
			echo '<option value="' .$k. '">' .$v. '</option>';
		}
	}
	
}