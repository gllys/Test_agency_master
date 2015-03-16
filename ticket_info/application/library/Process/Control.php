<?php
/**
 * 进程启动
 * @author  mosen
 */
class Process_Control extends Process_Base
{
    protected $tryLimit = 2;

    /**
     * [run description]
     * @return [type] [description]
     */
    public function run() {
        $this->stopManager();
        $this->startManager();
    }
    
    /**
     * [stopManager description]
     * @return [type] [description]
     */
    public function stopManager() {
        // 停止主控进程
        $manager = $this->basePath . 'Manager.php';
        $num = Process_System::getProcessNum($manager);
        if ($num > 0) {
            echo '['.date('Y-m-d H:i:s')."] stop...{$manager}...";
            while (true) {
                if (Process_System::stop($manager)) {
                    echo "ok\n";
                    break;
                }
                echo '.';
                sleep(5);
            }
        }
    }
    
    /**
     * [startManager description]
     * @return [type] [description]
     */
    public function startManager() {
        $try = 0;
        $manager = $this->basePath . 'Manager.php';
        $log = $this->logPath . 'manager.log';
        echo '['.date('Y-m-d H:i:s')."] start...{$manager} > {$log}...";
        while ($try < $this->tryLimit) {
            $try++;
            $status = Process_System::startByRoot($this->bin, $manager, 1, $log);
            if ($status) {
                echo "ok\n";
                break;
            }
            echo '.';
        }
    }
}