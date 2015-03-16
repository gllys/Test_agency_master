<?php
require dirname(__FILE__) . '/Base.php';

class Crontab_Share extends Process_Base 
{
    protected $list = array();
    public function run() {
        $config = new Yaf_Config_Ini(APPLICATION_PATH.'/conf/share.ini','common');
        $this->list = $config->share->toArray();
        while (true) {
        	$this->check();
            $this->sleep(86400);
        }
    }

    public function check() {
        foreach($this->list as $db=>$items) {
            $sth = Db_Mysql::factory($db)->prepare("show tables like ?");
            foreach($items as $basename => $item) {
              // 当月
              $tblname = $basename . date('Ym');
              $sth->execute(array($tblname));
              if (!$sth->fetch()) {
                  echo "[".date('Y-m-d H:i:s')."]...create $db $tblname...";
                  $sql = str_replace('{tblname}', $tblname, $item);
                  $rt = Db_Mysql::factory($db)->exec($sql);
                  echo $rt!==false ? "ok\n" : "fail\n";
              }
              // 下月
              $tblname = $basename . date('Ym', strtotime('+1 month'));
              $sth->execute(array($tblname));
              if (!$sth->fetch()) {
                  echo "[".date('Y-m-d H:i:s')."]...create $db $tblname...";
                  $sql = str_replace('{tblname}', $tblname, $item);
                  $rt = Db_Mysql::factory($db)->exec($sql);
                  echo $rt!==false ? "ok\n" : "fail\n";
              }
            }
        }
    }
}

$test = new Crontab_Share;
