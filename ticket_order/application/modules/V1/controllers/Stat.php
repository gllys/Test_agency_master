<?php

/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/23
 * Time: 16:00
 */
class StatController extends Base_Controller_Api
{
    /**
     * 按天显示人次和销售额
     * author : yinjian
     */
    public function listAction()
    {
        $cooperation_type = intval($this->body['cooperation_type']);
        $supplier_id = intval($this->body['supplier_id'])>0?intval($this->body['supplier_id']):Lang_Msg::error('供应商id不能为空');
        // 前端传过来
        $distributor_id = $this->body['distributor_id']?$this->body['distributor_id']:Lang_Msg::error('分销商id不能为空');
        !Validate::isString($this->body['date']) && Lang_Msg::error('日期不能为空');
        $date = explode(' - ',$this->body['date']);
        $start_date = reset($date);
        $end_date = end($date);
        $data = array();
        switch($cooperation_type){
            // 合作的所有分销商
            case 0:
                // select id,day,supplier_id,sum(num_total),sum(price_total) from day_report where supplier_id = 3 and distributor_id in (5,6,8) group by day;
                $my_distribute_where = 'day between \''.$start_date.'\' AND \''.$end_date.'\' AND supplier_id = '.$supplier_id.' AND distributor_id in ('.$distributor_id.') group by day';
                $my_distribute_fields = 'id,day,supplier_id,sum(num_total) as num_total,sum(price_total) as price_total';
                $data = DayReportModel::model()->search($my_distribute_where,$my_distribute_fields);
                break;
            // 未合作的所有分销商
            case 1:
                $my_distribute_where = 'day between \''.$start_date.'\' AND \''.$end_date.'\' AND supplier_id = '.$supplier_id.' AND distributor_id not in ('.$distributor_id.') group by day';
                $my_distribute_fields = 'id,day,supplier_id,sum(num_total) as num_total,sum(price_total) as price_total';
                $data = DayReportModel::model()->search($my_distribute_where,$my_distribute_fields);
                break;
            // 单个分销商
            case 2:
                $solo_distribute_where = 'supplier_id ='.$supplier_id.' AND distributor_id = '.intval($distributor_id).' AND day between \''.$start_date.'\' AND \''.$end_date.'\' group by day';
                $solo_distribute_fields = 'id,day,supplier_id,sum(num_total) as num_total,sum(price_total) as price_total';
                $data = DayReportModel::model()->search($solo_distribute_where,$solo_distribute_fields);
                break;
            default:
                Lang_Msg::error('类型错误');
        }
        $day_stat = array();
        foreach($data as $k=>$v){
            $day_stat[$v['day']] = $v;
        }
        Lang_Msg::output($day_stat);
    }

    /**
     * 统计产品数
     * author : yinjian
     */
    public function productAction()
    {
        // 可以传多个产品id
        $product_id = $this->body['product_id'];
        $supplier_id = intval($this->body['supplier_id'])>0?intval($this->body['supplier_id']):Lang_Msg::error('供应商id不能为空');
        $distributor_id = $this->body['distributor_id']?$this->body['distributor_id']:Lang_Msg::error('分销商id不能为空');
        $date_type = in_array($this->body['date_type'],array(1,2)) ? $this->body['date_type']:Lang_Msg::error('日期类型不正确');
        $date = $this->body['date'];
        // 按年统计
        if($date_type == 1){
            $start_date = $date.'-01-01';
            $end_date = $date.'-12-31';
        }elseif($date_type == 2){
            // 按月统计
            $start_date = $date.'-01';
            $end_date = date('Y-m-d', strtotime("$start_date +1 month -1 day"));
        }
        // 平台分销商
        // select sum(num_total),distributor_id from day_report where day between '2015-01-01' AND '2015-01-31' AND product_id in (731) group by supplier_id,distributor_id
        $other_all_where = 'day between \''.$start_date.'\' AND \''.$end_date.'\' AND product_id in ('.$product_id.') AND supplier_id = '.$supplier_id.' AND distributor_id not in ('.$distributor_id.') group by supplier_id';
        $orher_all_fields = 'sum(num_total) as num_total';
        $other_all_data = reset(DayReportModel::model()->search($other_all_where,$orher_all_fields));
        // select sum(num_total),distributor_id from day_report where day between '2015-01-01' AND '2015-01-31' AND product_id = 731 group by supplier_id,distributor_id
        // 产品统计 前9的为合作分销商，10为合作的其他分销商，未合作的分销商
        $where = 'day between \''.$start_date.'\' AND \''.$end_date.'\' AND product_id in ('.$product_id.') AND supplier_id = '.$supplier_id.' AND distributor_id in ('.$distributor_id.') group by supplier_id,distributor_id';
        $fields = 'id,sum(num_total) as num_total,distributor_id';
        $day_data_cooperation = DayReportModel::model()->search($where,$fields);
        $num_total = array();
        foreach($day_data_cooperation as $k=>$v){
            $num_total[] = $v['num_total'];
        }
        array_multisort($num_total,SORT_DESC,$day_data_cooperation);
        // 统计前9的平台分销商
        if(!$other_all_data){
            $other_all_data = array('num_total'=>0);
            $my_top_8 = array_slice($day_data_cooperation,0,9);
            $my_other = array_slice($day_data_cooperation,9);
        }else{
            $my_top_8 = array_slice($day_data_cooperation,0,8);
            $my_other = array_slice($day_data_cooperation,8);
        }
        $other_data['num_total'] = 0;
        foreach($my_other as $k=>$v){
            $other_data['num_total'] += $v['num_total'];
        }
        Lang_Msg::output(array('top8'=>$my_top_8,'other'=>$other_data,'other_all'=>$other_all_data));
    }

