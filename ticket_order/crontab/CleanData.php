<?php

require dirname(__FILE__) . '/Base.php';

class Crontab_CleanData extends Process_Base
{
    const MAX_PROCESS = 100;
    private $distributorArr = [];
    private $supplierArr = [];
    private $distributorStr = '';
    private $supplierStr = '';

    public function init()
    {
        $this->distributorArr = $this->getDistributor();
        $this->supplierArr = $this->getSupplier();
        $this->distributorStr = implode(',', $this->distributorArr);
        $this->supplierStr = implode(',', $this->supplierArr);
    }

    public function run()
    {
        $db = Db_Mysql::factory('itourism');
        $db->begin();
        try {
            echo '清理开始...', PHP_EOL;
            $this->removeOrders();
            $this->removePayments();
            $this->removeTransactionFlow();
            $this->removeBill();
            $this->removeAgencyTkStat();
            $this->removeDayReport();
            $this->removeTicketCode();
            $this->removeSaleStat();
            echo '清理成功...', PHP_EOL;
            $db->commit();
        } catch (Exception $e) {
            print_r($e);
            $db->rollback();
            echo '清理失败...', PHP_EOL;
        }
    }

    protected function removeOrders()
    {
        $dIds = $this->distributorStr;
        $sIds = $this->supplierStr;
        $sql = "select * from `orders` where `distributor_id` in ({$dIds}) or `supplier_id` not in ({$sIds})";
        $yield = $this->findAll($sql);
        $process = function ($orderIDStr) {
            if (empty($orderIDStr)) {
                echo "orders 没有记录", PHP_EOL;
                return;
            }
            $sql = "delete from `order_items` where `order_id` in ({$orderIDStr})";
            $this->execSql($sql);
            echo "order_items ok", PHP_EOL;

            $sql = "delete from `tickets` where `order_id` in ({$orderIDStr})";
            $this->execSql($sql);
            echo "tickets ok", PHP_EOL;

            $sql = "delete from `ticket_items` where `order_id` in ({$orderIDStr})";
            $this->execSql($sql);
            echo "ticket_items ok", PHP_EOL;

            $sql = "delete from `ticket_record` where `record_code` in ({$orderIDStr})";
            $this->execSql($sql);
            echo "ticket_record ok", PHP_EOL;

            $sql = "delete from `ticket_refund` where `order_id` in ({$orderIDStr})";
            $this->execSql($sql);
            echo "ticket_refund ok", PHP_EOL;

            $sql = "delete from `payment_orders` where `order_id` in ({$orderIDStr})";
            $this->execSql($sql);
            echo "payment_orders ok", PHP_EOL;

            $sql = "delete from `order_queue` where order_id in ({$orderIDStr})";
            $this->execSql($sql);
            echo "order_queue ok", PHP_EOL;

            $sql = "delete from `order_exchange` where `order_id` in ({$orderIDStr})";
            $this->execSql($sql);
            echo "order_exchange ok", PHP_EOL;

            $sql = "delete from `order_item_used` where `order_id` in ({$orderIDStr})";
            $this->execSql($sql);
            echo "order_item_used ok", PHP_EOL;

            $sql = "delete from `sms_log` where `order_id` in ({$orderIDStr})";
            $this->execSql($sql);
            echo "sms_log ok", PHP_EOL;

            $this->removeRefundApply($orderIDStr);
        };
        $this->processHandler($yield, $process);
        $sql = "delete from `orders` where `distributor_id` in ({$dIds}) or `supplier_id` not in ({$sIds})";
        $this->execSql($sql);
        echo "orders ok", PHP_EOL;
    }

    protected function removePayments()
    {
        $sql = "delete from `payments` where `distributor_id` in ({$this->distributorStr})";
        $this->execSql($sql);
        echo "payments ok", PHP_EOL;
    }
    protected function removeTransactionFlow()
    {
        $sql = "delete from `transaction_flow` where `agency_id` in ({$this->distributorStr}) or `supplier_id` not in ({$this->supplierStr})";
        $this->execSql($sql);
        echo "transaction_flow ok", PHP_EOL;
    }
    private function removeRefundApply($orderIDStr)
    {
        $sql = "select * from `refund_apply` where `order_id` in ({$orderIDStr})";
        $yield = $this->findAll($sql);

        $process = function ($idStr) {
            if (empty($idStr)) {
                echo 'RefundApply 没有记录', PHP_EOL;
                return;
            }
            $sql = "delete from `refund_apply_items` where `refund_apply_id` in ({$idStr})";
            $this->execSql($sql);
            echo "refund_apply_items ok", PHP_EOL;
        };
        $this->processHandler($yield, $process);
        $sql = "delete from `refund_apply` where `order_id` in ({$orderIDStr})";
        $this->execSql($sql);
        echo "refund_apply ok", PHP_EOL;
    }

