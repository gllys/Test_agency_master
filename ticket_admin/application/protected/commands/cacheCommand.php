<?php

class CacheCommand extends CConsoleCommand {

        private $_redis;

        public function __construct($name, $runner) {
                parent::__construct($name, $runner);
                $this->_redis = Yii::app()->redis;
        }

        public function run($args) {

                //敏感词库
                $words = Words::model()->findAll();
                if (!is_null($words)) {
                        foreach ($words as $item) {
                                $this->_redis->sAdd('words', $item->find);
                        }
                }
                //用户组
                $groupmembers = GroupMembers::model()->findAll();
                if (!is_null($groupmembers)) {
                        foreach ($groupmembers as $item) {
                                $this->_redis->sAdd("groupmembers:{$item->groupid}", $item->account);
                        }
                }
                //禁止IP
                $ipbanned = Ipbanned::model()->findAll();
                if (!is_null($ipbanned)) {
                        foreach ($ipbanned as $item) {
                                $this->_redis->zAdd('ipbanned', $item->expiration, $item->ip);
                        }
                }
                //黑名单
                $blacklist = Blacklist::model()->findAll();
                if (!is_null($blacklist)) {
                        foreach ($blacklist as $item) {
                                $this->_redis->zAdd('blacklist', $item->expiration, $item->account);
                        }
                }
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
                        $result2 = $command->select('tid,displayorder,lastposttime')
                                ->from('threads')
                                ->where('fid=:fid', array(':fid' => $item['fid']))
                                ->queryAll();
                        if (!is_null($result2)) {
                                foreach ($result2 as $item2) {
                                        if ($item2['displayorder'] >= 0) {
                                                if ($item2['displayorder'] == 1) {
                                                        $item2['lastposttime'] += 1000000000;
                                                }
                                                $this->_redis->zAdd('forum_threads:' . $item['fid'], $item2['lastposttime'], $item2['tid']);
                                        }
                                }
                        }
                }

                //帖子回复
                $total = Posts::model()->count();
                $pageSise = 1000;
                $pages = intval($total / $pageSise) + 1;
                for ($i = 1; $i <= $pages; $i++) {
                        $command->reset();
                        $result = $command->select('pid,tid,dateline')
                                ->from('posts')
                                ->where('`first` <> :first', array('first' => 1))
                                ->limit($pageSise, ($i - 1) * $pageSise)
                                ->queryAll();
                        if (!is_null($result)) {
                                foreach ($result as $item) {
                                        $this->_redis->zAdd('thread_posts:' . $item['tid'], $item['dateline'], $item['pid']);
                                }
                        }
                }
                //投票贴投票日志
                $polllogs = Polllogs::model()->findAll();
                if (!is_null($polllogs)) {
                        foreach ($polllogs as $item) {
                                // $this->_redis->sAdd('polllogs:' . $item->tid . ':' . $item->polloptionid, $item->account);
                                $this->_redis->sAdd('polllogs:' . $item->tid, $item->account);
                        }
                }
                //勋章详细数据
                $medals = Medals::model()->findAll();
                if (!is_null($medals)) {
                        foreach ($medals as $item) {
                                $this->_redis->set('medals:' . $item->medalid, json_encode($item->attributes));
                        }
                }
                //勋章账户
                $command->reset();
                $medallog = $command->select('account,medalid')
                        ->from('medallog')
                        ->queryAll();
                if (!is_null($medallog)) {
                        foreach ($medallog as $item) {
                                $this->_redis->sAdd('medallog:' . $item['account'], $item['medalid']);
                        }
                }
        }

}