<?php
require dirname(__FILE__) . '/Base.php';

class Crontab_GenTicketCode extends Process_Base 
{
    public function run() {
        $list = TicketTemplateModel::model()->search(array('ota_code'=>''));
        if(!$list) {
            return ;
        }
        foreach($list as $item) {
            $this->setTicketCode($item);
        }
    }

    public function setTicketCode($item) {
        echo "deal {$item[id]}...";
        try{
            $code = Util_Common::genTicketCode(microtime(true));
            TicketTemplateModel::model()->updateById($item['id'], array('ota_code'=>$code));
            echo "ok, code:{$code}\n";
        }catch(Exception $e) {
            $msg = $e->getMessage();
            echo "error:{$msg}\n";
        }
    }
}

$test = new Crontab_GenTicketCode;