    /**
     * 平台订单销量金额统计
     * author : yinjian
     */
    public function plateform_listAction()
    {
        $date = trim($this->body['date']);
//        !Validate::isString($date) && Lang_Msg::error('日期不能为空');
        !in_array($this->body['type'],array('whole','scenic','agency')) && Lang_Msg::error('类型不正确');
//        !in_array($this->body['date_type'],array(1,2,3)) && Lang_Msg::error('日期类型不正确');
        $start_date = isset($this->body['start_date'])?$this->body['start_date']:reset(explode(' - ',$date));
        $start_date = $start_date?strtotime($start_date):0;
        $end_date = isset($this->body['end_date'])?$this->body['end_date']:end(explode(' - ',$date));
        $end_date = $end_date?strtotime($end_date.' 23:59:59'):time();
        $type = $this->body['type'];
        $fields = 'COUNT(*) order_num,SUM(nums) person_num,SUM(used_nums) used_person_num,SUM(nums-used_nums-refunded_nums) unused_person_num,SUM(refunded_nums) refunded_person_num,SUM(amount) amount,SUM(amount-refunded) receive_amount,SUM(refunded) refunded';
        if($type == 'whole'){
            // 供应商
            $supplier_id = trim($this->body['supplier_id']);
            $fields_whole = 'supplier_id,';
            $where = '`status` IN (\'paid\',\'finish\',\'billed\') AND ';
            if($supplier_id) $where .= 'supplier_id in ('.$supplier_id.') AND ';
            $where .= ' nums>=used_nums+refunding_nums+refunded_nums AND `created_at` BETWEEN '.$start_date.' AND '.$end_date;
            $group_by = ' GROUP BY `supplier_id`';
            $count = reset(OrderModel::model()->db->selectBySql('SELECT count(*) COUNT FROM (SELECT COUNT(*) COUNT FROM orders WHERE '.$where.$group_by.') a'));
            $this->count = reset($count);
            $this->pagenation();
            $data['data'] = OrderModel::model()->search($where.$group_by,$fields_whole.$fields,null,$this->limit,'supplier_id');
        }elseif($type == 'scenic'){
            // 景区
            $landscape_ids = trim($this->body['landscape_ids']);
            $fields_scenic = 'landscape_ids,';
            $where = 'kind=1 AND `status` IN (\'paid\',\'finish\',\'billed\') AND ';
            if($landscape_ids) $where .= 'landscape_ids in ('.$landscape_ids.') AND ';
            $where .= ' nums>=used_nums+refunding_nums+refunded_nums AND `created_at` BETWEEN '.$start_date.' AND '.$end_date;
            $group_by = ' GROUP BY `landscape_ids`';
            $count = reset(OrderModel::model()->db->selectBySql('SELECT count(*) COUNT FROM (SELECT COUNT(*) COUNT FROM orders WHERE '.$where.$group_by.') a'));
            $this->count = reset($count);
            $this->pagenation();
            $data['data'] = OrderModel::model()->search($where.$group_by,$fields_scenic.$fields,null,$this->limit,'landscape_ids');
        }elseif($type == 'agency'){
            // 分销商
            $distributor_id = trim($this->body['distributor_id']);
            $fields_agency = 'distributor_id,';
            $where = '`status` IN (\'paid\',\'finish\',\'billed\') AND ';
            if($distributor_id) $where .= 'distributor_id in ('.$distributor_id.') AND ';
            $where .= ' nums>=used_nums+refunding_nums+refunded_nums AND `created_at` BETWEEN '.$start_date.' AND '.$end_date;
            $group_by = ' GROUP BY `distributor_id`';
            $count = reset(OrderModel::model()->db->selectBySql('SELECT count(*) COUNT FROM (SELECT COUNT(*) COUNT FROM orders WHERE '.$where.$group_by.') a'));
            $this->count = reset($count);
            $this->pagenation();
            $data['data'] = OrderModel::model()->search($where.$group_by,$fields_agency.$fields,null,$this->limit,'distributor_id');
        }
        $data['amount'] = reset(OrderModel::model()->search($where,$fields,null));
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Lang_Msg::output($data);
    }

