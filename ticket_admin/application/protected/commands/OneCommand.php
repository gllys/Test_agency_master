<?php

class OneCommand extends CConsoleCommand {

        public function run($args) {
               $this->delThreads();
        }
		
        public function delThreads(){
               $sql = "SELECT tid FROM `threads` WHERE `displayorder` = -1";
	       $result = Yii::app()->db->createCommand($sql)->queryColumn();
               foreach ($result as $tid) {
                     $sql = "DELETE FROM `posts` WHERE `tid` = {$tid}";
                    $rs = Yii::app()->db->createCommand($sql)->execute();
                    if($rs)$tid."帖子删除成功" ;
                    
                    $sql  = "DELETE FROM `threads` WHERE `tid` = {$tid}" ;
                    $rs  = Yii::app()->db->createCommand($sql)->execute();
                    if($rs ) $tid."主题删除成功" ;
               }
	}

}