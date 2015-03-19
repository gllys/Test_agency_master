<?php

class clearCommand extends CConsoleCommand {

        private $_redis;

        public function __construct($name, $runner) {
                parent::__construct($name, $runner);
                $this->_redis = Yii::app()->redis;
        }

        public function run($args) {
               Forums::model()->updateAll(array('todayposts'=>0)) ;
			   //板块相关
               Yii::import('application.controllers.forums.*');
               $forumController = new ForumsController(1);
               $forumController->actionRedis();
        }

}