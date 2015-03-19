<?php
//生成1千万张票
require dirname(__FILE__) . '/Base.php';

class Crontab_GenOta extends Process_Base
{
    public function run() {
       for($i=1001;$i<=2000;$i++){
            $param['id'] = $i;
            $param['name'] = 'name'.$i;
            $param['secret'] = strtoupper(md5(Util_Common::payid('ota')));
            OtaAccountModel::model()->replace($param);
            echo 'add '.json_encode($param)."\n";
       }
    }
}

$test = new Crontab_GenOta;
