<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-4-27
 * Time: 上午11:11
 * 该脚本用于通知合作伙伴下单，
 */


require dirname(__FILE__) . '/Base.php';

class Crontab_PartnerOrder extends Process_Base
{
    protected $interval = 5;

    public function run()
    {
        while (true) {
            $this->now = time();
            $this->OpenApiPartnerModel = new OpenApiPartnerModel();
            $this->mc = Cache_Memcache::factory();
            $this->redis = Cache_Redis::factory();
            $this->OrderModel = OrderModel::model();

            $this->addOrder();
            $this->sleep($this->interval);
        }

    }

    //通知合作伙伴（大漠）下单，获取订单号
    public function addOrder()
    {
        $i = 0;
        while ($order_id = $this->redis->lpop('OpenApiPartnerAddOrder')) {
            if(empty($order_id)) {
                continue;
            }
            $i++;
            $order = $this->OrderModel->getById($order_id);
            if (empty($order)) {
                continue;
            }
            $res = $this->OpenApiPartnerModel->partnerAddOrder($order);
            $redoKey = 'OpenApiPartnerAddOrder_' . $order_id . '_redo_times';
            echo "Get Partner Order ID of [{$order_id}]: ";
            if ($res === false) { //如果通知大漠下单失败，重复3次重新通知
                $redoTimes = intval($this->mc->get($redoKey));
                if ($redoTimes < 3) { //如果检查重复
                    $this->redis->rpush('OpenApiPartnerAddOrder', $order_id);
                    $redoTimes++;
                    $this->mc->set($redoKey, $redoTimes, $this->interval * 6);
                    print_r($redoTimes);
                } else {
                    $this->mc->set($redoKey, 0, 1);
                    echo "\nPartner Order Created faid! [OrderID]: " . $order_id . "\n";
                }
            } else if (!empty($res['body']['partner_order_id'])) { //成功后更改订单信息并发短信
                $partner_order_id = $res['body']['partner_order_id'];
                $r = $this->OrderModel->updateById($order_id, array('partner_order_id' => $partner_order_id));
                if (!empty($r) && $order['message_open']==1) {
                    $this->sendSMS($order_id, $order, $partner_order_id); //发短信
                    echo $partner_order_id."\n";
                }
            } else {
                echo "\nPartner Order Created faid! [OrderID]: " . $order_id . "\n";
            }
            if ($i == 20) break; //每次处理20条
        }
    }

    private function sendSMS($order_id, $order, $partner_order_id)
    { //发短信
        /*$cnt = SmsModel::model()->getOrderSmsContentMap($order_id);
        if (!empty($cnt)) { //修改短信模版中的订单号为大漠订单号
            $cnt = str_replace($order_id, $partner_order_id, $cnt);
            $expire_end = $order['expire_end'] > 0 ? $order['expire_end'] : strtotime($order['use_day'] . " 23:59:59");
            $expire = $expire_end + 86400 * 62 - $this->now;
            $this->redis->set('SmsModel|OrderSmsTpl|' . $order_id, urlencode($cnt), $expire);
        }*/ //放在Sms::sendSMS处理
        $order['code'] = $partner_order_id;
        $order['id'] = $partner_order_id;
        $str = Sms::_getCreateOrderContent($order);
        Sms::sendSMS($order['owner_mobile'], urlencode($str), 1, $order_id);
    }

}


$test = new Crontab_PartnerOrder;