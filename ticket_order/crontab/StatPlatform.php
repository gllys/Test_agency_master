<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/3/9
 * Time: 14:38
 */
date_default_timezone_set('PRC');
require dirname(__FILE__) . '/Base.php';

class Crontab_StatPlatform extends Process_Base
{
    public function run()
    {
        while (true) {
            echo 'start_time:'.date('Y-m-d H:i:s')."\n";
            // 预定日期
            $date_time_sql = "SELECT DATE_FORMAT(FROM_UNIXTIME( `created_at`),'%Y-%m-%d') date_time FROM orders WHERE created_at>0 AND created_at<".strtotime(date('Y-m-d')) ." AND `status` in ('paid','finish','billed') GROUP BY date_time ORDER BY date_time ASC";
            $date_time = OrderItemModel::model()->db->selectBySql($date_time_sql);
            while($yesterday = array_shift($date_time)){
                if(!DaySupplyReportModel::model()->get(array('day'=>$yesterday))){
                    echo 'statSupply:'.$yesterday['date_time']."\n";
                    $this->statSupply($yesterday['date_time'],1);
                }
                if(!DayScenicReportModel::model()->get(array('day'=>$yesterday))){
                    echo 'statScenic:'.$yesterday['date_time']."\n";
                    $this->statScenic($yesterday['date_time'],1);
                }
            }
            $this->sleep(strtotime(date('Y-m-d',strtotime("+1 day")))-time());
        }
    }

    /**
     * 统计昨天的所有供应
     * author : yinjian
     */
    public function statSupply($yesterday,$date_type)
    {
        $now = time();
        // 1预定2游玩3入园
        switch($date_type){
            case 1:
                $yesterday_start = strtotime($yesterday.' 00:00:00');
                $yesterday_end = strtotime($yesterday.' 23:59:59');
                $data = $data2 = $data3 = array();
                // 指定日期的订单数supplier_id，order_num，amount,person_num
                $sql = 'SELECT supplier_id,COUNT(1) order_num,SUM(amount) amount,SUM(nums) person_num,GROUP_CONCAT(id) ids FROM orders WHERE `status` in (\'paid\',\'finish\',\'billed\') and created_at BETWEEN '.$yesterday_start.' AND '.$yesterday_end.' GROUP BY supplier_id';
                $data = OrderItemModel::model()->db->selectBySql($sql);
                // 当天已使用产品数 used_person_num
                $sql_used_person_num = 'SELECT supplier_id,COUNT(1) used_person_num FROM order_items WHERE created_at BETWEEN '.$yesterday_start.' AND '.$yesterday_end.' AND use_time BETWEEN '.$yesterday_start.' AND '.$yesterday_end .' GROUP BY supplier_id';
                $data2 = OrderItemModel::model()->db->selectBySql($sql_used_person_num);
                $used_person_num = array();
                foreach($data2 as $k => $v){
                    $used_person_num[$v['supplier_id']] = $v;
                }
                // 退款金额 && 格式规整
                foreach($data as $k => $v){
                    $data[$k]['day'] = $yesterday;
                    if(isset($used_person_num[$v['supplier_id']])){
                        $data[$k]['used_person_num'] = $used_person_num[$v['supplier_id']]['used_person_num'];
                    }else{
                        $data[$k]['used_person_num'] = 0;
                    }
                    $sql_refund = 'SELECT supplier_id,nums `refunded_person_num`,SUM(money) refunded FROM refund_apply WHERE allow_status =1 AND order_id IN ('.trim($v['ids'],",").') AND updated_at BETWEEN '.$yesterday_start.' AND '.$yesterday_end.' GROUP BY supplier_id';
                    $tmp = OrderItemModel::model()->db->selectBySql($sql_refund);
                    if($refund = reset($tmp)){
                        $data[$k]['refunded_person_num'] = $refund['refunded_person_num'];
                        $data[$k]['refunded'] = $refund['refunded'];
                    }else{
                        $data[$k]['refunded_person_num'] = 0;
                        $data[$k]['refunded'] = 0;
                    }
                    $data[$k]['unused_person_num'] = $v['person_num'] - $data[$k]['used_person_num'] - $data[$k]['refunded_person_num'];
                    $data[$k]['receive_amount'] = $v['amount'] - $data[$k]['refunded'];
                    $data[$k]['created_at'] = $data[$k]['updated_at'] = $now;
                    unset($data[$k]['ids']);
                }
                $this->addSupply($data);
                break;
            case 2:
                break;
            case 3:
                break;
        }
    }

