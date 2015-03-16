<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/6/14
 * Time: 11:44 AM
 */

class QrController extends CController
{

	public function accessRules() {
		return array(
			array('allow', 'users' => array('*'))
		);
	}

	public function filters() {
		return array('accessControl');
	}

	public function actionView() {
		$code = Yii::app()->request->getParam('id');
		if (ctype_digit($code) && strlen($code) == 15) {
			$this->renderPartial('index', array('code' => $code));
		}
		exit;
	}

} 
