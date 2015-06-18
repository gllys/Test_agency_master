<?php
require dirname(__FILE__) . '/Base.php';

class Crontab_Stattest extends Process_Base
{
    protected $OrderModel;
    protected $LandscapeModel;
    protected $TicketModel;
    protected $config;
    protected $logPath;
    protected $st;
    protected $et;

    public function run() {
        $argv = $_SERVER['argv'];
        $this->st = isset($argv[1]) ? strtotime($argv[1]) : strtotime(date('Y-m-01 00:00:01'));
        $this->et = isset($argv[2]) ? strtotime($argv[2]) : strtotime(date('Y-m-d 23:59:59'));
        $this->OrderModel = OrderModel::model();
        $this->LandscapeModel = LandscapeModel::model();
        $this->TicketModel = TicketModel::model();
        $this->config = Yaf_Registry::get("config");
        $this->logPath = $this->config['log']['path'];

        // $this->genSupplierStat();
        // $this->genDistributorStat();
        // $this->genLandscapeStat();
        $this->genMonthSupplierStat();
        $this->genMonthLandscapeStat();
    }

    protected function genMonthLandscapeStat() {
        echo "genMonthLandscapeStat..\n";
        $sql = "select 
        DATE_FORMAT(from_unixtime(a.use_time),'%Y%m') as month,
 a.landscape_id,
 count(a.id) as total_nums, 
 sum(b.price) as amount
 from tickets as a left join orders as b on a.order_id=b.id
 where a.status = 2 
 group by month,a.landscape_id;";
        $rows = $this->TicketModel->db
            ->selectBySql($sql);
        $total = $rows ? count($rows) : 0 ;
        echo "total $total..\n";
        if ($rows) {
            $ms = array();
            $items = array();
            foreach($rows as $row) {
                $id = $row['landscape_id'];
                $m = $row['month'];
                $num = $row['total_nums'];
                $amount = $row['amount'];
                $items[$id][$m]['num'] = $num;
                $items[$id][$m]['amount'] = $amount;
                $ms[$m] = $m;
                // $info = $this->LandscapeModel->getDetail($id);
                // $name = $info ? $info['name'] : '';
                // $code .= "{$row[landscape_id]},{$name},{$row[total_nums]}\n";
            }
            //标题栏
            sort($ms);
            $code = "景区名称,";
            foreach ($ms as $m) {
                $code .= $m."游客,".$m."金额,";
            }
            $code .= "\n";

            foreach($items as $id => $values) {
                $info = $this->LandscapeModel->getDetail($id);
                $name = $info ? $info['name'] : '';

                $code .= "{$name},";
                foreach ($ms as $m) {
                    $num = intval($values[$m]['num']);
                    $code .= $num.",";
                    $amount = intval($values[$m]['amount']);
                    $code .= $amount.",";
                }
                $code .= "\n";
            }

            $file = "genMonthLandscapeStat.csv";
            @unlink($this->logPath .'/'. $file);
            Log_Base::save($file,$code);
        }
    }

    protected function genMonthSupplierStat() {
        echo "genMonthSupplierStat..\n";
        $sql = "select 
 date(from_unixtime(created_at)) as day,
 supplier_id,
 supplier_name,
 count(id) as order_nums,
 sum(nums) as total_nums,
 sum(used_nums) as total_used_nums,
 sum(refunded_nums) as total_refunded_nums, 
 sum(amount) as total_amount,
 sum(refunded) as total_refunded
 from orders 
 where status in ('paid','finish','billed') and created_at between {$this->st} and {$this->et}
 group by day,supplier_id;";
        $rows = $this->OrderModel->db
            ->selectBySql($sql);
        $total = $rows ? count($rows) : 0 ;
        echo "total $total..\n";
        if ($rows) {
            $code = "日期,供应商编号,供应商名称,订单数量,产品销量,产品已使用,产品已退,销售金额,退款金额\n";
            foreach($rows as $row) {
                $code .= "{$row[day]},{$row[supplier_id]},{$row[supplier_name]},{$row[order_nums]},{$row[total_nums]},{$row[total_used_nums]},{$row[total_refunded_nums]},{$row[total_amount]},{$row[total_refunded]}\n";
            }
            $date = date("Ymd",$this->st);
            $file = "genSupplierStat_{$date}.csv";
            @unlink($this->logPath .'/'. $file);
            Log_Base::save($file,$code);
        }
        
    }

