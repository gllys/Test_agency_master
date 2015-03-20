<?php

class AjaxServerController extends CController
{
	public function actionGetChildern()
	{
			$id = $_REQUEST['id'];
            $rs = Districts::model()->findAllByAttributes(array('parent_id'=>$id));
            echo CJSON::encode($rs) ;
	}

	public function actionPoiNames() {
		if (Yii::app()->request->getIsAjaxRequest()) {
			header('Content-type: application/json; charset=utf-8');
			$ids    = Yii::app()->request->getParam('ids');
			$items  = substr_count($ids, ',') + 1;
			$result = Poi::api()->lists(array(
				'ids' => $ids,
				'organization_id' => Yii::app()->user->org_id,
				'fields' => 'name',
				'items' => $items
			));
			if ($result['code'] == 'succ') {
				echo json_encode(array(
					'code' => 1,
					'data' => $result['body']['data']
				), JSON_UNESCAPED_UNICODE);
			}
			else {
				echo json_encode(array('code' => 0));
			}
		}
	}

	public function actionLandscapeNames() {
		if (Yii::app()->request->getIsAjaxRequest()) {
			header('Content-type: application/json; charset=utf-8');
			$ids    = Yii::app()->request->getParam('ids');
			$items  = substr_count($ids, ',') + 1;
			$result = Landscape::api()->lists(array(
				'ids' => $ids,
//				'organization_id' => Yii::app()->user->org_id,
				'fields' => 'name',
				'items' => $items,
				'status' => 1
			));
			if ($result['code'] == 'succ') {
				echo json_encode(array(
					'code' => 1,
					'data' => $result['body']['data']
				), JSON_UNESCAPED_UNICODE);
			}
			else {
				echo json_encode(array('code' => 0));
			}
		}
	}

	public function actionOrganizationsNames() {
		if (Yii::app()->request->getIsAjaxRequest()) {
			header('Content-type: application/json; charset=utf-8');
			$ids    = Yii::app()->request->getParam('ids');
			$items  = substr_count($ids, ',') + 1;
			$result = Organizations::api()->list(array(
				'id' => $ids,
//				'organization_id' => Yii::app()->user->org_id,
				'fields' => 'name',
				'items' => $items
			));
			if ($result['code'] == 'succ') {
				echo json_encode(array(
					'code' => 1,
					'data' => $result['body']['data']
				), JSON_UNESCAPED_UNICODE);
			}
			else {
				echo json_encode(array('code' => 0));
			}
		}
	}

}
