<?php

class Cache2Command extends CConsoleCommand {

        private $_redis;

        public function __construct($name, $runner) {
                parent::__construct($name, $runner);
                $this->_redis = Yii::app()->redis;
        }

        public function run($args) {
                //板块相关
                Yii::import('application.controllers.forums.*');
                $forumController = new ForumsController(1);
                $forumController->actionRedis();
                $forumController->actionRedisForumThreadtypes();
                $forumController->actionRedisForumModerators();
                $threadTypeController = new ThreadtypesController(1);
                $threadTypeController->actionRedis();
                //板块帖子
                $command = Yii::app()->db->createCommand();
                $result = $command->select('fid')
                        ->from('forums')
                        ->queryAll();
                foreach ($result as $item) {
                        $command->reset();
                        $result2 = $command->select('tid,displayorder,dateline')
                                ->from('threads')
                                ->where('fid=:fid', array(':fid' => $item['fid']))
                                ->queryAll();
                        if (!is_null($result2)) {
                                foreach ($result2 as $item2) {
                                        if ($item2['displayorder'] >= 0) {
                                                if ($item2['displayorder'] > 0) {
                                                        $item2['dateline'] *= 1000000000;
                                                }
                                                $this->_redis->zAdd('forum_threads:' . $item['fid'], $item2['dateline'], $item2['tid']);
                                        }
                                }
                        }
                }
        }

}