    /**
     * 平台订单销量金额统计详情
     * author : yinjian
     */
    public function plateform_detailAction()
    {
        $date = trim($this->body['date']);
//        !Validate::isString($date) && Lang_Msg::error('日期不能为空');
        !in_array($this->body['type'],array('whole','scenic','agency')) && Lang_Msg::error('类型不正确');
//        !in_array($this->body['date_type'],array(1,2,3)) && Lang_Msg::error('日期类型不正确');
        $start_date = isset($this->body['start_date'])?$this->body['start_date']:reset(explode(' - ',$date));
        $start_date = strtotime($start_date)?strtotime($start_date):0;
        $end_date = isset($this->body['end_date'])?$this->body['end_date']:end(explode(' - ',$date));
        $end_date = strtotime($end_date.' 23:59:59')?strtotime($end_date.' 23:59:59'):time();
        $type = $this->body['type'];
        $fields_day = 'FROM_UNIXTIME(`created_at`,\'%Y-%m-%d\') AS `day`,';
        $fields = 'COUNT(*) order_num,SUM(nums) person_num,SUM(used_nums) used_person_num,SUM(nums-used_nums-refunded_nums) unused_person_num,SUM(refunded_nums) refunded_person_num,SUM(amount) amount,SUM(amount-refunded) receive_amount,SUM(refunded) refunded';
        if($type == 'whole'){
            // 供应商
            $supplier_id = trim($this->body['supplier_id']);
            $where = '`status` IN (\'paid\',\'finish\',\'billed\') AND ';
            if($supplier_id) $where .= 'supplier_id in ('.$supplier_id.') AND ';
            $where .= ' nums>=used_nums+refunding_nums+refunded_nums AND `created_at` BETWEEN '.$start_date.' AND '.$end_date;
            $group_by = ' GROUP BY `day`';
            $count = reset(OrderModel::model()->db->selectBySql('SELECT count(*) COUNT FROM (SELECT FROM_UNIXTIME(`created_at`,\'%Y-%m-%d\') `day` FROM orders WHERE '.$where.$group_by.') a'));
            $this->count = reset($count);
            $this->pagenation();
            $data['data'] = OrderModel::model()->search($where.$group_by,$fields_day.$fields,null,$this->limit,'day');
        }elseif($type == 'scenic'){
            // 景区
            $landscape_ids = trim($this->body['landscape_ids']);
            $where = 'kind=1 AND `status` IN (\'paid\',\'finish\',\'billed\') AND ';
            if($landscape_ids) $where .= 'landscape_ids in ('.$landscape_ids.') AND ';
            $where .= ' nums>=used_nums+refunding_nums+refunded_nums AND `created_at` BETWEEN '.$start_date.' AND '.$end_date;
            $group_by = ' GROUP BY `day`';
            $count = reset(OrderModel::model()->db->selectBySql('SELECT count(*) COUNT FROM (SELECT FROM_UNIXTIME(`created_at`,\'%Y-%m-%d\') `day` FROM orders WHERE '.$where.$group_by.') a'));
            $this->count = reset($count);
            $this->pagenation();
            $data['data'] = OrderModel::model()->search($where.$group_by,$fields_day.$fields,null,$this->limit,'day');
        }elseif($type == 'agency'){
            // 分销商
            $distributor_id = trim($this->body['distributor_id']);
            $where = '`status` IN (\'paid\',\'finish\',\'billed\') AND ';
            if($distributor_id) $where .= 'distributor_id in ('.$distributor_id.') AND ';
            $where .= ' nums>=used_nums+refunding_nums+refunded_nums AND `created_at` BETWEEN '.$start_date.' AND '.$end_date;
            $group_by = ' GROUP BY `day`';
            $count = reset(OrderModel::model()->db->selectBySql('SELECT count(*) COUNT FROM (SELECT FROM_UNIXTIME(`created_at`,\'%Y-%m-%d\') `day` FROM orders WHERE '.$where.$group_by.') a'));
            $this->count = reset($count);
            $this->pagenation();
            $data['data'] = OrderModel::model()->search($where.$group_by,$fields_day.$fields,null,$this->limit,'day');
        }
        $data['amount'] = reset(OrderModel::model()->search($where,$fields,null));
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Lang_Msg::output($data);
    }
}