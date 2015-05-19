<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/14
 * Time: 14:46
 */
date_default_timezone_set('PRC');
require dirname(__FILE__) . '/Base.php';

define('SMTP_SERVER',"smtp.exmail.qq.com");
define('SMTP_SSL', TRUE);
define('SMTP_USERNAME', "yinjian@ihuilian.com");
define('SMTP_PASSWORD', "");
define('SMTP_HELO', "www.ihuilian.com");

class Crontab_Sms extends Process_Base
{
    public function run()
    {
        while (true) {
            $this->SmsLogModel=SmsLogModel::model();
            $this->SmsModel = SmsModel::model();
            $this->sendsms();
            $this->sleep(10);
        }

    }

    private function sendsms()
    {
        $i = 0;
        while($sms_content = $this->SmsLogModel->redis->lpop('sms_cache')){
            $i++;
            $data = json_decode($sms_content,true);
            if($data['order_id'] && $data['type']==1){ //获取订单在redis中解析好的短信模版内容
                $cnt = $this->SmsModel->getOrderSmsContentMap($data['order_id']);
                if($cnt) $data['content'] = $cnt;
            }
            $r = Sms::doSendSMS($data['mobile'],$data['content']);
            echo '['.date('Y-m-d H:i:s').']'.$data['mobile'].':'.$data['content'] ."\n";

            if($r !== true){
                // 重试
                $r = Sms::doSendSMS($data['mobile'],$data['content']);
                // $this->SmsLogModel->redis->rpush('sms_cache' , $sms_content);
            }
            
            if($r !== true){
                Log_Base::save('sms_error', '['.date('Y-m-d H:i:s').']'.$data['mobile'].':'.$data['content']);
            }
            $this->SmsLogModel->add([
                'sent_at'    => time(),
                'mobile'     => $data['mobile'],
                'status'     => $r==true? 1: 2, // 1成功;2失败
                'type'       => $data['type'],
                'content'    => urldecode($data['content']),
                'order_id'   => $data['order_id'],
                'fail_reason'=> $r==true? '': 'errno:'.$r
            ]);
            if($i == 20) break;
        }
    }

    private function sendemail($data)
    {
//        Mail::sendTextMail('短信发送失败',array('address'=>'fangshixiang@ihuilian.com'),'yinjian@ihuilian.com','yinjian',$data['mobile'].':'.$data['content']);
    }
}

$sms = new Crontab_Sms();