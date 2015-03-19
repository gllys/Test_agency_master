<?php
//生成1千万张票
require dirname(__FILE__) . '/Base.php';

class Crontab_Tk1kw extends Process_Base
{
    private $order_id = '166129187502183';
    private $ticket_template_id = 176;

    public function run() {
       $r = TicketModel::model()->setTable($this->order_id)->search(array('order_id'=>$this->order_id),"count(*) as total");
       $total = @reset(reset($r));
       if($total>=10000000) {
           echo "The existing {$total} tickets!\n";
           exit;
       }
       for($i=0;$i<100;$i++) {
            $used = memory_get_usage();
            echo "[".date('Y-m-d H:i:s')."]...";
            $this->insert1WTk();
            $now = memory_get_usage();
            $diff = $now - $used;
            echo "No.{$i}*10000~No.".($i+1)."*10000, memory_get_usage...{$diff}...\n";
            sleep(1);
        }
    }

    public function insert1WTk() {
        $tickets = $fields = array();
        $nowTime = time();
        for($j=0;$j<10000;$j++){
            $data = array();
            $data['id'] = Util_Common::uniqid(2);
            $data['status'] = 1;
            $data['order_id'] = $this->order_id;
            $data['ticket_template_id'] = $this->ticket_template_id;
            $data['poi_list'] = '19,21,20,23,24,25,26,27,28,29,30,31';
            $data['poi_num'] = 12;
            $data['created_at'] = $nowTime;
            $data['updated_at'] = $nowTime;
            if(!$fields)
                $fields = array_keys($data);
            $tickets[] = $data;
        }

        array_unshift($tickets,$fields);
        return TicketModel::model()->setTable($this->order_id)->add($tickets) ? true :false;
    }
}

$test = new Crontab_Tk1kw;
