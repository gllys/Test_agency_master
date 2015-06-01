<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 2015-04-29
 * Time: 下午1:03
 */

require dirname(__FILE__) . '/Base.php';

class Crontab_SaleStat extends Process_Base
{
    private $interval = 3600; //每次间隔1小时
    private $start_time = 0;
    private $end_time = 0;
    private $year = 2015;
    private $month = 1;
    private $day = 1;

    public function run()
    {
        /*
         * for ($month = 10; $month <= 12; $month++) { //统计历史数据
            $this->statYm(2014, $month);
        }

        $currM = date("n");
        for ($month = 1; $month <= $currM; $month++) { //统计历史数据
            $this->statYm(2015, $month);
        }
        */

        while (true) {
            $hour = intval(date("H")); //当前小时
            $runTime = 0;
            if($hour>=2 && $hour<=4) {
                $this->OrderModel = new OrderModel();
                $this->SaleStatModel = new SaleStatModel();
                $this->statYestoday();
                $runTime = 3600;
            }
            $this->sleep($this->interval+$runTime);
        }
    }

    public function statYestoday() //统计昨天销售数据
    {
        $day = date("Y-m-d", strtotime("-1 day"));
        $this->start_time = strtotime($day);
        $this->end_time = strtotime("{$day} 23:59:59");
        $this->year = substr($day, 0, 4);
        $this->month = substr($day, 5, 2);
        $this->day = substr($day, 8, 2);
        $this->statistics();
    }

    public function statYm($year, $month)
    { //统计某月份的数据
        $this->OrderModel = new OrderModel();
        $this->SaleStatModel = new SaleStatModel();
        $this->year = $year;
        $this->month = $month;

        $days = date("t", mktime(0, 0, 0, $month, 1, $year));
        for ($d = 1; $d <= $days; $d++) {
            $day = date("Y-m-d", strtotime($year . "-" . $month . "-" . $d));
            $this->start_time = strtotime($day);
            $this->end_time = strtotime("{$day} 23:59:59");
            $this->day = $d;
            $this->statistics();
        }
    }


    private function statistics() //1可退已使用，2不可退，3可退有抵用券且已使用
    {
        $fields = "id,supplier_id AS supply_id,distributor_id AS agency_id,product_id,price_type,
            ticket_infos,price,nums,used_nums,activity_paid,refund,use_time,created_at,updated_at,
            supplier_name AS supply_name,distributor_name AS agency_name,name AS product_name";
        $where = "((refund=1 AND used_nums>0 AND use_time>{$this->start_time} AND use_time<{$this->end_time} )
	        OR (refund=0 AND updated_at>{$this->start_time} AND updated_at<{$this->end_time})) AND product_id>0";
        $orderby = "created_at ASC";
        $orders = $this->OrderModel->search($where, $fields, $orderby);
        if (!empty($orders)) {
            $result = array();
            foreach ($orders as $order) {
                $key = $order['supply_id'] . '_' . $order['agency_id'] . '_' . $order['product_id'] . '_' . $order['price_type'];
                if (!isset($result[$key])) {
                    $result[$key] = array(
                        'datetime' => $this->end_time,
                        'supply_id' => $order['supply_id'],
                        'agency_id' => $order['agency_id'],
                        'product_id' => $order['product_id'],
                        'price_type' => $order['price_type'],
                        'visitors' => 0,
                        'amount' => 0,
                        'nums' => 0,
                        'order_nums' => 0,
                        'year' => $this->year,
                        'month' => $this->month,
                        'day' => $this->day,
                        'supply_name' => $order['supply_name'],
                        'agency_name' => $order['agency_name'],
                        'product_name' => $order['product_name'],
                    );
                }
                $result[$key]['order_nums'] += 1;
                $nums = 0;
                if ($order['refund'] == 0 || ($order['used_nums'] > 0 && $order['activity_paid'] > 0)) {
                    $nums = $order['nums'];
                } else {
                    $nums = $order['used_nums'];
                }
                $result[$key]['nums'] += $nums;

                $ticketInfos = json_decode($order['ticket_infos'], true);
                if (empty($ticketInfos)) {
                    $result[$key]['visitors'] += $nums;
                } else {
                    foreach ($ticketInfos as $ticket) {
                        $result[$key]['visitors'] += $nums * $ticket['num'];
                    }
                }

                $result[$key]['amount'] += $nums * $order['price'];
            }
            if ($result) {
                echo "统计" . date("Y-m-d", $this->start_time) . ": " . count($result) . "笔数据被更新\n";
                array_unshift($result, array_keys(reset($result)));
                $this->SaleStatModel->replace($result);
            }
        }
    }
}

$test = new Crontab_SaleStat;
