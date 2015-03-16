<?php

class AjaxServerController extends CController
{
	public function actionGetChildern()
	{
			$id = $_REQUEST['id'];
            $rs = Districts::model()->findAllByAttributes(array('parent_id'=>$id));
            echo CJSON::encode($rs) ;
	}
}