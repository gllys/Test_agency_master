<?php
/**
 *  短信接口
 *
 *	http://sdk999ws.eucp.b2m.cn:8080?cdkey=0SDK-EMY-0130-AAAAA&password=123456&phone=1333333333,13444444444&message=单发即时短信测试&addserial=10086
 *
 * cdkey	用户序列号。
 * password	用户密码
 * phone	手机号码（最多1000个），多个用英文逗号(,)隔开。
 * message	短信内容（UTF-8编码）（最多500个汉字或1000个纯英文）。
 * addserial	附加号（最长10位，可置空）。
 *
 * 2013-09-04
 *
 * @author  yinjian(yinjian@ihuilian.com)
 * @version 1.0
 */
class Sms
{
    const HOST     = 'http://sdk999ws.eucp.b2m.cn';
    const PORT     = '8080';
    const CDKEY    = '9SDK-EMY-0999-JBTRP';
    const PASSWORD = '771459';

    /**
     * 单一号码，单一内容下发
     *
     *  * @return String
     */
    public static function sendSMS($mobile, $content)
    {
        $request = self::HOST.':'.self::PORT.'/sdkproxy/sendsms.action';
        $request.="?cdkey=".self::CDKEY."&password=".self::PASSWORD."&phone=".$mobile."&message=".$content;
        $result  = Tools::curl($request);
        $result  = simplexml_load_string(trim($result));
        if($result->error == '0') {
            return true;
        } else {
            return false;
        }
    }
}