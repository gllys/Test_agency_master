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
        return Process_Async::send(array(__CLASS__, 'rsync'), array($data));
    }

    public static function rsync($data)
    {
        //echo "start...", PHP_EOL;
        $model = TicketModel::model();
        $model->begin();
        try {
            // Tools::pr($data);
            $model->addNew($data['productInfo'], $data['orderItem']);
            $model->commit();
            //echo $data['productInfo']['id'], " ok ", PHP_EOL;
        } catch (Exception $e) {
            $model->rollback();
            //echo $data['productInfo']['id'], " fail ", PHP_EOL;
             $logs = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            Log_Base::save(self::$_prefix . "FailData", $logs);
        }
    }

    public function getTmpCacheByOrderID($orderID)
    {
        $key = $this->_getKey(":tmp:{$orderID}");
        $data = $this->redis->get($key);
        if(!$data) {
           return false;
        }
        $data = json_decode($data, true);
        if (!$data) {
            return false;
        }
        return $data;
    }

    public function saveTmpCache($orderID, $productInfo, $orderItem)
    {
        $data = json_encode(array(
            'productInfo' => $productInfo,
            'orderItem' => $orderItem,
        ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $redis = $this->getRedis();
        $key = $this->_getKey(":tmp:{$orderID}");
        return $redis->setex($key, self::EXPIREAT_TIME, $data);
    }

    public function saveTmpBatchCache(array $productInfos, array $orderItems)
    {
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