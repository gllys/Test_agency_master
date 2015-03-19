<?php

/**
 * 文件上传类
 * @author $Author: yuanxx@uuzu.com $
 * @copyright Copyright &copy; 2009-2011 uuzu
 * @version $Id: UFile.php 82259 2013-01-25 01:35:28Z yuanxx@uuzu.com $
 *
 */
class UFile extends CApplicationComponent {

        /**
         * 上传文件保存的本地目录
         *
         * @var string
         */
        public $uploadDir;

        /**
         * rsync命令
         *
         * @var unknown_type
         */
        public $rsyncCommand;
        public $rsync_server;
        public $rsync_module;

        /**
         * 将图片内容分发（同步）到静态服务器
         * @param $siteId 网站编号
         * @param $dir 需求同步的路径
         *
         */
        private function distribute( $dir = '.', $files) {
                if (is_array($files)) {
                        foreach ($files as &$file) {
                                $file = trim($file, "/");
                        }
                        $files = implode(" ", $files);
                }


                $rsyncServer = $this->rsync_server;
                $rsyncModule = $this->rsync_module;

                $arrServer = explode(";", $rsyncServer);
                if (empty($this->rsyncCommand))
                        throw new CHttpException(500, '请先配置rsync命令:rsyncCommand');
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        $dir = preg_replace("/(\/|\\\)/", DIRECTORY_SEPARATOR, $dir);
                        $cdCommand = "cd /d";
                        $comdSeparator = '&';
                } else {
                        $dir = preg_replace("/(\/|\\\)/", DIRECTORY_SEPARATOR, $dir);
                        $cdCommand = "cd";
                        $comdSeparator = ';';
                }
                $output = "";
                foreach ($arrServer as $server) {
                        exec($cdCommand . " " . $dir . $comdSeparator . $this->rsyncCommand . " " . $files . " " . $server . "::" . $rsyncModule, $output, $status);
                }
        }

        /**
         * 上传图片
         * @param string $filedName 表单字段名
         * @param array $thumbSetting 缩略图设置，结构是array(array('width'=>$width,'height'=>$height)...)
         * @return array array(0=>'原图url','1'=>'第一种尺寸的缩略图',...)
         *
         */
        public function uploadImage($fieldName, $thumbSetting = array()) {
                $images = array();
                $uploadObj = CUploadedFile::getInstanceByName($fieldName);
                if (!$uploadObj) {
                        return false;
                }
                $newName = date('His') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); //;

                $baseDir = $this->uploadDir;

                $folder = "/" . date("Y") . "/" . date("md") . "/";
                if (!is_dir($baseDir . $folder)) {
                        if (!mkdir($baseDir . $folder, 0755, true)) {
                                throw new Exception('创造文件夹失败...');
                        }
                }
                if (!$uploadObj->saveAs($baseDir . $folder . $newName . '.' . $uploadObj->extensionName)) {
                        throw new Exception('上传失败');
                }

                $images[] = $folder . $newName . '.' . $uploadObj->extensionName;
                if (strtolower($uploadObj->extensionName) == 'swf') {  //如果flash文件执行这个，其他执行图片格式
                        $this->distribute($baseDir, $images);
                        return implode("|", $images);
                } else {
                        $index = 0;
                        array_unshift($thumbSetting, array('width' => 100, 'height' => 100));
                        foreach ($thumbSetting as $thumbSize) {
                                $image = Yii::app()->image->load($baseDir . $folder . $newName . '.' . $uploadObj->extensionName);
                                $master = CImage::AUTO;
                                $needResize = true; //是否需要重新生成缩略图，在一些情况下只需要将原图复制一下就行了
                                if ($thumbSize['width'] == 0) {//尺寸设置为0时表示和原图保持一致，下同
                                        $master = CImage::HEIGHT;
                                        $thumbSize['width'] = $image->width;

                                        //在缩略图尺寸设置长和宽有一项为0，另一项的值大于原图时，无需生成缩略图，直接将原图复制一份保存
                                        if ($thumbSize['height'] > $image->height)
                                                $needResize = false;
                                }
                                if ($thumbSize['height'] == 0) {
                                        $master = CImage::WIDTH;
                                        $thumbSize['height'] = $image->height;

                                        if ($thumbSize['width'] > $image->width)
                                                $needResize = false;
                                }
                                $m = $image->height / $image->width > $thumbSize['height'] / $thumbSize['width'] ? 'height' : 'width';
                                $tempWidth = $m == 'width' ? $thumbSize['width'] / $thumbSize['height'] * $image->height : $image->width;
                                $tempHeight = $m == 'height' ? $thumbSize['height'] / $thumbSize['width'] * $image->width : $image->height;

                                $thumbName = $newName . "_" . $index . '.' . $uploadObj->extensionName;
                                if ($master == CImage::AUTO) {
                                        $image->crop($tempWidth, $tempHeight);
                                }
                                if ($needResize) {
                                        $image->resize($thumbSize['width'], $thumbSize['height'], $master);
                                }
                                $images[] = $folder . $thumbName;
                                $image->save($baseDir . $folder . $thumbName);
                                $index++;
                        }
                        $this->distribute( $baseDir, $images);
                        return implode("|", $images);
                }
        }

        public function getThumb($thumb, $base) {
                if (empty($thumb))
                        return array();
                $arrThumb = explode("|", $thumb);
                $images = array();
                foreach ($arrThumb as $item) {
                        $images[] = $base . $item;
                }
                return $images;
        }
}