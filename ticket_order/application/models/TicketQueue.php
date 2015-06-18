<?php

class TicketQueueModel extends Base_Model_Abstract
{
    static private $_prefix = 'TicketQueueModel';
    const EXPIREAT_TIME = 172800;//2 * 3600 * 24; //缓存有效

    public function sendOrderIds($orderIDs)
    {
        $data = array();
        foreach ($orderIDs as $orderID) {
            if (!$data = $this->getTmpCacheByOrderID($orderID)) {
                return false;
            }
            if (self::send($data) == false) {
                return false;
            }
        }
        return true;
    }

    public static function send(array $data)
    {
        // return static::rsync($data);
        return Process_Async::presend(array(__CLASS__, 'rsync'), array($data));
    }

    public static function rsync($data)
    {
        //echo "start...", PHP_EOL;
        $model = TicketModel::model();
        $model->begin();
        try {
            $result = $model->addNew($data['productInfo'], $data['orderItem']);
            if (!$result) {
                throw new Exception("操作失败 message: ".var_export($result, true));
            }
            $model->commit();
            //echo $data['productInfo']['id'], " ok ", PHP_EOL;
        } catch (Exception $e) {
            $model->rollback();
            //echo $e->getMessage(), PHP_EOL;
            //echo $data['productInfo']['id'], " fail ", PHP_EOL;
            $logs = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            //保存参数并发邮件通知
            $args = var_export($data, true);
            $content = 'method: '.__METHOD__."\n message: ". $e->getMessage() ."\n params: {$args}";
            MailModel::sendSrvGroup("下单异步操作失败", $content);
            Log_Base::save(self::$_prefix . "FailData", $logs);
            return false;
        }
        return true;
    }

    public function getTmpCacheByOrderID($orderID)
    {
        /*$key = $this->_getKey(":tmp:{$orderID}");
        $data = $this->redis->get($key);
        if(!$data) {
           return false;
        }
        $data = json_decode($data, true);
        if (!$data) {
            return false;
        }*/
        $order = OrderModel::model()->getById($orderID);
        $orderItem = OrderItemModel::model()->search(['order_id'=>$orderID]);
        if (!$order || !$orderItem) {
            throw new Lang_Exception("该订单不存在");
        }
        $data = [
            'productInfo'=>[
                'id'=>$order['product_id'],
                'items'=>json_decode($order['ticket_infos'], true)
            ],
            'orderItem'=>$orderItem,
        ];
        return $data;
    }

    public function saveTmpCache($orderID, $productInfo, $orderItem)
    {
        return true;
        $data = $orderID;
        $redis = $this->getRedis();
        $key = $this->_getKey(":tmp:{$orderID}");
        return $redis->setex($key, self::EXPIREAT_TIME, $data);
    }

    public function saveTmpBatchCache(array $productInfos, array $orderItems)
    {
        return true;
        $data = array();
        foreach($orderItems as $orderItem) {
            $orderID = $orderItem['order_id'];
            $data[$orderID]['productInfo'] = $productInfos[$orderID];
            $data[$orderID]['orderItem'][] = $orderItem;
        }
        unset($orderItem, $orderID);
        foreach ($data as $orderID=>$d) {
            if ($this->saveTmpCache($orderID, $d['productInfo'], $d['orderItem']) == false) {
                return false;
            }
        }
        return true;
    }

    public function test()
    {
        $redis = $this->redis;
        //$redis->hSet(__CLASS__,'test', 'testValue');
        $r = $redis->hgetall(__CLASS__);
        var_dump($r);
        die();
    }

    private function _getKey($key)
    {
        return self::$_prefix . $key;
    }
}