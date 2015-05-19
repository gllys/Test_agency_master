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
    public static function sendSMS($mobile, $content,$type=0,$order_id='')
    {
        $data['mobile'] = $mobile;
        $data['content'] = $content;
        $data['type'] = $type;
        $data['order_id'] = $order_id;
        $cnt='';
        if($order_id && $type==1){ //获取订单在redis中解析好的短信模版内容
            $cnt = SmsModel::model()->getOrderSmsContentMap($order_id);
            if(!empty($cnt)) {
                $data['content'] = $cnt;
            }
        }
        if($order_id!='') {
            $order = OrderModel::model()->getById($order_id);
            if(!empty($order) && $order['partner_type']>0 && $order['partner_product_code']!='') { //判断是否合作伙伴大漠产品
                if($order['partner_order_id']!='' && $order['message_open']==1) { //已有大漠订单号，则短信中系统订单号替换为大漠订单号
                    $data['content'] = str_replace($order_id, $order['partner_order_id'], $data['content']);
                    if(!empty($cnt)) {
                        $expire_end = $order['expire_end'] > 0 ? $order['expire_end'] : strtotime($order['use_day'] . " 23:59:59");
                        $expire = $expire_end + 86400 * 62 - time();
                        OrderModel::model()->redis->set('SmsModel|OrderSmsTpl|' . $order_id, $data['content'], $expire);
                    }
                } else { //否则不发短信
                    return true;
                }
            }
        }
        OrderModel::model()->redis->rpush('sms_cache' , json_encode($data));
        return true;
    }

    public static function doSendSMS($mobile, $content)
    {
        $request = self::HOST.':'.self::PORT.'/sdkproxy/sendsms.action';
        $request.="?cdkey=".self::CDKEY."&password=".self::PASSWORD."&phone=".$mobile."&message=".$content.'&addserial=10086';
        $result  = Tools::curl($request);
        $result  = simplexml_load_string(trim($result));
        if($result->error == '0') {
            return true;
        } else {
            Log_Base::save('sms_error','errno:'.$result->error);
            return false;
        }
    }

    public static function _getCreateOrderContent($orderInfo) {
        $use_day = date('Y-m-d',$orderInfo['expire_start']);
        $endtime = date('Y-m-d',$orderInfo['expire_end']);
        if ($endtime == $use_day) {
            $endtime = "当天";
        } else {
            $endtime = "~" . $endtime;
        }
        $url = SmsModel::model()->getCodeUrl($orderInfo['code']);//'http://www.piaotai.com' . '/qr/' . $orderInfo['code'];
        $str = '【景旅通票台】';
        $str .= '您已成功预订 「' . $orderInfo['name'] . "」门票 " . $orderInfo['nums'] . ' 张，订单号：' . $orderInfo['id'];
        $str.='，点击以下链接，至售票处展示二维码，工作人员扫描后即可入园。 ' . $url.' ';
        $str .= '，可于：' . $orderInfo['use_day'] . $endtime . '游玩';
        /* 获取地址字符串 */
        $landscapeIds = explode(',', $orderInfo['landscape_ids']);
        if (count($landscapeIds) == 1) {
            $addresses = "";
            foreach ($landscapeIds as $v) {
                $rs = LandscapeModel::model()->getDetail($v);
                $rs && $addresses .= $rs['address'];
            }
            if (!empty($addresses)) {
                $str .= '，景区地址：' . $addresses;
            }
        }
        if ($orderInfo['supplier_id']==204) {
            $str .= '。应急电话：18939755352、13361985062';
        }
        //须加签名，不然发不出去
        return $str;
    }

    /**
     * 获取短信可用数量
     */
    public static function getBalance() {
        $url    = self::HOST.':'.self::PORT.'/sdkproxy/querybalance.action?cdkey='.self::CDKEY."&password=".self::PASSWORD;
        $xml = Tools::curl($url);

        $result = simplexml_load_string(trim($xml));
        if($result->error == '0') {
            return floatval($result->message)*10 ;
        } else {
            return 0;
        }
    }
}