<?php
/**
 * @link
 */

namespace common\huilian\models;

use common\huilian\utils\Server;

/**
 * 部件类
 * 
 * @author LRS
 */
class Widgets {
	
	/**
	 * 返回引入页
	 * 注意：
	 * - 如果当前页面没有引入页，则返回null
	 */
	public static function httpReferer() {
		return ($httpReferer = Server::httpReferer()) ? '<a class="btn btn-default" href="' .$httpReferer. '" >返回</a>' : null;
	}
	
}

?>