    protected function genSupplierStat() {
        echo "gen supplier stat..\n";
        $sql = "select 
 date(from_unixtime(created_at)) as day,
 supplier_id,
 supplier_name,
 count(id) as order_nums,
 sum(nums) as total_nums,
 sum(used_nums) as total_used_nums,
 sum(refunded_nums) as total_refunded_nums, 
 sum(amount) as total_amount,
 sum(refunded) as total_refunded
 from orders 
 where status in ('paid','finish','billed') and created_at between {$this->st} and {$this->et}
 group by day,supplier_id;";
        $rows = $this->OrderModel->db
            ->selectBySql($sql);
        $total = $rows ? count($rows) : 0 ;
        echo "total $total..\n";
        if ($rows) {
            $code = "日期,供应商编号,供应商名称,订单数量,产品销量,产品已使用,产品已退,销售金额,退款金额\n";
            foreach($rows as $row) {
                $code .= "{$row[day]},{$row[supplier_id]},{$row[supplier_name]},{$row[order_nums]},{$row[total_nums]},{$row[total_used_nums]},{$row[total_refunded_nums]},{$row[total_amount]},{$row[total_refunded]}\n";
            }
            $date = date("Ymd",$this->st);
            $file = "genSupplierStat_{$date}.csv";
            @unlink($this->logPath .'/'. $file);
            Log_Base::save($file,$code);
        }
        
    }

    protected function genDistributorStat() {
        echo "gen distributor stat..\n";
        $sql = "select 
 date(from_unixtime(created_at)) as day,
 distributor_id,
 distributor_name,
 count(id) as order_nums,
 sum(nums) as total_nums,
 sum(used_nums) as total_used_nums,
 sum(refunded_nums) as total_refunded_nums, 
 sum(amount) as total_amount,
 sum(refunded) as total_refunded
 from orders 
 where status in ('paid','finish','billed') and created_at between {$this->st} and {$this->et}
 group by day,distributor_id;";
        $rows = $this->OrderModel->db
            ->selectBySql($sql);
        $total = $rows ? count($rows) : 0 ;
        echo "total $total..\n";
        if ($rows) {
            $code = "日期,分销商编号,分销商名称,订单数量,产品销量,产品已使用,产品已退,销售金额,退款金额\n";
            foreach($rows as $row) {
                $code .= "{$row[day]},{$row[distributor_id]},{$row[distributor_name]},{$row[order_nums]},{$row[total_nums]},{$row[total_used_nums]},{$row[total_refunded_nums]},{$row[total_amount]},{$row[total_refunded]}\n";
            }
            $date = date("Ymd",$this->st);
            $file = "genDistributorStat_{$date}.csv";
            @unlink($this->logPath .'/'. $file);
            Log_Base::save($file,$code);
        }
    }

    protected function genLandscapeStat() {
        echo "gen landscape stat..\n";
        $sql = "select 
 landscape_id,
 count(id) as total_nums
 from tickets 
 where status = 2 and use_time between {$this->st} and {$this->et}
 group by landscape_id;";
        $rows = $this->TicketModel->db
            ->setListKey('landscape_id')
            ->selectBySql($sql);
        $total = $rows ? count($rows) : 0 ;
        echo "total $total..\n";
        if ($rows) {
            $code = "景区编号,景区名称,游客人次\n";
            foreach($rows as $id => $row) {
                $info = $this->LandscapeModel->getDetail($id);
                $name = $info ? $info['name'] : '';
                $code .= "{$row[landscape_id]},{$name},{$row[total_nums]}\n";
            }
            $date = date("Ymd",$this->st);
            $file = "genLandscapeStat_{$date}.csv";
            @unlink($this->logPath .'/'. $file);
            Log_Base::save($file,$code);
        }
    }
}

$test = new Crontab_Stattest;
