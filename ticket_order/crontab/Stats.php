<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/23
 * Time: 18:56
 */
date_default_timezone_set('PRC');
require dirname(__FILE__) . '/Base.php';

class Crontab_Stats extends Process_Base
{
    public function run()
    {
        while (true) {
            $date_time_sql = "select date_format(FROM_UNIXTIME( `use_time`),'%Y-%m-%d') date_time from order_items where use_time>0 group by date_time order by date_time asc";
            $date_time = OrderItemModel::model()->db->selectBySql($date_time_sql);
            foreach($date_time as $k=>$v){
                echo $v['date_time']."_";
                if(!DayReportModel::model()->get(array('day'=>$v['date_time']))){
                    if(date("Y-m-d") != $v['date_time']){
                        echo $v['date_time']."\n";
                        $this->stat($v['date_time']);
                    }
                }
            }
//            $yesterday = date("Y-m-d",strtotime("-1 day"));
            $this->sleep(strtotime(date('Y-m-d',strtotime("+1 day")))-time());
        }
    }

    /**
     * 统计昨天的所有核销产品分销
     * author : yinjian
     */
    public function stat($yesterday)
    {
        $now = time();
        // 昨日起始时间结束时间
        $yesterday_start = strtotime($yesterday.' 00:00:00');
        $yesterday_end = strtotime($yesterday.' 23:59:59');
        // select supplier_id,distributor_id,product_id,count(*) as product_num,sum(price) as `price_total` from `order_items201501` WHERE `status`=2 AND use_time BETWEEN 1421942400 AND 1422028799 GROUP BY distributor_id,supplier_id,product_id
        $OrderItemModel = new OrderItemModel();
        // 统计金额
        $where = "status=2 AND use_time BETWEEN ".$yesterday_start." AND ".$yesterday_end." GROUP BY distributor_id,supplier_id,product_id";
        $fields = 'id,supplier_id,distributor_id,product_id,COUNT(*) as product_num,sum(price) as price_total';
        $day_report = $OrderItemModel->search($where,$fields);
        // 组合三者关系
        $distributor_supplier_product = array();
        $distributor_supplier_product_price = array();
        foreach($day_report as $k=>$v){
            $distributor_supplier_product_price[$v['distributor_id'].'_'.$v['supplier_id'].'_'.$v['product_id']] = $v;
        }
        // 统计人次
        // "select count(*) as num_total,distributor_id,supplier_id,product_id from `tickets` WHERE status=0 AND poi_used_num>0 AND `use_time` BETWEEN 1421942400 AND 1422028799 GROUP BY distributor_id,supplier_id,product_id"
        $day_num_where = "status=2 AND `use_time` BETWEEN ".$yesterday_start." AND ".$yesterday_end." GROUP BY distributor_id,supplier_id,product_id";
        $day_num_fields = "id,count(*) as num_total,distributor_id,supplier_id,product_id";
        $ticketsModel = new TicketModel();
        $day_num_report = $ticketsModel->search($day_num_where,$day_num_fields);
        // 整理格式
        foreach($day_num_report as $k=>$v){
            unset($v['id']);
            $key = $v['distributor_id'].'_'.$v['supplier_id'].'_'.$v['product_id'];
            $distributor_supplier_product[$key] = $v;
            $distributor_supplier_product[$key]['day'] = $yesterday;
            $distributor_supplier_product[$key]['updated_at'] = $distributor_supplier_product[$key]['created_at'] = $now;
            // 填充价格和产品数目
            if(isset($distributor_supplier_product_price[$key])){
                $distributor_supplier_product[$key]['price_total'] = $distributor_supplier_product_price[$key]['price_total'];
                $distributor_supplier_product[$key]['product_num'] = $distributor_supplier_product_price[$key]['product_num'];
            }else{
                $distributor_supplier_product[$key]['price_total'] = 0;
                $distributor_supplier_product[$key]['product_num'] = 0;
            }
        }
        try{
            DayReportModel::model()->begin();
            foreach($distributor_supplier_product as $kk=>$vv){
                DayReportModel::model()->add($vv);
            }
            DayReportModel::model()->commit();
        } catch (Exception $e){
            DayReportModel::model()->rollBack();
        }
    }
}

$stats = new Crontab_Stats();