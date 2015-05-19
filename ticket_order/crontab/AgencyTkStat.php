<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-20
 * Time: 上午9:54
 */

require dirname(__FILE__) . '/Base.php';

class Crontab_AgencyTkStat extends Process_Base
{
    private $interval = 3600; //每次统计间隔3600秒

    public function run() {
        while (true) {
            $this->statistics();
            $this->sleep($this->interval);
        }
    }

    //统计分销商当月票
    public function statistics(){
        $ym = date("Y-m");
        $ym2Time = $start_time = strtotime($ym);

        //如果当日已到了下月的0点内，则继续统计上月的，避免上月结束前在间隔期有票未统计到
        $nowTime = time();
        if($this->interval> $nowTime - $ym2Time){
            $nowTime = $nowTime - $this->interval;
            $ym = date("Y-m",$nowTime);
            $ym2Time = $start_time = strtotime($ym);
        }

        $days = date("t", mktime(0, 0, 0, date("m",$ym2Time), 1, date("Y",$ym2Time)));
        $end_time = strtotime($ym."-{$days} 23:59:59");

        $OrderItemModel = new OrderItemModel();
        $OrderModel = new OrderModel();

        /*$fields = "i.distributor_id,'{$end_time}' AS created_at,SUM(i.`nums`-i.`refunded_nums`) AS ticket_nums,SUM(i.`price`*(i.`nums`-i.`refunded_nums`)) AS money_amount";
        $from = $OrderItemModel->share($ym2Time)->getTable()."` `i` JOIN `".$OrderModel->share($ym2Time)->getTable()."` `o";
        $where = " i.order_id=o.id AND i.created_at >= ".$start_time." AND i.created_at<=".$end_time." ";
        $where.=" AND o.status='billed' AND  0<i.`nums`-i.`refunded_nums` ";
        $where.=" GROUP BY i.`distributor_id` ";
        $orderby = "i.`distributor_id` ASC";

        $result = $OrderItemModel->getDb()->select($from,$where,$fields,$orderby);*/

        $fields = "distributor_id,'{$end_time}' AS created_at,SUM(nums-refunded_nums) AS ticket_nums,SUM(price*(nums-refunded_nums)) AS money_amount";
        $where = array(
            'created_at|>='=>$start_time,'created_at|<='=>$end_time,
            'status'=> 'billed','nums|exp'=>'>refunded_nums','product_id'>0
        );
        $groupby = 'distributor_id';
        $orderby = "distributor_id ASC";
        $result = $OrderModel->setGroupBy($groupby)->search($where,$fields,$orderby);

        if($result){
            array_unshift($result,array_keys(reset($result)));
            AgencyTkStatModel::model()->replace($result);
        }
    }
}

$test = new Crontab_AgencyTkStat;
