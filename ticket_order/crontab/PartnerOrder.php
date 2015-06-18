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
            echo "\nGet Partner Order ID of [{$order_id}]: ";
            if ($res === false) { //如果通知大漠下单失败，重复3次重新通知
                $redoTimes = intval($this->mc->get($redoKey));
                if ($redoTimes < 3) { //如果检查重复
                    $this->redis->rpush('OpenApiPartnerAddOrder', $order_id);
                    $redoTimes++;
                    $this->mc->set($redoKey, $redoTimes, $this->interval * 6);
                    echo "\nRetry No.".$redoTimes;
                } else {
                    $this->mc->set($redoKey, 0, 1);
                    // @TODO fix refund
                    echo "\nPartner Order Created faid! [OrderID]: " . $order_id . "\n";
                    $this->applycheck(array(
                        'nums'=>$order['nums'],
                        'order_id'=>$order['id'],
                        'user_id'=>1,
                        'source_id'=>$order['source'],
                        'remark' => '通知合作伙伴［'.$this->OpenApiPartnerModel->partnerTypes[$order['partner_type']].'］下单反馈失败，系统自动退款',
                    ));
                }
            } else if (!empty($res['body']['partner_order_id'])) { //成功后更改订单信息并发短信
                $partner_order_id = $res['body']['partner_order_id'];
                $r = $this->OrderModel->updateById($order_id, array('partner_order_id' => $partner_order_id));
                if (!empty($r) && $order['message_open']==1) {
                    $this->sendSMS($order_id, $order, $partner_order_id); //发短信
                    echo $partner_order_id."\n";
                }
            } else {
                // @TODO fix refund
                echo "\nPartner Order Created faid!! [OrderID]: " . $order_id . "\n";
                $this->applycheck(array(
                    'nums'=>$order['nums'],
                    'order_id'=>$order['id'],
                    'user_id'=>1,
                    'user_account' => 'system',
                    'user_name' => 'system',
                    'source_id'=>$order['source'],
                    'remark' => '通知合作伙伴［'.$this->OpenApiPartnerModel->partnerTypes[$order['partner_type']].'］下单反馈失败，系统自动退款',
                ));
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

    /**
     * 申请并退款 预处理 提示错误
     * author : yinjian
     * @param $params
     */
    public function applycheck($params)
    {
        if (intval($params['nums']) < 1) {
            echo Lang_Msg::getLang('ERROR_REFUNDAPPLY_1')."\n";
            return false;
        }
        if(!$params['order_id'] && !$params['source_id']) {
            echo Lang_Msg::getLang('ERROR_REFUNDAPPLY_2')."\n";
            return false;
        }
        if(!$params['user_id']) {
            echo Lang_Msg::getLang('ERROR_REFUNDAPPLY_3')."\n";
            return false;
        }
        // 订单是否存在
        $this->OrderModel->begin();
        try {
            $orderModel = new OrderModel();
            $orderItemModel = new OrderItemModel();
            $where = array('deleted_at' => 0);
            $params['order_id'] && $where['id'] = $params['order_id'];
            $params['source_id'] && $where['source_id'] = $params['source_id'];
            $order = reset($orderModel->search($where));
            if(!$order) {
                echo Lang_Msg::getLang('ERROR_REFUNDAPPLY_4')."\n";
                return false;
            }
            // 是否使用了优惠券
            if ($order['activity_paid'] > 0 && $params['nums'] != $order['nums']) {
                echo '使用优惠券的订单只能退全部票数'."\n";
                return false;
            }
            // 未支付单不能退款
            if(!in_array($order['status'], array('paid', 'finish'))) {
                echo Lang_Msg::getLang('ERROR_REFUNDAPPLY_5')."\n";
                return false;
            }
            // 可退票数=总票数-已使用票数-退款中票数-已退款张数
            $remain_ticket = $order['nums'] - $order['used_nums'] - $order['refunding_nums'] - $order['refunded_nums'];
            if($params['nums'] > $remain_ticket) {
                echo Lang_Msg::getLang('ERROR_REFUNDAPPLY_6')."\n";
                return false;
            }
            // 票模板为可退属性
            if($order['refund'] == 0) {
                echo Lang_Msg::getLang('ERROR_REFUNDAPPLY_7')."\n";
                return false;
            }
            // 根据可退产品数来进行筛选order_items
            $order_item = $orderItemModel->getCacheByOrderId($order['id']);
            $order_item_ids = [];
            $i = 0;
            foreach ($order_item as $itemID=>&$status) {
                if ($status == 1) {
                    $i++;
                    $order_item_ids[] = $itemID;
                    $status = 0;
                }
                if ($i >= $params['nums']) {
                    break;
                }
            }
            if (empty($order_item_ids)) {
                echo Lang_Msg::getLang('没有可退票！')."\n";
                return false;
            }
            if ($params['nums']>count($order_item_ids)) {
                echo Lang_Msg::getLang('ERROR_REFUNDAPPLY_6')."\n";
                return false;
            }
            $clacApply = $this->clacApply($order, $params);
            if(!$clacApply){
                return false;
            }
            $clacApply['refund_apply_id'] = Util_Common::payid(2);
            //更新缓存
            $orderItemModel->setCacheByOrderId($order['id'], $order_item);
            // 申请退票操作
            $res = Process_Async::presend([get_class(new RefundApplyModel()), 'asyncApplyAndCheck'], [$order, $order_item_ids, $params, $clacApply]);
            if ($res == false) {
                echo Lang_Msg::getLang('操作失败!')."\n";
                $this->OrderModel->rollback();
                return false;
            }
//            $res = $this->applyAndCheck($order, $order_item_ids, $params, $clacApply);
            $this->OrderModel->commit();
            return $clacApply['refund_apply_id'];
        } catch(Exception $e) {
            $this->OrderModel->rollback();
            echo Lang_Msg::getLang('ERROR_REFUNDAPPLY_8')."\n";
            return false;
        }
    }

    /**
     * @param $order
     * @param $data
     * @return array
     */
    private function clacApply($order, $data)
    {
        // 金额计算
        $money = $order['price'] * $data['nums'];
        $refunded_nums = $order['refunded_nums'] + $data['nums'];
        $refunded = $order['refunded'] + $money;

        $orderParams = array('refunded_nums' => $refunded_nums, 'refunded' => $refunded);
        if ($order['used_nums'] == $order['nums'] - $order['refunded_nums'] - $data['nums']) {
            $orderParams['status'] = 'finish';
        }
        if ($order['refunding_nums'] > 0) {
            $orderParams['refund_status'] = 1;
        } elseif ($orderParams['refunded_nums'] > 0) {
            $orderParams['refund_status'] = 2;
        }
        if ($refunded_nums > $order['nums']) {
            echo Lang_Msg::getLang("没有可退票!")."\n";
            return false;
        }
        return array('money'=>$money, 'orderParams'=>$orderParams);
    }

}


$test = new Crontab_PartnerOrder;