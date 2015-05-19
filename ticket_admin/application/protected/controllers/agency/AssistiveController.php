<?php
/**
 * @link
 */
use common\huilian\utils\Header;
use common\huilian\models\Performance;

/**
 * 辅助控制器
 * 本控制器用以提高一些检测、性能优化等方面的工作
 * 与具体业务无关，删除该控制器不影响具体业务。
 */
class AssistiveController extends Controller {
	
	/**
	 * 记录之前响应过慢的接口
	 */
	public $slowQueries = [
		[
			'interface' => 'Landscape::lists',
			'params' => [
				'ids' => '385',
				'fields' => 'id,name',
				'items' => 1000,
			]
		]
	];

	/**
	 * 慢查询
	 * 请求一些响应比较慢的接口
	 */
	public function actionSlowQueries() {
		$interface = 'Landscape::lists';
		$params = [
			'ids' => '385,89022,89022,89022,35,66,28,89022,66,89022,66,89022,89022,89022,89022,89022,89022',
			'items' => 15,
		];
		
		Performance::APIEcho($interface, $params);
	}
}