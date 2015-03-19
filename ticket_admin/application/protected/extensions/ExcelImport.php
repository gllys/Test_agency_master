<?php

/**
 * Created by PhpStorm.
 * User: grg
 * Date: 9/2/14
 * Time: 8:22 PM
 */
class ExcelImport {

	public static function createFile($upload, $user_type, $org_id, $scenic, $date, $act = '') {
		$img = $scenic.date('Y年n月', strtotime($date . '01')).'月报数据录入表';
		$dir = Yii::app()->basePath . '/../files/inputs/'.$user_type.'/'.$org_id.'/';
		if ($upload['name'] != $img.'.xlsx') {
			return null;
		}
		if (!empty($upload) && $act === 'override') {
			$file = $dir . $img.'_input.xlsx';
			if (is_file($file)) {
				unlink($file);
			}
		}
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}

		if (!move_uploaded_file($upload['tmp_name'], $file)) {
			return null;
		}

		return true;
	}

}
