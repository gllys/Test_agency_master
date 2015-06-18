<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-1-21
 * Time: 上午11:43
 */

class TicketItemModel extends Base_Model_Abstract
{

    protected $dbname = 'itourism';
    protected $tblname = 'ticket_items';
    protected $basename = 'ticket_items';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|TicketItemsModel|';
    protected $autoShare = 1;
    protected $list = array();

    public function getTable()
    {
        return $this->tblname;
    }

    public function setTable($id = 0)
    {
        //if (!$id)
        //    $this->tblname = $this->basename . date('Ym');
        //else
        //    $this->tblname = $this->basename . Util_Common::uniqid2date($id);
        return $this;
    }

    public function share($ts = 0)
    {
        //if (!$ts)
        //    $ts = time();
        //$this->tblname = $this->basename . date('Ym', $ts);
        return $this;
    }

    //生成验票记录
    public function addBatch($tickets)
    {
        $ticketInfo = reset($tickets);
        $this->setTable($ticketInfo['order_id']);
        $tkItems = $tkItem_ids = $items = array();
        $i = 0;
        foreach($tickets as $ticket){
            $view_point = explode(',', $ticket['poi_list']);
            foreach($view_point as $poi_id){
                $i++;
                $id = '5'.substr("{$ticket['order_id']}", 1)."{$i}".mt_rand(10,99);
                $data = array();
                $data['id'] = $id;
                $data['ticket_id'] = $ticket['id'];
                $data['order_id'] = $ticket['order_id'];
                $data['order_item_id'] = $ticket['order_item_id'];
                $data['landscape_id'] = $ticket['landscape_id'];
                $data['poi_id'] = $poi_id;
                $data['status'] = 1; //状态：0：不可使用 1：可使用 2已使用，支付后状态为1
                $data['created_at'] = $ticket['created_at'];
                $data['updated_at'] = $ticket['updated_at'];
                $tkItem_ids[] = $data['id'];
                $tkItems[] = $data;
                $items[$ticket['order_id']][$id] = json_encode($data);
            }
        }
        foreach ($items as $orderID => $d) {
            $this->setCacheByOrderId($orderID, $d);
        }
        array_unshift($tkItems, array_keys(reset($tkItems)));
        return $this->add($tkItems)?$tkItems:false;
    }

    public function setCacheByOrderId($orderID, $ticketItems)
    {
        return $this->redis->push('hmset', array("{$this->preCacheKey}{$orderID}", $ticketItems));
    }
    
    public function deleteRedisCache($orderID)
    {
        return $this->redis->push('del', array("{$this->preCacheKey}{$orderID}"));
    }

    public function getCacheByOrderId($orderID)
    {
        $cacheKey = "{$this->preCacheKey}{$orderID}";
        $items = $this->redis->hgetall($cacheKey);
        if ($items) {
            $data = array();
            foreach ($items as $d) {
                $d = json_decode($d, true);
                $data[$d['id']] = $d;
            }
            return $data;
        }
        $data = $this->setListKey('id')->search(array('order_id'=>$orderID));
        if (is_array($data) && $data) {
            $cacheData = [];
            foreach ($data as $key => $value) {
                $cacheData[$key] = json_encode($value);
            }
            $this->redis->hmset($cacheKey, $cacheData);
        }
        return $data;
    }
}