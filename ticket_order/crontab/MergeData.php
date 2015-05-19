<?php
/**
 * 迁移数据
 * 仅用于分销1.4.2升级到分销1.5
 */
require dirname(__FILE__) . '/Base.php';
class Crontab_MergeData extends Process_Base
{
      protected $dbname = 'itourism';
      protected $db;
      protected $table;
      protected $tables;
      protected $fields;
      protected $sharding_tables;
      protected $rows;

      public function run() {
            $this->db = Db_Mysql::factory($this->dbname);
            $this->tables = array('bills_items','order_items','orders','payment_orders','payments','tickets');
            foreach($this->tables as $table) {
                  //处理分表
                  $this->sharding_tables = $this->get_sharding_tables($table);
                  if (!$this->sharding_tables) {
                        echo "\nsharding_tables of $table error..."; 
                        continue;
                  }
                  echo "\ndeal_sharding..{$table}..";
                  $this->deal_sharding($table);
            }
      }

      protected function deal_sharding($table) {
            //分表列表
            $this->table = $table;
            $this->db->begin();
            try {
                  foreach($this->sharding_tables as $key=>$value) {
                        $this->deal_data($key);
                  }
                  echo "\ncommit..";
                  $this->db->commit();
            } catch (Exception $e) {
                  $this->db->rollback();
                  $msg = $e->getMessage();
                  echo "\nerror..{$table}..{$msg}..";
            }
      }

      protected function deal_data($table) {
            echo "\ndeal_data..{$table}..";
            $start = 0;
            $limit = 100;
            while(true) {
                  $this->rows = $this->get_rows($table, $start, $limit);
                  if (!$this->rows) {
                        echo "\nend..";
                        break;
                  }
                  if ($this->table=='order_items') {
                        $this->deal_order_items();
                  } elseif ($this->table=='orders') {
                        $this->deal_orders();
                  } elseif ($this->table=='tickets') {
                        $this->deal_tickets();
                  }
                  $len = count($this->rows);
                  echo "\ndeal {$start}-{$len}..";
                  $start += $limit;
                  $this->fields = array_keys(reset($this->rows));
                  array_unshift($this->rows, $this->fields);
                  $this->db->replace($this->table, $this->rows);
            }
      }

      protected function deal_order_items() {
            // $i = 0;
            foreach($this->rows as $key=>$value) {
                  if (isset($value['ticket_template_id'])) {
                        $value['product_id'] = $value['ticket_template_id'];
                        unset($value['ticket_template_id']);
                  }
                  // $i++;
                  // $id = '3'.substr("{$value['order_id']}", 1)."1";
                  $value['use_time'] = 0;
                  $value['status'] = 0;
                  $value['bill_time'] = 0;
                  unset($value['base_num_total']);
                  // $value['base_num_total'] = 0;
                  // $value['id'] = $id;
                  $this->rows[$key] = $value;
            }
      }

      protected function deal_orders() {
            foreach($this->rows as $key=>$value) {
                  $value['activity_paid'] = 0;
                  $value['product_id'] = 0;
                  $value['name'] = '';
                  $value['price'] = 0;
                  $value['fat_price'] = 0;
                  $value['group_price'] = 0;
                  $value['sale_price'] = 0;
                  $value['listed_price'] = 0;
                  $value['valid'] = 0;
                  $value['max_buy'] = 0;
                  $value['mini_buy'] = 0;
                  $value['week_time'] = '';
                  $value['refund'] = 0;
                  $value['expire_start'] = 0;
                  $value['expire_end'] = 0;
                  $value['ticket_infos'] = '';
                  $value['valid_flag'] = 0;
                  $value['product_payment'] = '';
                  $value['source'] = 0;
                  $value['local_source'] = 0;
                  $value['source_id'] = '';
                  $value['source_token'] = '';
                  $this->rows[$key] = $value;
            }
      }

      protected function deal_tickets() {
            // $i = 0;
            foreach($this->rows as $key=>$value) {
                  // $i++;
                  // $id = '2'.substr("{$value['order_id']}", 1)."$i";
                  $value['distributor_id'] = 0;
                  $value['supplier_id'] = 0;
                  $value['product_id'] = 0;
                  $value['use_time'] = 0;
                  $value['order_item_id'] = 0;
                  // $value['id'] = $id;
                  $this->rows[$key] = $value;
            }
      }

      protected function get_rows($table, $start, $limit) {
            $sql = "select * from `".$table."` order by created_at asc limit {$start},{$limit}";
            return $this->db->selectBySql($sql);
      }

      protected function get_fields($table) {
            return $this->db->setlistKey('Field')->selectBySql("desc {$table}");
      }

      protected function get_sharding_tables($table) {
            return $this->db->setlistKey("Tables_in_ticket_order ({$table}201%)")->selectBySql("show tables like '{$table}201%'");
      }
}

$test = new Crontab_MergeData;
