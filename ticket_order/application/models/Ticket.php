<?php

/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-24
 * Time: 下午6:14
 */
class TicketModel extends Base_Model_Abstract {

    protected $dbname = 'itourism';
    protected $tblname = 'tickets';
    protected $basename = 'tickets';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|TicketModel|';
    protected $autoShare = 1;
    protected $list = array();

    public function getTable() {
        return $this->tblname;
    }

    public function setTable($id = 0) {
        // if (!$id)
        //     $this->tblname = $this->basename . date('Ym');
        // else
        //     $this->tblname = $this->basename . Util_Common::uniqid2date($id);
        return $this;
    }

    public function share($ts = 0) {
        // if (!$ts)
        //     $ts = time();
        // $this->tblname = $this->basename . date('Ym', $ts);
        return $this;
    }

    //生成票号，对单个票的订单
    public function addNew($productInfo,$orderItems) {
        $this->setTable($productInfo['order_id']);
        $tickets = $ticket_ids = array();
        $idx = 0;
        foreach($orderItems as $orderItem){
            foreach($productInfo['items'] as $prodItem){
                $view_point = explode(',', $prodItem['view_point']);
                $poi_num = count($view_point);
                for($i=0;$i<$prodItem['num'];$i++) {
                    $idx++;
                    $id = '2'.substr("{$orderItem['order_id']}", 1)."$idx";
                    $ticket = array();
                    $ticket['id'] = $id;
                    $ticket['status'] = 0; //状态：0：不可使用 1：可使用 2已使用，支付后状态为1
                    $ticket['order_id'] = $orderItem['order_id'];
                    $ticket['ticket_template_id'] = $prodItem['base_id'];
                    $ticket['landscape_id'] = $prodItem['scenic_id'];
                    $ticket['poi_list'] = $prodItem['view_point'];
                    $ticket['poi_num'] = $poi_num;
                    $ticket['created_at'] = $orderItem['created_at'];
                    $ticket['updated_at'] = $orderItem['updated_at'];
                    $ticket['distributor_id'] = $orderItem['distributor_id'];
                    $ticket['supplier_id'] = $orderItem['supplier_id'];
                    $ticket['product_id'] = $productInfo['id'];
                    $ticket['order_item_id'] = $orderItem['id'];
                    $ticket_ids[] = $ticket['id'];
                    $tickets[] = $ticket;
                }
            }
        }
        array_unshift($tickets, array_keys(reset($tickets)));
        $r = $this->add($tickets);
        if($r){
            //添加票明细
            array_shift($tickets);
            $r = TicketItemModel::model()->addBatch($tickets);
            if($r) return $tickets;
        }
        return false;
    }

    public function addBatch($productInfos,$orderItems) {
        $orderItemInfo = reset($orderItems);
        $this->setTable($orderItemInfo['order_id']);
        $tickets = $ticket_ids = array();
        $idx=0;
        foreach($orderItems as $orderItem){
            $productInfo = $productInfos[$orderItem['order_id']];
            foreach($productInfo['items'] as $prodItem){
                $view_point = explode(',', $prodItem['view_point']);
                $poi_num = count($view_point);
                for($i=0;$i<$prodItem['num'];$i++) {
                    $idx++;
                    $id = '2'.substr("{$orderItem['order_id']}", 1)."$idx";
                    $ticket = array();
                    $ticket['id'] = $id;
                    $ticket['status'] = 0; //状态：0：不可使用 1：可使用 2已使用，支付后状态为1
                    $ticket['order_id'] = $orderItem['order_id'];
                    $ticket['ticket_template_id'] = $prodItem['base_id'];
                    $ticket['landscape_id'] = $prodItem['scenic_id'];
                    $ticket['poi_list'] = $prodItem['view_point'];
                    $ticket['poi_num'] = $poi_num;
                    $ticket['created_at'] = $orderItem['created_at'];
                    $ticket['updated_at'] = $orderItem['updated_at'];
                    $ticket['distributor_id'] = $orderItem['distributor_id'];
                    $ticket['supplier_id'] = $orderItem['supplier_id'];
                    $ticket['product_id'] = $productInfo['id'];
                    $ticket['order_item_id'] = $orderItem['id'];
                    $ticket_ids[] = $ticket['id'];
                    $tickets[] = $ticket;
                }
            }
        }
        array_unshift($tickets, array_keys(reset($tickets)));
        $r = $this->add($tickets);
        if($r){
            //添加票明细
            array_shift($tickets);
            $r = TicketItemModel::model()->addBatch($tickets);
            if($r) return $tickets;
        }
        return false;
    }

    public function getValidList($orderId) {
        $ts = strtotime(Util_Common::uniqid2date($orderId, 'Y-m-d'));
        return $this->share($ts)->search(array('order_id' => $orderId, 'status' => 1));
    }

}
