<?php

/**
 * Class SmsModel
 */
class SmsModel extends Base_Model_Abstract
{
    const PI_APP_DOMAIN = "www.piaotai.com";

    protected $orderSmsTplCacheKey = 'SmsModel|OrderSmsTpl|';

    /**
     * 短信信息发送
     * @author yinjian
     * @date   2014-08-25
     * @return [type]     [description]
     */
    public function sendSms($order)
    {  
        $str = '【景旅通票台】您已成功预订'.$order['name'].$order['nums'].'张，订单号：'.$order['id'].'，您可在使用有效期内游玩，通过以下二维码链接进行手机二维码：'.$this->getCodeUrl($order['code']).' 入园。';
        return Sms::sendSMS($order['mobile'],urlencode($str),1,$order['id']);
    }

    public function sendRefunds($order)
    {
        $str = '您申请的【拉手网】'.$order['name'].$order['nums'].'张退款，已经为您办理，退款金额会在7个工作日退还到您的支付账户。';
        return Sms::sendSMS($order['mobile'],urlencode($str));
    }
    
    public function getCodeUrl( $code )
    {
    	return 'http://'.self::PI_APP_DOMAIN.'/qr/'.$code.' ';
    }

    public function orderSms($orderInfo=array()){
        if(is_string($orderInfo)) $orderInfo = OrderModel::model()->getById($orderInfo);
        if(!$orderInfo) return false;

        $orderItemInfo = reset(OrderItemModel::model()->setTable($orderInfo['id'])->search(array('order_id'=>$orderInfo['id'])));
        $str = '【景旅通票台】';
        $str .= '您已成功预订 「'.$orderItemInfo['name']."」门票 ".$orderInfo['nums'].' 张，订单号：'.$orderInfo['id'].'，可于：'.$orderInfo['use_day'].' ~ '.date("Y-m-d",$orderItemInfo['expire_end']).'游玩，';
        $url = 'http://www.piaotai.com/qr/'.$orderInfo['id'];
        $str.='点击以下链接，向工作人员展示二维码，工作人员扫描后即可入园。 '."\n".$url;

        Sms::sendSMS($orderInfo['owner_mobile'],urlencode($str),1,$orderInfo['id']);
        return true;
    }


    //把产品的短信模版解析后保存redis
    public function setOrderSmsTemplateMap($productInfo,$orderInfo){
        if(!$productInfo || !$productInfo['sms_template'] || !$orderInfo) return false;
        $cacheKey = $this->orderSmsTplCacheKey.$orderInfo['id'];
        $smsContent = $this->parseOrderSmsContent($productInfo['sms_template'],$orderInfo);
        if($smsContent){
            $expire_end = $orderInfo['expire_end']?$orderInfo['expire_end']:strtotime($orderInfo['use_day']." 23:59:59");
            $expire = $expire_end + 86400*62 - time();
            $smsContent = '【景旅通票台】'.$smsContent;
            if ($orderInfo['supplier_id']==204) {
                $smsContent .= ' 应急电话：18939755352、13361985062';
            }
            $this->redis->push('set', array($cacheKey , urlencode($smsContent), $expire));
            return true;
        }
        else return false;
    }

    public function parseOrderSmsContent($sms_template,$orderInfo){ //解析订单短信模版
        if(!$sms_template || !$orderInfo) return false;
        //您已成功预订 「{{{产品名称}}}」门票 {{{产品数量}}}张，订单号：{{{订单号}}}，点击以下链接，至售票处展示二维码，
        //工作人员扫描后即可入园。 {{{二维码链接}}}，可于：{{{游玩日期}}}游玩
        $url = $this->getCodeUrl($orderInfo['code']);
        $use_day = date('Y-m-d',$orderInfo['expire_start']);
        $endtime = date('Y-m-d',$orderInfo['expire_end']);
        if ($endtime == $use_day) {
            $endtime = "当天";
        } else {
            $endtime = "~" . $endtime;
        }
        $sms_template = preg_replace(
            array(
                '/\{\{\{产品名称(\s*)?\}\}\}/',
                '/\{\{\{产品数量(\s*)?\}\}\}/',
                '/\{\{\{订单号(\s*)?\}\}\}/',
                '/\{\{\{二维码链接(\s*)?\}\}\}/',
                '/\{\{\{游玩日期(\s*)?\}\}\}/',
                '/&nbsp;/'
            ),
            array(
                '［'.$orderInfo['name'].'］',
                $orderInfo['nums'].'张',
                $orderInfo['id'],
                $url,
                $orderInfo['use_day'] . $endtime,
                ''
            ),
            $sms_template
        );
        return strip_tags($sms_template);
    }

    public function getOrderSmsContentMap($order_id){ //获取订单短信内容
        if(!$order_id) return false;
        $cacheKey = $this->orderSmsTplCacheKey.$order_id;
        return $this->redis->get($cacheKey);
    }

    public function delOrderSmsContentMap($order_id){ //删除订单短信内容
        if(!$order_id) return false;
        $cacheKey = $this->orderSmsTplCacheKey.$order_id;
        return $this->redis->del($cacheKey);
    }

}