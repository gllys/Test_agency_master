<?php

class MedalsImageRsyncCommand extends CConsoleCommand {

        public function run($args) {
                $medals = Medals::model()->findAll();
                $attributes = array();
                if (!is_null($medals)) {
                        foreach ($medals as $item) {
                                $images = array();
                                $medalInfo = Medals::model()->findByPk($item->medalid);
                                $images[] = $medalInfo->image;
                                $this->distribute(dirname(__FILE__) . '/../../images/common/', $images);
                                $attributes['Medals']['image'] = '/'.$medalInfo->image.'|'.'/'.$medalInfo->image;
                                $medalInfo->attributes = $attributes['Medals'];
                                $result = $medalInfo->save();
                                if ($result) {
                                        $medalInfo = Medals::model()->findByPk($item->medalid);
                                        Yii::app()->redis->set('medals:' . intval($item->medalid), json_encode($medalInfo->attributes));
                                }
                        }
                }
        }

        private function distribute($dir = '.', $files) {
                if (is_array($files)) {
                        foreach ($files as &$file) {
                                $file = trim($file, "/");
                        }
                        $files = implode(" ", $files);
                }


                $rsyncServer = Yii::app()->UFile->rsync_server;
                $rsyncModule = Yii::app()->UFile->rsync_module;

                $arrServer = explode(";", $rsyncServer);
                if (empty(Yii::app()->UFile->rsyncCommand))
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
                        exec($cdCommand . " " . $dir . $comdSeparator . Yii::app()->UFile->rsyncCommand . " " . $files . " " . $server . "::" . $rsyncModule, $output, $status);
                }
        }

}