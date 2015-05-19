<?php
/**
 * 合并数据
 * 仅用于分销1.4.2升级到分销1.5
 */
require dirname(__FILE__) . '/Base.php';
class Crontab_TransData extends Process_Base
{
      protected $dbname = 'itourism';
      protected $db;
      protected $orders;
      protected $product = array();
      protected $products = array();
      protected $order = array();
      protected $order_item = array();
      protected $order_items = array();
      protected $tickets = array();
      protected $ticket_items = array();

      public function run() {
            $this->db = Db_Mysql::factory($this->dbname);
            $this->start = 0;
            $limit = 100;
            while (true) {
                $this->orders = $this->get_orders($this->start,$limit);
                if (!$this->orders) {
                  echo "\nfinish...";
                  break;
                }
                $count = count($this->orders);
                echo "\ndeal {$this->start}-{$count}...";
                $this->deal_orders();
            }
      }

      protected function deal_orders() {
            //获取需处理订单
            foreach($this->orders as $value) {
                  if($value['nums']>=1000){
                    $this->start++;
                    echo "\n order {$value['id']} nums is too large";
                    continue;
                  }
                  $this->order = $value;
                  $this->deal_order();
            }
      }

      protected function deal_order() {
            $order_id = $this->order['id'];
            echo "\ndeal_order...{$order_id}...";
            $this->db->begin();
            try{
                  $this->get_order_item($order_id);
                  if(!$this->product['items']) {
                    throw new Exception("product items not exits");
                  }
                  //更新ORDER
                  $this->order['product_id'] = $this->order_item['product_id'];
                  $this->order['name'] = $this->order_item['name'];
                  $this->order['price'] = $this->order_item['price'];
                  $this->order['fat_price'] = $this->order_item['fat_price'];
                  $this->order['group_price'] = $this->order_item['group_price'];
                  $this->order['sale_price'] = $this->order_item['sale_price'];
                  $this->order['listed_price'] = $this->order_item['listed_price'];
                  $this->order['valid'] = $this->order_item['valid'];
                  $this->order['max_buy'] = $this->order_item['max_buy'];
                  $this->order['mini_buy'] = $this->order_item['mini_buy'];
                  $this->order['week_time'] = $this->order_item['week_time'];
                  $this->order['refund'] = $this->order_item['refund'];
                  $this->order['expire_start'] = $this->order_item['expire_start'];
                  $this->order['expire_end'] = $this->order_item['expire_end'];
                  $this->order['ticket_infos'] = $this->product['items'];
                  $this->order['product_payment'] = $this->order_item['payment'];
                  //更新ORDER_ITEMS
                  $this->deal_order_item();
                  //更新TICKETS
                  $this->deal_tickets($order_id);
                  //核销产品
                  $this->update_order_item();
                  $this->db->update('orders', $this->order, array('id'=>$order_id));
                  $this->db->commit();
                  echo "\ndeal...ok...";
            }catch(Exception $e) {
                  $this->start++;
                  $this->db->rollback();
                  $msg = $e->getMessage();
                  echo "\ndeal...false...{$msg}...";
            }
      }

      protected function deal_order_item() {
          echo "\ndeal_order_item...";
          $this->order_items = array();
          for($i=1;$i<=$this->order['nums'];$i++) {
              $id = '3'.substr("{$this->order['id']}", 1)."$i";
              $this->order_items[$id] = $this->order_item;
              $this->order_items[$id]['id'] = $id;
          }
      }

      protected function update_order_item() {
          $tmp = array();
          foreach($this->tickets as $value) {
              if($value['order_item_id']) $tmp[$value['order_item_id']][$value['status']][$value['id']] = $value['use_time'];
          }
          foreach($tmp as $key=>$value) {
              $use_time = isset($value[2]) ? min($value[2]) : 0;
              $status = isset($value[2])?2 : (isset($value[0]) ? 0 : 1);
              $this->order_items[$key]['status'] = $status;
              $this->order_items[$key]['use_time'] = $use_time;
          }
          array_unshift($this->order_items, array_keys(reset($this->order_items)));
          $this->db->replace('order_items', $this->order_items);
          $this->db->delete('order_items', array('id'=>$this->order_item['id']));
          echo "\nreplace order_items..ok";
      }

