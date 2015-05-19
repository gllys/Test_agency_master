<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/19/14
 * Time: 2:19 PM
 */

class FavoritesController extends Controller
{

	public function actionIndex() {
		$type = Yii::app()->request->getParam('type');
		$data['type'] = is_null($type) ? 1 : intval($type);
		$name = Yii::app()->request->getParam('name');
		$data['name'] = $name;
		$page = Yii::app()->request->getParam('page');

		$params = array(
			'organization_id' => Yii::app()->user->org_id,
		    'type' => $data['type'],
		    'current' => isset($page)?$page:0,
		    'items' => 15
		);
		$name = trim($name);
		if ($name != '') {
			$params['name'] = $name;
		}
		$result = Favorites::api()->list($params);
		if ($result['code'] == 'succ' && isset($result['body']['data'])) {
			$data['lists'] = $result['body']['data'];
			$pagination = $result['body']['pagination'];
			$pages = new CPagination($pagination['count']);
			$pages->pageSize = $params['items']; #每页显示的数目
			$data['pages'] = $pages;
		}
		$this->render('index', $data);
	}

	/**
	 * 加入、移除收藏
	 * @author grg
	 */
	public function actionToggle() {
		$type = Yii::app()->request->getParam('single');
		$done = Yii::app()->request->getParam('done');
		$done = intval($done);
		if ($done) {
			$result = Favorites::api()->delete(array(
				'ticket_id' => Yii::app()->request->getParam('id'),
			    'organization_id' => Yii::app()->user->org_id,
			    'type' => intval($type)
			), 0);
		} else {
			$result = Favorites::api()->add(array(
				'ticket_id' => Yii::app()->request->getParam('id'),
				'organization_id' => Yii::app()->user->org_id,
				'name' => Yii::app()->request->getParam('name'),
				'type' => intval($type)
			), 0);
		}
		if ($result['code'] == 'succ') {
			echo json_encode(array(
				'code' => 1,
			    'done' => 1 - $done
			), JSON_UNESCAPED_UNICODE);
		} else {
			echo json_encode(array(
				'code' => 0,
				'done' => $done
			), JSON_UNESCAPED_UNICODE);
		}
	}
} 
