<?php
/**
 * Created by PhpStorm.
 * User: tywei
 * Date: 15/5/18
 * Time: 下午3:26
 */
defined('SMTP_SERVER') || define('SMTP_SERVER', "smtp.qq.com");
defined('SMTP_SSL') || define('SMTP_SSL', true);
defined('SMTP_USERNAME') || define('SMTP_USERNAME', "sa@ihuilian.com");
defined('SMTP_PASSWORD') || define('SMTP_PASSWORD', "1qazXSW@");
defined('SMTP_HELO') || define('SMTP_HELO', "www.ihuilian.com");

class MailModel extends Base_Model_Api
{

    public static function sendSrvGroup($subject, $content)
    {
    	$tos = ['address'=>'srv@ihuilian.com'];
    	$from = 'sa@ihuilian.com';
    	$from_name = 'sa';
        //$content = ""
    	return Mail::sendTextMail($subject, $tos, $from, $from_name, $content);
    }

    public static function sendTicketDevGroup($subject, $content)
    {
        $tos = ['address'=>'ticketdev@ihuilian.com'];
        $from = 'sa@ihuilian.com';
        $from_name = 'sa';
        //$content = ""
        return Mail::sendTextMail($subject, $tos, $from, $from_name, $content);
    }

    public static function sendTo($address, $subject, $content)
    {
    	$tos = ['address'=>$address];
    	$from = 'sa@ihuilian.com';
    	$from_name = 'sa';
    	return Mail::sendTextMail($subject, $tos, $from, $from_name, $content);
    }
}