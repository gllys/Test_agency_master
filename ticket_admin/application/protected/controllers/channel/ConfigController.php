<?php
/**
 * @link
 */

use common\huilian\utils\Header;
use common\huilian\utils\GET;
use common\huilian\models\Channel as ChannelStatic;
use common\huilian\models\Admin;

/**
 * 渠道配置控制器
 */
class ConfigController extends Controller
{
	/**
	 * 列表
	 */
	public function actionIndex()
	{	
// 		Header::utf8();
// 		$c = Channel::api()->add([
// 			'name' => '空文件名，不显示在列表',
// 			'op_user' => Yii::app()->user->display_name,
// 			'status' => 1,
// 			'op_user' => Yii::app()->user->id,
// 			'created_by' => Yii::app()->user->id,
// 		]);
		
		$params = [
			'current' => isset($_GET['page']) ? $_GET['page'] : 1,
			'is_template_name_empty' => 0,
		];
		$params = GET::requiredAdd(['id', 'created_at_start', 'created_at_end', 'template_name', 'author', 'op_user', ], $params);
		$res = Channel::api()->lists($params);
		$channels = ApiModel::getLists($res);
		
		$pagination = ApiModel::getPagination($res);
		$pages = new CPagination($pagination['count']);
		$pages->pageSize = 15; #每页显示的数目
		
// 		var_dump($params);
// 		var_dump($channels);
// 		exit;
		
		$this->render('index', [
			'channels' => $channels,
			'pages' => $pages,
			'cannelNamesWithTemplate' => ChannelStatic::allNamesWithTemplate(),
			'admins' => array_unique(Admin::allNames()),
		]);
	}

	/**
	 * 修改渠道配置
	 * 注意：
	 * - post修改数据时，目前只修改了`开发人员`和`备注`。参数中包含主键
	 */
	public function actionUpdate() {
		Header::utf8();
		if(Yii::app()->request->isPostRequest) {
			$params = [
				'id' => $_POST['id'],
				'author' => $_POST['author'],
				'remark' => $_POST['remark'],
				'op_user' => Yii::app()->user->display_name,
				'status' => 1,
			];
			$res = Channel::api()->edit($params);
			$res ? $this->_end(!ApiModel::isSucc($res), $res['message']) : $this->_end(1, '通信失败');
		} else {
			$this->renderPartial('update', ['channel' => ChannelStatic::get($_GET['id']), ]);
		}
	}
	
	/**
	 * 修改渠道配置
	 * 注意：
	 * - post修改数据时，目前只修改了`开发人员`和`备注`。参数中包含主键
	 */
	public function actionUpdateList() {
		Header::utf8();
		$this->renderPartial('updateList', ['channels' => ChannelStatic::allWithoutTemplate(), ]);
	}
	
	/**
	 * 删除渠道
	 * @param integer $id 渠道主键
	 */
	public function actionDel($id) {
// 		Header::utf8();
		$params = [
			'id' => $id,
			'deleted_at' => 1,
		];
		$res = Channel::api()->edit($params);
		$res ? $this->_end(!ApiModel::isSucc($res), $res['message']) : $this->_end(1, '通信失败');
	}
	
	/**
	 * 清除渠道配置文件
	 * @param integer $id 渠道主键
	 */
	public function actionClearTemplate($id) {
// 		Header::utf8();
		$params = [
			'id' => $id,
			'template' => '',
			'template_name' => '',
			'author' => '',
			'remark' => '',
			'op_user' => '',
			'status' => 0,
		];
		$res = Channel::api()->edit($params);
		$res ? $this->_end(!ApiModel::isSucc($res), $res['message']) : $this->_end(1, '通信失败');
	}
	
	/**
	 * 上传文件
	 * 注意：
	 * - 本控制器，返回上传文件的文件名和内容。因为设计上，文件是直接获取内容存入数据库。
	 * 文件上传参数格式：
	 * array ( 'template_name' => array ( 'name' => 'pure.html', 'type' => 'text/html', 'tmp_name' => 'D:\\wamp\\tmp\\php935F.tmp', 'error' => 0, 'size' => 98, ), ) 
	 * @param integer $id 渠道主键
	 * @return string 文件名
	 */
	public function actionUpload($id) {
		$params = [
			'id' => $_GET['id'],
			'template_name' => $_FILES['template_name']['name'],
			'template' => file_get_contents($_FILES['template_name']['tmp_name']),
		];
		// 如果上传的是ANSI编码，需要转换成UTF-8编码，存入数据库字段中，存入数据库中的是UTF-8编码的字符串，预览时，输出UTF-8头信息，即保证不乱码。
		$params['template'] = mb_convert_encoding($params['template'], 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');

		$res = Channel::api()->edit($params);
		if(ApiModel::isSucc($res)) {
			echo $_FILES['template_name']['name'];
		}
	}
	
	/**
	 * 预览渠道的文件
	 */
	public function actionPreview($id) {
		Header::utf8();
		$channel = ChannelStatic::get($id);
		echo $channel['template'];
	}
	
}