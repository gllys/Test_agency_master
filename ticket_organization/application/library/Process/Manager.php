<?php

/**
 * 进程管理器
 * @author  mosen
 */
class Process_Manager extends Process_Base
{
    protected $list = array();
    protected $reboot = false;

    /**
     * [run description]
     * @return [type] [description]
     */
    public function run() {
        if (Process_System::exists($this->basePath . 'Manager.php')) {
            echo "exists...\n";
            exit();
        }
        try {
            $this->list = ProcessModel::model()->search(array('state'=>1));
            $this->stopWorker();
        } catch(Exception $e) {
            echo $e->getMessage() . "\n";
            exit();
        }
        while(true) {
            try {
                $this->check();
            } catch(Exception $e) {
                echo $e->getMessage() . "\n";
            }
            $this->sleep(1);
        }
    }
    
    /**
     * [stopWorker description]
     * @return [type] [description]
     */
    public function stopWorker() {
        if ($this->list) {
            foreach ($this->list as $item) {
                $worker = $this->basePath . $item['path'];
                echo '['.date('Y-m-d H:i:s')."] stop...{$worker}...";
                if (!Process_System::stop($worker)) {
                    throw new Exception("stop {$worker} error...");
                } 
                echo "ok\n";
            }
        }
    }
    
    /**
     * [check description]
     * @return [type] [description]
     */
    public function check() {
        if ($this->list) {
            foreach ($this->list as $key => $item) {
                if (!$item['state']) {
                    unset($this->list[$key]);
                    continue;
                }
                $worker = $this->basePath . $item['path'];
                $num = $item['num'] ? $item['num'] : $this->cpu;
                $current = Process_System::getProcessNum($worker);
                $num-= $current;
                if ($num > 0) {
                    $log = $this->logPath . str_replace('/', '__', $item['path']) . '_' . date('Ymd');
                    echo '['.date('Y-m-d H:i:s')."] start...{$worker} > {$log}...";
                    if (Process_System::start($this->bin, $worker, $num, $log, '')) {
                        echo "ok\n";
                    } else {
                        echo "fail\n";
                    }
                }
            }
        }
    }
}