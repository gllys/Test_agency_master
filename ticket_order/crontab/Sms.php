<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/14
 * Time: 14:46
 */
date_default_timezone_set('PRC');
require dirname(__FILE__) . '/Base.php';

defined('SMTP_SERVER') || define('SMTP_SERVER', "smtp.qq.com");
defined('SMTP_SSL') || define('SMTP_SSL', true);
defined('SMTP_USERNAME') || define('SMTP_USERNAME', "sa@ihuilian.com");
defined('SMTP_PASSWORD') || define('SMTP_PASSWORD', "1qazXSW@");
defined('SMTP_HELO') || define('SMTP_HELO', "www.ihuilian.com");

class Crontab_Sms extends Process_Base
{
    /**
     * 计数器
     * @var int
     */
    public static $count_num = 0;

    public function run()
    {
        while (true) {
            $this->SmsLogModel = SmsLogModel::model();
            $this->SmsModel = SmsModel::model();
            $this->smsListener();
            $this->sendsms();
            $this->sleep(10);
        }

    }

    private function sendsms()
    {
        $i = 0;
        while ($sms_content = $this->SmsLogModel->redis->lpop('sms_cache')) {
            $i++;
            $data = json_decode($sms_content, true);
            if ($data['order_id'] && $data['type'] == 1) { //获取订单在redis中解析好的短信模版内容
                $cnt = $this->SmsModel->getOrderSmsContentMap($data['order_id']);
                if ($cnt) $data['content'] = $cnt;
            }
            $r = Sms::doSendSMS($data['mobile'], $data['content']);
            echo '[' . date('Y-m-d H:i:s') . ']' . $data['mobile'] . ':' . $data['content'] . "\n";

            if ($r !== true) {
                // 重试
                $r = Sms::doSendSMS($data['mobile'], $data['content']);
                // $this->SmsLogModel->redis->rpush('sms_cache' , $sms_content);
            }

            if ($r !== true) {
                Log_Base::save('sms_error', '[' . date('Y-m-d H:i:s') . ']' . $data['mobile'] . ':' . $data['content']);
            }
            $this->SmsLogModel->add([
                'sent_at' => time(),
                'mobile' => $data['mobile'],
                'status' => $r == true ? 1 : 2, // 1成功;2失败
                'type' => $data['type'],
                'content' => urldecode($data['content']),
                'order_id' => $data['order_id'],
                'fail_reason' => $r == true ? '' : 'errno:' . $r
            ]);
            if ($r == true) {
                OrderModel::model()->updateByAttr(array('send_sms_nums=send_sms_nums+1'), array('id' => $data['order_id']));
            }
            if ($i == 20) break;
        }
    }

    private function sendemail($data)
    {
//        Mail::sendTextMail('短信发送失败',array('address'=>'fangshixiang@ihuilian.com'),'yinjian@ihuilian.com','yinjian',$data['mobile'].':'.$data['content']);
    }

    /**
     * 心跳同步状态
     * author : yinjian
     */
    private function smsListener()
    {
        if (self::$count_num++ < 60) {
            return true;
        }
        self::$count_num = 0;

        $sms_warning_money = ConfigModel::model()->get(array('config_key' => 'sms_warning_money'));
        $sms_warning_send_email = ConfigModel::model()->get(array('config_key' => 'sms_warning_send_email'));
        $sms_warning_send = ConfigModel::model()->get(array('config_key' => 'sms_warning_send'));
        $sms_warning_send_email = explode(';', $sms_warning_send_email['config_value']);
        if (!$sms_warning_send_email || $sms_warning_money['config_value'] === '' || $sms_warning_send_email['config_value'] === '') {
            return true;
        }

        $smsBalance = Sms::getBalance();
        $sms_balance = round($smsBalance * 0.06); //短信可用余额

        if($sms_warning_send['config_value'] === '1' && $sms_warning_money['config_value'] < $sms_balance){
            // 重置flag = 0
            ConfigModel::model()->updateByAttr(array('config_value' => 0), array('config_key' => 'sms_warning_send'));
        }elseif($sms_warning_send['config_value'] === '0' && $sms_warning_money['config_value'] >= $sms_balance){
            //发email
            $subject = '短信预警通知';
            $sms_warning_send_email = array_unique($sms_warning_send_email);
            foreach ($sms_warning_send_email as $k => $v) {
                $tos[] = array('address' => $v);
            }
            $from = 'sa@ihuilian.com';
            $from_name = 'sa';
            $content = '分销平台短信当前余额低于' . $sms_warning_money['config_value'] . '元，发送短信剩余条数为' . $smsBalance . '条，请及时充值。';
            Mail::sendTextMail($subject, $tos, $from, $from_name, $content);
            ConfigModel::model()->updateByAttr(array('config_value' => 1), array('config_key' => 'sms_warning_send'));
        }

        return true;
    }
}

$sms = new Crontab_Sms();