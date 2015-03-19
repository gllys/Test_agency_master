<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-23
 * Time: 下午5:39
 */

class OrderItemModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'order_items';
    protected $basename = 'order_items';
    protected $pkKey = 'id';
    //protected $preCacheKey = 'cache|OrderItemModel|';
    protected $autoShare = 1;
    protected $ticketTemplateFields = array(
        'ticket_template_base_id','name','fat_price','group_price','sale_price','listed_price',
        'valid','max_buy','mini_buy','payment','view_point','week_time','refund','remark','date_available',
        'price_type','price','expire_start','expire_end','user_id','user_account','user_name'
    );

    public function getTable() {
        return $this->tblname;
    }

    public function setTable($id = 0) {
        if (!$id) $this->tblname = $this->basename . date('Ym');
        else  $this->tblname = $this->basename . Util_Common::uniqid2date($id);
        return $this;
    }

    public function share($ts = 0) {
        if (!$ts) $ts = time();
        $this->tblname = $this->basename . date('Ym', $ts);
        return $this;
    }

    //票明细添加，用于单个票生产单个订单操作
    public function addNew($orderInfo,$itemInfo){
        $data = array();
        $data['id'] = Util_Common::uniqid(3); //参数3，订单详情
        $data['order_id'] = $orderInfo['id'];
        $data['ticket_type'] = $itemInfo['type'];
        $data['kind'] = $itemInfo['is_union']==0?1:2; //种类:1单票2联票3套票
        $data['ticket_template_id'] = $itemInfo['id'];
        $data['use_day'] = $orderInfo['use_day'];
        $data['nums'] = $orderInfo['nums'];
        $data['distributor_id'] = $orderInfo['distributor_id'];
        $data['supplier_id'] = $orderInfo['supplier_id'];
        $data['landscape_ids'] = $itemInfo['scenic_id'];
        $data['created_at'] = $orderInfo['created_at'];
        $data['updated_at'] = $orderInfo['updated_at'];

        foreach ($itemInfo as $key=>$v) {
            in_array($key,$this->ticketTemplateFields) && $data[$key] = $v;
        }
        return $this->add($data) ? $data :false;
    }

    public function addBatch($orders,$ticketTemplateInfos){
        $items = $fields = array();
        foreach($ticketTemplateInfos as $ticket){
            $data = array();
            $data['id'] = Util_Common::uniqid(3); //参数3，订单详情
            $data['order_id'] = $ticket['order_id'];
            $data['ticket_type'] = $ticket['type'];
            $data['kind'] = $ticket['is_union']==0?1:2; //种类:1单票2联票3套票
            $data['ticket_template_id'] = $ticket['id'];
            $data['use_day'] = $orders[$ticket['order_id']]['use_day'];
            $data['nums'] = $orders[$ticket['order_id']]['nums'];
            $data['distributor_id'] = $orders[$ticket['order_id']]['distributor_id'];
            $data['supplier_id'] = $orders[$ticket['order_id']]['supplier_id'];
            $data['landscape_ids'] = $ticket['scenic_id'];
            $data['created_at'] = $orders[$ticket['order_id']]['created_at'];
            $data['updated_at'] = $orders[$ticket['order_id']]['updated_at'];
            foreach ($ticket as $key=>$v) {
                in_array($key,$this->ticketTemplateFields) && $data[$key] = $v;
            }
            if(!$fields)
                $fields = array_keys($data);
            $items[] = $data;
        }
        array_unshift($items,$fields);
        $r = $this->add($items);
        if(!$r)
            return false;
        else{
            array_shift($items);
            return $items;
        }
    }


}

