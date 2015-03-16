<?php
/**
 * 合并数据
 * 仅用于分销1.4.2升级到分销1.5
 */
require dirname(__FILE__) . '/Base.php';
class Crontab_MergeData extends Process_Base
{
      protected $dbname = 'itourism';
      protected $db;
      protected $ticket_templates;
      protected $scenics = array();
      protected $pois = array();
      protected $bases = array();

      public function run() {
            $this->db = Db_Mysql::factory($this->dbname);
            $this->deal_ticket_templates();
      }

      protected function deal_ticket_templates() {
            //获取需处理门票
            $this->ticket_templates = $this->get_ticket_templates();
            $total = count($this->ticket_templates);
            echo "\ntotal...{$total}...";
            //按景区生成基础票
            foreach($this->ticket_templates as $value) {
                  $this->deal_ticket_template($value);
            }
      }

      protected function deal_ticket_template($value) {
            echo "\ndeal_ticket_template...{$value['id']}...";
            $this->db->begin();
            try{
                  $scenic_id = explode(',', $value['scenic_id']);
                  $view_point = explode(',', $value['view_point']);
                  //加载景点
                  $this->get_pois($scenic_id);
                  $tmp = array();
                  if(!$value['listed_price']) {
                        $tmp['listed_price'] = $value['listed_price'] = max($value['fat_price'],$value['group_price'],$value['sale_price']);
                  }
                  if(!$value['sale_price']) {
                        $tmp['sale_price'] = $value['sale_price'] = $value['listed_price'];
                  }
                  if($tmp){
                        TicketTemplateModel::model()->updateById($value['id'],$tmp);
                  }
                  //每个景点生成基础票
                  $this->make_ticket_template_base($value, $scenic_id, $view_point);
                  $this->db->commit();
                  echo "\ndeal...ok...";
            }catch(Exception $e) {
                  $this->db->rollback();
                  $msg = $e->getMessage();
                  echo "\ndeal...false...{$msg}...";
            }
      }

      protected function make_ticket_template_base($ticket_template, $scenic_id, $view_point) {
            echo "\nmake_ticket_template_base...start..";
            $now = time();
            $id = $ticket_template['id'];
            $name = $ticket_template['name'];
            unset($ticket_template['id'], $ticket_template['sale_start_time'], $ticket_template['sale_end_time'], $ticket_template['policy_id'], $ticket_template['ota_code'], $ticket_template['base_org_num']); 
            foreach($scenic_id as $sid) {
                  if (!isset($this->scenics[$sid])){
                        // throw new Exception("scenics {$sid} not exists");
                        echo "scenics {$sid} not exists";
                        continue;
                  }
                  $scenic = $this->scenics[$sid];
                  $poi = $poi_names = array();
                  foreach($scenic['pois'] as $key=>$value) {
                        if(in_array($key, $view_point)) {
                              $poi[$key] = $key;
                              $poi_names[$key] = $value['poi_name'];
                        }
                  }
                  sort($poi);
                  ksort($poi_names);
                  $poi = implode(',', $poi);
                  $name = implode('_',$poi_names);
                  $sale_price = $ticket_template['sale_price']>0 ? $ticket_template['sale_price'] : ($ticket_template['fat_price']>0 ? $ticket_template['fat_price'] : $ticket_template['group_price']);
                  //生成基础票
                  $name = $scenic['name'] == $name ? $name : $scenic['name'].'-'.$name;
                  // $ticket_template['name'] = $scenic['name'].'-'.$name;
                  $ticket_template['name'] = $name;
                  $ticket_template['organization_id'] = $scenic['organization_id'];
                  $ticket_template['scenic_id'] = $sid;
                  $ticket_template['view_point'] = $poi;
                  $ticket_template['province_id'] = $scenic['province_id'];
                  $ticket_template['city_id'] = $scenic['city_id'];
                  $ticket_template['district_id'] = $scenic['district_id'];
                  $ticket_template['sale_price'] = $sale_price;
                  $ticket_template['rule_id'] = 0;
                  $ticket_template['type'] = 1;
                  $ticket_template['namelist_id'] = 0;
                  $ticket_template['discount_id'] = 0;
                  $ticket_template['is_union'] = 0;
                  $ticket_template['gid'] = Util_Common::payid();
                  $nid = $this->get_base_id($sid, $poi, $ticket_template);
                  
                  //生成产品&门票关系
                  $ticket_template_items = array();
                  $ticket_template_items['product_id'] = $id;
                  $ticket_template_items['base_id'] = $nid;
                  $ticket_template_items['base_org_id'] = $scenic['organization_id'];
                  $ticket_template_items['scenic_id'] = $scenic['id'];
                  $ticket_template_items['sceinc_name'] = $scenic['name'];
                  $ticket_template_items['view_point'] = $poi;
                  $ticket_template_items['base_name'] = $scenic['name'].'-'.$ticket_template['name'];
                  $ticket_template_items['type'] = 1;
                  $ticket_template_items['sale_price'] = $sale_price;
                  $ticket_template_items['num'] = 1;
                  $ticket_template_items['province_id'] = $scenic['province_id'];
                  $ticket_template_items['city_id'] = $scenic['city_id'];
                  $ticket_template_items['district_id'] = $scenic['district_id'];
                  $ticket_template_items['created_at'] = $now;
                  $ticket_template_items['updated_at'] = $now;
                  TicketTemplateItemModel::model()->replace($ticket_template_items);
                  echo "\nadd TicketTemplateItem...";
            }

      }

      protected function get_base_id($sid, $poi, $ticket_template) {
            if (!isset($this->bases[$sid]) || !isset($this->bases[$sid][$poi])) {
                  $item = $this->db->get('ticket_template_base', array('scenic_id'=>$sid,'view_point'=>$poi));
                  if(!$item) {
                        TicketTemplateBaseModel::model()->add($ticket_template);
                        $nid = TicketTemplateBaseModel::model()->getInsertId();
                        echo "\nadd TicketTemplateBase...{$nid}...";
                        $ticket_template['id'] = $nid;
                        $item = $ticket_template;
                  }
                  $this->bases[$sid][$poi] = $item;
                  return $item['id'];
            }
            return $this->bases[$sid][$poi]['id'];
      }

      protected function get_pois($scenic_id) {
            if (!is_array($scenic_id)) $scenic_id = explode(',', $scenic_id);
            foreach($scenic_id as $key=>$value) {
                  if (isset($this->scenics[$value])) {
                        unset($scenic_id[$key]);
                  }
            }
            if ($scenic_id) {
                  $ids = implode(',', $scenic_id);
                  $list = ScenicModel::model()->getScenicList(array('ids'=>$ids,'show_poi'=>1,'show_poi_flag'=>1,'items'=>300));
                  if(!$list || !$list['body'] || !$list['body']['data']) {
                        throw new Exception('scenic not exists');
                  }
                  foreach($list['body']['data'] as $value) {
                       $tmp = array();
                       foreach($value['poi_list'] as $val) {
                              $tmp[$val['poi_id']] = $val;
                       }
                       $value['pois'] = $tmp;
                       $this->scenics[$value['id']] = $value;
                  }
            }
            return true;
      }

      protected function get_ticket_templates() {
            $sql = 'select * from ticket_template where id not in(select distinct product_id from ticket_template_items)';
            // $sql = 'select * from ticket_template where id=175';
            return $this->db->setListKey('id')->selectBySql($sql);
      }
}

$test = new Crontab_MergeData;
