<?php

/**
 * 票号详情类
 * 每张票（码）的景区景点关系
 * 
 * @author mosen
 */
class TicketItemsModel extends Base_Model_Abstract {

    protected $dbname = 'itourism';
    protected $tblname = 'ticket_items';
    protected $basename = 'ticket_items';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|TicketItemsModel|';
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

    public function getTicketList($params) {
        //获取验票点的小码
        $ticket_codes = $order_codes = array();
        if(!is_array($params['order_id'])) $params['order_id'] = explode(',', $params['order_id']);
        $where = array('order_id|in'=>$params['order_id']);
        $params['landscape_id'] && $where['landscape_id'] = $params['landscape_id'];
        $params['poi_id'] && $where['poi_id'] = $params['poi_id'];
        $params['order_item_id'] && $where['order_item_id'] = $params['order_item_id'];
        $params['ticket_id'] && $where['ticket_id'] = $params['ticket_id'];
        $codes = $this->setTable(reset($params['order_id']))->select($where);
        if ($codes) {
            foreach($codes as $value) {
                $status = $value['status'];
                $tid = $value['ticket_id'];
                $oid = $value['order_id'];
                $ticket_codes[$status][$tid] = $value;
                $order_codes[$oid][$status][$tid] = $tid;
            }
        }
        return array($ticket_codes, $order_codes);
    }

}
