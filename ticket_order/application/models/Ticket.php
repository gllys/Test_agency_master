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
        if (!$id)
            $this->tblname = $this->basename . date('Ym');
        else
            $this->tblname = $this->basename . Util_Common::uniqid2date($id);
        return $this;
    }

    public function share($ts = 0) {
        if (!$ts)
            $ts = time();
        $this->tblname = $this->basename . date('Ym', $ts);
        return $this;
    }

    //生成票号，对单个票的订单
    public function addNew($orderInfo, $ticketTemplateInfo) {
        $this->setTable($orderInfo['id']);
        $tickets = $fields = $ticket_ids = array();
        $view_point = explode(',', $ticketTemplateInfo['view_point']);
        $poi_num = count($view_point);
        for ($i = 0; $i < $orderInfo['nums']; $i++) {
            $data = array();
            $data['id'] = Util_Common::uniqid(2);
            $data['status'] = 1;
            $data['order_id'] = $orderInfo['id'];
            $data['ticket_template_id'] = $ticketTemplateInfo['id'];
            $data['poi_list'] = $ticketTemplateInfo['view_point'];
            $data['poi_num'] = $poi_num;
            $data['created_at'] = $orderInfo['created_at'];
            $data['updated_at'] = $orderInfo['updated_at'];
            $ticket_ids[] = $data['id'];

            if (!$fields)
                $fields = array_keys($data);
            $tickets[] = $data;
        }
        array_unshift($tickets, $fields);
        return $this->add($tickets) ? $ticket_ids : false;
    }

    public function addBatch($orders, $ticketTemplateInfos) {
        $tickets = $fields = $ticket_ids = array();
        foreach ($ticketTemplateInfos as $ticket) {
            $this->setTable($ticket['order_id']);
            $view_point = explode(',', $ticket['view_point']);
            $poi_num = count($view_point);
            for ($i = 0; $i < $orders[$ticket['order_id']]['nums']; $i++) {
                $data = array();
                $data['id'] = Util_Common::uniqid(2);
                $data['status'] = 1;
                $data['order_id'] = $orders[$ticket['order_id']]['id'];
                $data['ticket_template_id'] = $ticket['id'];
                $data['poi_list'] = $ticket['view_point'];
                $data['poi_num'] = $poi_num;
                $data['created_at'] = $orders[$ticket['order_id']]['created_at'];
                $data['updated_at'] = $orders[$ticket['order_id']]['updated_at'];
                $ticket_ids[] = $data['id'];

                if (!$fields)
                    $fields = array_keys($data);
                $tickets[] = $data;
            }
        }

        array_unshift($tickets, $fields);
        return $this->add($tickets) ? $ticket_ids : false;
    }

    public function getValidList($orderId) {
        $ts = strtotime(Util_Common::uniqid2date($orderId, 'Y-m-d'));
        return $this->share($ts)->search(array('order_id' => $orderId, 'status' => 1));
    }

    public function getUnusedList($orderId, $poiId = 0, $ticket_id = 0) {
        $ts = strtotime(Util_Common::uniqid2date($orderId, 'Y-m-d'));
        if ($poiId > 0) {
            $where = array(
                'order_id' => $orderId,
                'status' => 1,
                'find_in_set|exp' => '(' . $poiId . ',poi_list)',
                'or' => array(
                    'poi_used|exp' => 'is null',
                    '!find_in_set|exp' => '(' . $poiId . ',poi_used)'
                )
            );
        } else {
            $where = array(
                'order_id' => $orderId,
                'status' => 1,
                'poi_used_num|exp' => '<poi_num'
            );
        }
        if ($ticket_id > 0) {
            $where['id'] = $ticket_id;
        }
        return $this->share($ts)->search($where, 'id');
    }

    public function getUnusedNum($orderId, $poiId, $ticket_id = 0) {
        $ts = strtotime(Util_Common::uniqid2date($orderId, 'Y-m-d'));
        if ($poiId > 0) {
            $where = array(
                'order_id' => $orderId,
                'status' => 1,
                'find_in_set|exp' => '(' . $poiId . ',poi_list)',
                'or' => array(
                    'poi_used|exp' => 'is null',
                    '!find_in_set|exp' => '(' . $poiId . ',poi_used)'
                )
            );
        } else {
            $where = array(
                'order_id' => $orderId,
                'status' => 1,
                'poi_used_num|exp' => '<poi_num'
            );
        }
        if ($ticket_id > 0) {
            $where['id'] = $ticket_id;
        }
        return @reset(reset($this->share($ts)->search($where, 'count(id)')));
    }

    public function useTicket($order_id, $poi_id = 0, $nums = 1, $ticket_id = 0) {
        $order = OrderModel::model()->getById($order_id);
        // 获取当前景点未使用的票
        $unusedTickets = $this->getUnusedList($order_id, $poi_id, $ticket_id);
        if (!$unusedTickets)
            return false;
        // 获取所有票
        $validTickets = $this->getValidList($order_id);
        // 顺序使用指定数量的票
        $ids = array_slice(array_keys($unusedTickets), 0, $nums);
        // 更新票
        $now = time();
        $used_nums = 0;
        foreach ($validTickets as $ticket) {
            if (!in_array($ticket['id'], $ids)) {
                if (in_array($poi_id, explode(',', $ticket['poi_used'])))
                    $used_nums ++;
                continue;
            }
            $used_nums ++;
            if ($poi_id > 0) {
                $poi_used = $ticket['poi_used'] ? $ticket['poi_used'] . ',' . $poi_id : $poi_id;
                $poi_used_num = $ticket['poi_used_num'] + 1;
            } else {
                $poi_used = $ticket['poi_list'];
                $poi_used_num = $ticket['poi_num'];
            }

            TicketModel::model()->updateById($ticket['id'], array('poi_used_num' => $poi_used_num, 'poi_used' => $poi_used));
        }
        if ($order['used_nums'] < $used_nums) {
            // 更新ORDER
            $upOrderData = array('used_nums' => $used_nums, 'updated_at' => $now);
            $used_nums==$order['nums']-$order['refunded_nums'] && $upOrderData['status'] = 'finish';
            OrderModel::model()->updateById($order_id, $upOrderData);
            // 更新ORDERITEM
            OrderItemModel::model()->setTable($order_id)->updateByAttr(array('used_nums' => $used_nums, 'updated_at' => $now), array('order_id' => $order_id));
        }
        return true;
    }

    public function getUsedList($orderId, $poiId = 0, $ticket_id = 0) {
        $ts = strtotime(Util_Common::uniqid2date($orderId, 'Y-m-d'));
        if ($poiId > 0) {
            $where = array(
                'order_id' => $orderId,
                'status' => 1,
                'find_in_set|exp' => '(' . $poiId . ',poi_list)',
                'find_in_set|exp' => '(' . $poiId . ',poi_used)'
            );
        } else {
            $where = array(
                'order_id' => $orderId,
                'status' => 1,
                'poi_used_num|exp' => '<poi_num'
            );
        }
        if ($ticket_id > 0) {
            $where['id'] = $ticket_id;
        }
        return $this->share($ts)->search($where);
    }

    public function cancelTicket($order_id, $poi_id = 0, $nums = 1, $ticket_id = 0) {
        $usedTickets = $this->getUsedList($order_id, $poi_id, $ticket_id);
        if (!$usedTickets)
            return false;
        //退掉票
        $cancelTickets = array_slice($usedTickets, 0, $nums);
        $order_used_num = 0;
        foreach ($cancelTickets as $ticket) {
            $poi_has_used = explode(',', $ticket['poi_used']);
            $poi_used = array();
            foreach ($poi_has_used as $val) {
                if ($poi_id != $val) {
                    $poi_used[] = $val;
                }
            }
            if (!$poi_used) {
                $order_used_num++;
            }
            TicketModel::model()->updateById($ticket['id'], array('poi_used_num' => $ticket['poi_used_num'] - 1, 'poi_used' => join(',', $poi_used)));
        }

        if ($order_used_num) {
            $now = time();
            $order = OrderModel::model()->getById($order_id);
            OrderModel::model()->updateById($order_id, array('used_nums' => $order['used_nums'] - $order_used_num, 'updated_at' => $now));
            OrderItemModel::model()->setTable($order_id)->updateByAttr(array('used_nums' => $order['used_nums'] - $order_used_num, 'updated_at' => $now), array('order_id' => $order_id));
        }
        
        return false;
    }

}