      protected function deal_tickets($order_id) {
          echo "\ndeal_tickets...";
          $this->tickets = $this->get_tickets($order_id);
          if (!$this->tickets) {
              throw new Exception("tickets (order_id:{$order_id}) not exists");
          }
          //景点-->门票
          $items = array();
          foreach($this->product['items'] as $val) {
              $items[$val['scenic_id']] = $val;
          }
          //产品ID列表
          $order_item_ids = array_keys($this->order_items);
          //更新票
          $list = array();
          $this->ticket_items = array();
          $this->i = 0;
          $i = 0;
          foreach($this->tickets as $value) {
              $order_item_id = array_shift($order_item_ids);
              foreach ($items as $scenic_id => $val) {
                  $i++;
                  $id = '2'.substr("{$this->order['id']}", 1)."$i";
                  $view_point = explode(',', $val['view_point']);
                  $poi_used = explode(',', $value['poi_used']);
                  $used = array_intersect($view_point,$poi_used);
                  $status = $used ? 2 : ($value['status']==0 ? 0:1);
                  $use_time = $status==2? $value['updated_at'] : 0;
                  $value['id'] = $id;
                  $value['distributor_id'] = $this->order['distributor_id'];
                  $value['supplier_id'] = $this->order['supplier_id'];
                  $value['product_id'] = $this->order['product_id'];
                  $value['use_time'] = $use_time;
                  $value['status'] = $status;
                  $value['order_item_id'] = $order_item_id;
                  $value['landscape_id'] = $scenic_id;
                  $value['poi_list'] = $val['view_point'];
                  $value['poi_num'] = count($view_point);
                  $value['ticket_template_id'] = $val['base_id'];
                  //生成票验票点
                  $this->deal_ticket_items($value,$view_point,$poi_used);
                  $list[$id] = $value;
              }
          }

          //清理老数据
          $this->db->delete('tickets', array('id|in'=>array_keys($this->tickets)));
          $this->tickets = $list;
          //更新票
          array_unshift($list, array_keys(reset($list)));
          $this->db->replace('tickets', $list);
          echo "\nreplace tickets..ok";
          //更新验票点
          array_unshift($this->ticket_items, array_keys(reset($this->ticket_items)));
          $this->db->replace('ticket_items', $this->ticket_items);
          echo "\nreplace ticket_items..ok";
      }

      protected function deal_ticket_items($ticket, $pois, $poi_used) {
          // echo "\ndeal_ticket_items...";
          foreach($pois as $poi) {
              $this->i++;
              $id = '5'.substr("{$this->order['id']}", 1)."{$this->i}";
              $tmp = array();
              $status = $ticket['status']==0 ? 0 : (in_array($poi, $poi_used) ? 2 : 1);
              $tmp['id'] = $id;
              $tmp['ticket_id'] = $ticket['id'];
              $tmp['order_id'] = $ticket['order_id'];
              $tmp['order_item_id'] = $ticket['order_item_id'];
              $tmp['landscape_id'] = $ticket['landscape_id'];
              $tmp['poi_id'] = $poi;
              $tmp['status'] = $status;
              $tmp['created_at'] = $ticket['created_at'];
              $tmp['updated_at'] = $status==2 ? $ticket['use_time'] : $ticket['updated_at'];
              $this->ticket_items[$id] = $tmp;
          }
      }

      protected function get_orders($start, $limit = 100) {
            $sql = "select * from `orders` where product_id=0 order by `created_at` asc limit {$start},{$limit}";
            return $this->db->setListKey('id')->selectBySql($sql);
      }

      protected function get_order_item($order_id) {
            $this->order_item = $this->db->get('order_items', array('order_id'=>$order_id));
            if(!$this->order_item) {
                throw new Exception("order_item (order_id:{$order_id}) not exists");
            }
            $this->product = $this->get_product($this->order_item['product_id']);
      }

      protected function get_tickets($order_id) {
            $sql = 'select * from tickets where order_id='.$order_id;
            return $this->db->setListKey('id')->selectBySql($sql);
      }

      protected function get_product($product_id) {
            if(!isset($this->products[$product_id])) {
                  $this->products[$product_id] = TicketTemplateModel::model()->getTicketInfo($product_id,2);
            }
            if(!$this->products[$product_id]) {
                  throw new Exception("product {$product_id} not exists");
            }
            return $this->products[$product_id];
      }
}

$test = new Crontab_TransData;

