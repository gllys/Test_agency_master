<?php

/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/11/14
 * Time: 2:44 PM
 */
class MessagesController extends CController
{
	public function actionTrans() {
		//$transaction = Yii::app()->db->beginTransaction();
		$receive             = new MessageReceive();
		$receive->attributes = $_POST['receive'];
		unset($_POST['receive']);
		$message             = new Message();
		$message->attributes = $_POST;
		try {
			if ($message->save()) {
				$receive->message_id = $message->id;
				if ($receive->save()) {
					$this->_return('succ', '消息传输成功！');
				}
			}
			//$transaction->commit();
		} catch (Exception $e) {
			//$transaction->rollback();
		}
		$this->_return('fail', '消息传输失败！');
	}

	public function _return($code, $msg, $params = array()) {
		echo json_encode(array('code' => $code, 'message' => $msg, 'params' => $params), JSON_UNESCAPED_UNICODE);
		Yii::app()->end();
	}
}
