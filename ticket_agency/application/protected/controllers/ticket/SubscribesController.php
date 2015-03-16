<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/19/14
 * Time: 2:22 PM
 */

class SubscribesController extends Controller
{

	/**
	 * 加入、移除订阅
	 * @author grg
	 */
	public function actionToggle() {
		$type = Yii::app()->request->getParam('single');
		$done = Yii::app()->request->getParam('done');
		$done = boolval($done);
		if ($done) {
			$result = Subscribes::api()->delete(array(
				'ticket_id' => Yii::app()->request->getParam('id'),
				'organization_id' => Yii::app()->user->org_id,
				'type' => intval($type)
			), 0);
		} else {
			$result = Subscribes::api()->add(array(
				'ticket_id' => Yii::app()->request->getParam('id'),
				'organization_id' => Yii::app()->user->org_id,
				'fat_price' => Yii::app()->request->getParam('fat_price'),
				'group_price' => Yii::app()->request->getParam('group_price'),
			    'name' => Yii::app()->request->getParam('name'),
			    'type' => intval($type)
			), 0);
		}
		if ($result['code'] == 'succ') {
			echo json_encode(array(
				'code' => 1,
				'done' => 1 - intval($done)
			), JSON_UNESCAPED_UNICODE);
		} else {
			echo json_encode(array(
				'code' => 0,
				'done' => intval($done)
			), JSON_UNESCAPED_UNICODE);
		}
	}
} 