    protected function removeBill()
    {
        $sql = "select * from `bills` where `supply_id` not in ({$this->supplierStr})";
        $yield = $this->findAll($sql);
        $process = function ($idStr) {
            if (empty($idStr)) {
                echo 'bills 没有记录', PHP_EOL;
                return;
            }
            $sql = "delete from `bills_items` where `bill_id` in ({$idStr})";
            $this->execSql($sql);
            echo "bills_items ok", PHP_EOL;
        };
        $this->processHandler($yield, $process);
        $sql = "delete from `bills` where `supply_id` not in ({$this->supplierStr})";
        $this->execSql($sql);
        echo "bills ok", PHP_EOL;
    }

    protected function removeAgencyTkStat()
    {
        $sql = "delete from `agency_tk_stat` where `distributor_id` in ({$this->distributorStr})";
        $this->execSql($sql);
        echo "agency_tk_stat ok", PHP_EOL;
    }

    protected function removeTicketCode()
    {
        $sql = "delete from `ticket_code` where `distributor_id` in ({$this->distributorStr})";
        $this->execSql($sql);
        echo "ticket_code ok", PHP_EOL;
    }

    protected function removeDayReport()
    {
        $sql = "delete from `day_report` where `distributor_id` in ({$this->distributorStr}) or `supplier_id` not in ({$this->supplierStr})";
        $this->execSql($sql);
        echo "day_report ok", PHP_EOL;
    }

    protected function removeSaleStat()
    {
        $sql = "delete from `sale_stat` where `agency_id`in ({$this->distributorStr}) or `supply_id` not in ({$this->supplierStr})";
        $this->execSql($sql);
        echo "sale_stat ok", PHP_EOL;
    }

    public function execSql($sql)
    {
        $method = substr($sql, 0, 6);
        if (strcasecmp($method, 'delete') == 0
                || strcasecmp($method, 'update') == 0
            ) {
            if (empty($_SERVER['argv'][1])
                || strcasecmp($_SERVER['argv'][1], 'clean')!=0) {
                echo PHP_EOL, $sql, PHP_EOL;
                return ;
            }
        }
        $db = Db_Mysql::factory('itourism');
        $sth = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $rt = $sth->execute();
        if (!$rt) {
            print_r($sth->errorInfo());
            return false;
        }
        return $sth;
    }

    public function findAll($sql)
    {
        $sth = $this->execSql($sql);
        while ($row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            yield $row;
        }
    }

    private function show($msg)
    {
        echo $msg, PHP_EOL;
    }

    //分销商 需清理的ids
    public function getDistributor()
    {
        $file = __DIR__ . "/cleanData/distributorClean.log";
        $data = file($file);
        $ids = [];
        foreach ($data as $v) {
            $ids[] = trim(explode("\t", $v)[0]);
        }
        return $ids;
    }

    //供应商 需保留的ids
    public function getSupplier()
    {
        $file = __DIR__ . "/cleanData/supplierClean.log";
        $data = file($file);
        $ids = [];
        foreach ($data as $v) {
            $ids[] = trim(explode("\t", $v)[0]);
        }
        return $ids;
    }

    /**
     * @param $yield
     * @param $process
     * @return string
     */
    private function processHandler($yield, $process, $pk = 'id')
    {
        $idStr = '';
        $i = 0;
        foreach ($yield as $v) {
            if ($i >= self::MAX_PROCESS) {
                if (empty($idStr)) {
                    echo "当前 idStr为空, i:{$i}", PHP_EOL;
                    return;
                }
                call_user_func_array($process, [$idStr]);
                $i = 0;
                $idStr = '';
            }
            $i++;
            $idStr .= $idStr === '' ? "{$v[$pk]}" : ",{$v[$pk]}";
        }
        if (empty($idStr)) {
            return;
        }
        call_user_func_array($process, [$idStr]);
    }
}

new Crontab_CleanData;