    /**
     * 统计昨天的所有景区
     * author : yinjian
     * @param $yesterday
     * @param $date_type
     */
    public function statScenic($yesterday,$date_type)
    {
        $now = time();
        // 1预定2游玩3入园
        switch($date_type){
            case 1:
                $yesterday_start = strtotime($yesterday.' 00:00:00');
                $yesterday_end = strtotime($yesterday.' 23:59:59');
                $data = $data2 = $data3 = array();
                // 指定日期的订单数supplier_id，order_num，amount,person_num
                $sql = 'SELECT landscape_ids,COUNT(1) order_num,SUM(amount) amount,SUM(nums) person_num,GROUP_CONCAT(id) ids FROM orders WHERE kind = 1 AND `status` in (\'paid\',\'finish\',\'billed\') and created_at BETWEEN '.$yesterday_start.' AND '.$yesterday_end.' GROUP BY landscape_ids';
                $data = OrderItemModel::model()->db->selectBySql($sql);
                // 当天已使用产品数 used_person_num
                $sql_used_person_num = 'SELECT landscape_ids,COUNT(1) used_person_num FROM order_items WHERE kind = 1 AND created_at BETWEEN '.$yesterday_start.' AND '.$yesterday_end.' AND use_time BETWEEN '.$yesterday_start.' AND '.$yesterday_end .' GROUP BY landscape_ids';
                $data2 = OrderItemModel::model()->db->selectBySql($sql_used_person_num);
                $used_person_num = array();
                foreach($data2 as $k => $v){
                    $used_person_num[$v['landscape_ids']] = $v;
                }
                // 退款金额 && 格式规整
                foreach($data as $k => $v){
                    $data[$k]['day'] = $yesterday;
                    if(isset($used_person_num[$v['landscape_ids']])){
                        $data[$k]['used_person_num'] = $used_person_num[$v['landscape_ids']]['used_person_num'];
                    }else{
                        $data[$k]['used_person_num'] = 0;
                    }
                    $sql_refund = 'SELECT landscape_id `landscape_ids`,nums `refunded_person_num`,SUM(money) refunded FROM refund_apply WHERE allow_status =1 AND order_id IN ('.trim($v['ids'],",").') AND updated_at BETWEEN '.$yesterday_start.' AND '.$yesterday_end.' GROUP BY landscape_id';
                    $tmp = OrderItemModel::model()->db->selectBySql($sql_refund);
                    if($refund = reset($tmp)){
                        $data[$k]['refunded_person_num'] = $refund['refunded_person_num'];
                        $data[$k]['refunded'] = $refund['refunded'];
                    }else{
                        $data[$k]['refunded_person_num'] = 0;
                        $data[$k]['refunded'] = 0;
                    }
                    $data[$k]['unused_person_num'] = $v['person_num'] - $data[$k]['used_person_num'] - $data[$k]['refunded_person_num'];
                    $data[$k]['receive_amount'] = $v['amount'] - $data[$k]['refunded'];
                    $data[$k]['created_at'] = $data[$k]['updated_at'] = $now;
                    unset($data[$k]['ids']);
                }
                $this->addScenic($data);
                break;
            case 2:
                break;
            case 3:
                break;
        }
    }

    /**
     * 批量添加景区统计
     * author : yinjian
     * @param $data
     */
    public function addScenic($data)
    {
        array_unshift($data,array_keys(reset($data)));
        DayScenicReportModel::model()->begin();
        try{
            DayScenicReportModel::model()->add($data);
            DayScenicReportModel::model()->commit();
        }catch (Exception $e){
            DayScenicReportModel::model()->rollback();
            var_dump($e->getMessage());
            echo 'fails'."\n";
        }
    }

    /**
     * 批量添加数据
     * author : yinjian
     * @param $data
     */
    public function addSupply($data)
    {
        array_unshift($data,array_keys(reset($data)));
        DaySupplyReportModel::model()->begin();
        try{
            DaySupplyReportModel::model()->add($data);
            DaySupplyReportModel::model()->commit();
        }catch (Exception $e){
            DaySupplyReportModel::model()->rollback();
            var_dump($e->getMessage());
            echo 'fails'."\n";
        }
    }
}

$statPlatform = new Crontab_StatPlatform();