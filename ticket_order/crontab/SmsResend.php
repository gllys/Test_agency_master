<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/2/6
 * Time: 10:27
 */

date_default_timezone_set('PRC');
require dirname(__FILE__) . '/Base.php';

class Crontab_SmsResend extends Process_Base
{
    /**
     * [run description]
     * @return [type] [description]
     */
    public function run()
    {
        while (true) {
            $now = time();
            $st = $now - 7200;
            $et = $now - 600;
            $orders = OrderModel::model()->search(array('created_at|between'=>array($st,$et),'local_source'=>2,'nums|exp'=>'>used_nums+refunding_nums+refunded_nums','status'=>'paid'));
            foreach($orders as $k=>$v){
                $count = intval($this->check($k, $v['created_at']));
                if ($count>0) {
                    continue;
                }
                echo 'order:'.$v['id'].' '.date('Y-m-d H:i:s', $v['created_at'])."\n";
                $str = Sms::_getCreateOrderContent($v);
                echo $str."\n";
                Sms::sendSMS($v['owner_mobile'],urlencode($str),1,$v['id']);
                $this->savelog($k, $v['created_at']);
            }
            sleep(600);
        }
    }

    protected function check($id, $date) {
        $sd = date('Ymd', $date);
        $log = '/data/web/ticket_order/log/Sms.php_'.$sd;
        return shell_exec("more $log | grep '{$id}' | awk '{count++}END{print count}'");
    }

    protected function savelog($id, $date) {
        $sd = date('Ymd', $date);
        $log = 'Sms.php_'.$sd;
        Log_Base::save($log,'['.date('Y-m-d H:i:s').']resend:'.$id);
    }
}

$sms_send = new Crontab_SmsResend();