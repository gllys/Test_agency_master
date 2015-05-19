<?php
require dirname(__FILE__) . '/Base.php';

class Crontab_Share extends Process_Base 
{
    protected $list = array(array("log", "log_test", "CREATE TABLE `{tblname}` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
          `type` tinyint(4) DEFAULT '0' COMMENT '操作类型',
          `num` int(11) DEFAULT '0' COMMENT '操作数量',
          `content` text COMMENT '内容',
          `organization_id` int(11) NOT NULL COMMENT '机构ID',
          `landscape_id` int(11) NOT NULL COMMENT '景区ID',
          `user_id` int(11) NOT NULL COMMENT '操作人编号',
          `user_name` varchar(64) NOT NULL COMMENT '操作人名称',
          `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"),
    );

    public function run() {
        while (true) {
        	$this->check();
            $this->sleep(86400);
        }
    }

    public function check() {
        foreach($this->list as $item) {
            $db = $item[0];
            $tblname = $item[1] . date('Ym', strtotime('+1 month'));
            $sth = Db_Mysql::factory($db)->prepare("show tables like ?");
            $sth->execute(array($tblname));
            if (!$sth->fetch()) {
                echo "[".date('Y-m-d H:i:s')."]...create $db $tblname...";
                $sql = str_replace('{tblname}', $tblname, $item[2]);
                $rt = Db_Mysql::factory($db)->exec($sql);
                echo $rt!==false ? "ok\n" : "fail\n";
            }
        }
    }
}

$test = new Crontab_Share;
