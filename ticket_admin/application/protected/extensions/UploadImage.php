<?php
define('UPLOAD_PATH', realpath(dirname(__FILE__).'/../../').'/upload');

class UploadImage{
	private static $_model = null;
	
	public static function model($className = __CLASS__) {
		if(self::$_model  == null) {
			self::$_model = new $className();
		}
		return self::$_model;
	}
	
	public function upload($file) {
		$filename = 'p'.substr(time(), 6).rand(1001, 9999);
		$allowPicTypes = array('image/jpg'=>'jpg', 'image/pjpeg'=>'jpg', 'image/jpeg'=>'jpg', 'image/gif'=>'gif');
		$fileType = strtolower($file['type']);
		
		
		if(!empty($file) && $file['error'] == 0) {
			$imgMiniType = image_type_to_mime_type($file['tmp_name']);
			
			if(!isset($allowPicTypes[$fileType])) {
				return array(1, '图片格式错误，请上传jpg或gif格式图片！', 0);
			}
			
			if($file['size']/1024 > 500 ) {
				return array(1, '图片大小错误，请上传小于500K的图片！', 0);
			}
			$fileext = $allowPicTypes[$fileType];
			
			// 创建目录
			$uploaddir = $this->makeDir();
			$uploadPath = $uploaddir.$filename.'.'.$fileext;
			$savePath = $uploaddir.'thumb/'.$filename.'.'.$fileext;

			// 上传文件
			if(!move_uploaded_file($file['tmp_name'], $uploadPath)) {
				return array(1, '图片上传失败！', 0);
			}
			
			// 生成缩略图
			$this->mkThumb($uploadPath, $savePath, $fileext);
			
			//本地调试
			list($width, $height) = getimagesize($savePath);
			$uploadPath = str_replace(UPLOAD_PATH, '', $savePath);
			
			# return array(0, $uploadPath, $height);
		
			// 上传头像到 图片服务器 线上调试开启
			$rsync = "/usr/bin/rsync -lrRptW ";
			$uploaddir = str_replace(UPLOAD_PATH, '', $uploaddir);
			$command = "cd ".UPLOAD_PATH."; ".$rsync." .".$uploaddir." ".Yii::app()->params['picServer']['ip']. "::" . Yii::app()->params['picServer']['host']." >/dev/null 2>&1";
			
			try{
				exec($command, $message, $return);
			}catch(Exception $e) {
				$return = false;
			}
			if($return) return array(1, '同步图片失败！', 0);
			
			$param = str_replace(UPLOAD_PATH, '', $savePath);
			return array(0, Yii::app()->params['picServer']['url'].$param, $height);
		}
		
		return array(1, '请选择文件!', 0);
	}
	
	// 创建目录
	public function makeDir() {
		$dir = UPLOAD_PATH.'/'.date('Y').'/'.date('m').'/'.substr(time(), 0, 6).'/thumb';
		@ mkdir($dir,0777,true);
		return substr($dir, 0, -5);
	}

	// 生成缩略图
	public function mkThumb($path, $savePath, $fileext) {
		list($width, $height) = getimagesize($path);
		
		if($width > 100) {
			$toWidth = 100;
			$toHeight = $toWidth / $width * $height;
		} else {
			$toWidth = $width;
			$toHeight = $height;
		}

		$im = imagecreatetruecolor($toWidth, $toHeight);
		switch($fileext) {
			case 'gif': $image = imagecreatefromgif($path); break;
			case 'jpg': $image = imagecreatefromjpeg($path); break;
		}
		imagecopyresampled($im, $image, 0, 0, 0, 0, $toWidth, $toHeight, $width, $height);
		switch($fileext) {
			case 'gif': imagegif($im, $savePath, 100); break;
			case 'jpg': imagejpeg($im, $savePath, 100); break;
		}
	}
}