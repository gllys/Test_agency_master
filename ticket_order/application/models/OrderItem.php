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
    protected $productFields = array(
        'name','fat_price','group_price','sale_price','listed_price',
        'valid','max_buy','mini_buy','view_point','week_time','refund','remark','date_available',
        'price_type','price','user_id','user_account','user_name'
    );
    /** 缓存前缀 */
    const CACHE_PREFIX = 'OrderItem|OrderId_';
    /** 缓存最大有效期 */
    const CACHE_EXPIRE_MAX_TIME = 604800; //3600*24*7

    public function getTable() {
        return $this->tblname;
    }

    public function setTable($id = 0) {
        // if (!$id) $this->tblname = $this->basename . date('Ym');
        // else  $this->tblname = $this->basename . Util_Common::uniqid2date($id);
        return $this;
    }

    public function share($ts = 0) {
        // if (!$ts) $ts = time();
        // $this->tblname = $this->basename . date('Ym', $ts);
        return $this;
    }

    //票明细添加，按产品票数生成记录
    public function addNew($order,$productInfo,$visitors=array()){
        $data = array();
        for ($i = 0; $i < $order['nums']; $i++) {
            $idx = $i+1;
            $id = '3'.substr("{$productInfo['order_id']}", 1)."$idx";
            $item = array();
            $item['id'] = $id; //参数3，订单详情
            $item['order_id'] = $productInfo['order_id'];
            $item['ticket_type'] = $productInfo['type']; //票类型:0电子票1任务单
            $item['kind'] = $productInfo['is_union'] == 0 ? 1 : 2; //种类:1单票2联票3套票
            $item['product_id'] = $productInfo['id'];
            $item['use_day'] = $order['use_day'];
            $item['nums'] = 0;
            $item['distributor_id'] = $order['distributor_id'];
            $item['supplier_id'] = $order['supplier_id'];
            $item['landscape_ids'] = $order['landscape_ids'];
            $item['payment'] = $order['payment'];
            $item['created_at'] = $order['created_at'];
            $item['updated_at'] = $order['updated_at'];
            $item['expire_start'] = $order['expire_start'];
            $item['expire_end'] = $order['expire_end'];
            if($visitors && $visitors[$i]){
                $item['visitor_name'] = $visitors[$i]['visitor_name'];
                $item['visitor_mobile'] = $visitors[$i]['visitor_mobile'];
                $item['visitor_card'] = $visitors[$i]['visitor_card'];
            }
            foreach ($productInfo['items'] as $piv) {
                $item['nums'] += $piv['num'];
            }
            foreach ($productInfo as $key=>$v) {
                in_array($key,$this->productFields) && $item[$key] = $v;
            }
            $data[] = $item;
        }
        array_unshift($data,array_keys(reset($data)));
        $r = $this->add($data);
        if($r){
            //添加票
            array_shift($data);
            $r = TicketQueueModel::model()->saveTmpCache($order['id'], $productInfo, $data);
            //$r = TicketModel::model()->addNew($productInfo,$data);
            if($r)
                return $data;
        }
        return false;
    }

    public function addBatch($orders,$productInfos,$visitorsArr=array()){
        $items = array();
        foreach($productInfos as $order_id=>$product){
            for ($i = 0; $i < $orders[$order_id]['nums']; $i++) {
                $idx = $i+1;
                $id = '3'.substr("{$order_id}", 1)."$idx";
                $data = array();
                $data['id'] = $id; //参数3，订单详情
                $data['order_id'] = $order_id;
                $data['ticket_type'] = $product['type'];
                $data['kind'] = $product['is_union'] == 0 ? 1 : 2; //种类:1单票2联票3套票
                $data['product_id'] = $product['id'];
                $data['use_day'] = $orders[$order_id]['use_day'];
                $data['nums'] = 0;
                $data['distributor_id'] = $orders[$order_id]['distributor_id'];
                $data['supplier_id'] = $orders[$order_id]['supplier_id'];
                $data['landscape_ids'] = $orders[$order_id]['landscape_ids'];
                $data['created_at'] = $orders[$order_id]['created_at'];
                $data['updated_at'] = $orders[$order_id]['updated_at'];
                $data['expire_start'] = $orders[$order_id]['expire_start'];
                $data['expire_end'] = $orders[$order_id]['expire_end'];
                if(!empty($visitorsArr[$order_id]) && !empty($visitorsArr[$order_id][$i])) {
                    $data['visitor_name'] = $visitorsArr[$order_id][$i]['visitor_name'];
                    $data['visitor_mobile'] = $visitorsArr[$order_id][$i]['visitor_mobile'];
                    $data['visitor_card'] = $visitorsArr[$order_id][$i]['visitor_card'];
                }
                foreach ($product['items'] as $piv) {
                    $data['nums'] += $piv['num'];
                }

                foreach ($product as $key => $v) {
                    in_array($key, $this->productFields) && $data[$key] = $v;
                }
                $items[] = $data;
            }
        }
        array_unshift($items,array_keys(reset($items)));
        $r = $this->add($items);
        if($r){
            //添加票
            array_shift($items);
            $r = TicketQueueModel::model()->saveTmpBatchCache($productInfos, $items);
            //$r = TicketModel::model()->addBatch($productInfos,$items);
            if($r) return $items;
        }
        return false;
    }

    public function setCacheByOrderId($id, $items)
    {
        if(is_array($items))
        {
            $cacheKey = self::CACHE_PREFIX . $id;
            $this->redis->push('hmset', array($cacheKey, $items));
            $this->redis->push('expire', array($cacheKey, self::CACHE_EXPIRE_MAX_TIME));
        }
        return true;
    }

    public function deleteRedisCache($orderID)
    {
        return $this->redis->push('del',array(self::CACHE_PREFIX . $orderID));
    }

    public function getCacheByOrderId($id)
    {
        $cacheKey = self::CACHE_PREFIX .$id;
        $items = $this->redis->hGetAll($cacheKey);
        if(!empty($items) && is_array($items)) {
            return $items;
        }

        if(empty($items)) {
            $orderItems = $this->search(array('order_id' => $id));
            if(empty($orderItems) || !is_array($orderItems)) {
                Lang_Msg::error('订单异常请联系管理员');
            }
            $items = array();
            foreach($orderItems as $value) {
                $items[$value['id']] = $value['status'];
            }
            $this->redis->hmset($cacheKey, $items);
            $this->redis->expire($cacheKey, self::CACHE_EXPIRE_MAX_TIME);
        }
        return $items;
    }

    /* 检查旅客信息是否有效，一维数组
     * @param $visitor array('visitor_name'=>'','visitor_mobile'=>'','visitor_card'=>'')
     * @param $need_idcard 身份证是否必填
     * zqf 2015-06-17
     * v1.13
     **/
    public function checkVisitorVal($visitor,$need_idcard=0,$popMsg=true) {
        if(!preg_match("/\S+/",$visitor['visitor_name'])) {
            if($popMsg) {
                Tools::lsJson(false,'旅客姓名不能为空');
            } else {
                return false;
            }
        } else if(!preg_match("/\S+/",$visitor['visitor_card'])) {
            if($popMsg) {
                Tools::lsJson(false,'旅客['.$visitor['visitor_name'].']身份证号不能为空');
            } else {
                return false;
            }
        } else if(!empty($visitor['visitor_mobile']) && !Validate::isMobilePhone($visitor['visitor_mobile'])) {
            if($popMsg) {
                Tools::lsJson(false,'旅客['.$visitor['visitor_name'].']手机号“'.$visitor['visitor_mobile'].'”格式错误');
            } else {
                return false;
            }
        } else if(($need_idcard || (!$need_idcard && !empty($visitor['visitor_card']))) && !Validate::isCard($visitor['visitor_card'])) {
            if($popMsg) {
                Tools::lsJson(false,'旅客['.$visitor['visitor_name'].']身份证号“'.$visitor['visitor_card'].'”无效');
            } else {
                return false;
            }
        }
        return true;
    }

    /*批量检查旅客信息是否有效，二维数组
     * @param $visitors array( 0=>array('visitor_name'=>'','visitor_mobile'=>'','visitor_card'=>''),... )
     * @param $product_name 产品名称
     * @param $need_idcard 产品是否需要旅客身份证
     * @param $num 购买张数
     * zqf 2015-06-17
     * v1.13
     * */
    public function checkVisitors($visitors,$product_name='',$need_idcard=0,$num=1,$popMsg=true) {
        if($need_idcard && empty( $visitors)) {
            if($popMsg) {
                Tools::lsJson(false,'该产品［'.$product_name.'］旅客姓名和身份证必填');
            } else {
                return false;
            }
        } else if(is_array($visitors)) {
            if($need_idcard && count($visitors)<$num) {
                if($popMsg) {
                    Tools::lsJson(false,'该产品［'.$product_name.'］旅客姓名和身份证信息数量需和预定张数相同');
                } else {
                    return false;
                }
            } else {
                foreach($visitors as $visitor) {
                    if(!$this->checkVisitorVal($visitor,$need_idcard,$popMsg)) {
                        return false;
                    }
                }
                return true;
            }
        }
        if($popMsg) {
            Tools::lsJson(false,'该产品［'.$product_name.'］旅客姓名和身份证数据有误');
        } else {
            return false;
        }
    }

}

