<?php
/**
 * Process基类
 * @author  mosen
 */

abstract class Process_Base 
{
    protected $params = array();
    protected $bin;
    protected $logPath;
    protected $basePath;
    protected $cpu = 1;
    protected $date;
    protected $reboot = true;

    /**
     * [__construct description]
     */
    public function __construct() {
        Yaf_Application::app()->bootstrap();
        $this->init();
        $this->start();
    }

    protected function init() {
        $conf = Yaf_Registry::get("config");
        $this->bin = $conf['crontab']['bin'];
        $this->logPath = $conf['crontab']['log_path'];
        $this->params = $_SERVER['argv'];
        $this->cpu = Process_System::getCpu();
        $this->basePath = APPLICATION_PATH . '/crontab/';
        $this->date = date("Ymd");
    }
    
    /**
     * [start description]
     * @return [type] [description]
     */
    public function start() {
        Yaf_Application::app()->execute(array($this, 'run'));
    }

    public function sleep($cd) {
        if ($this->reboot && $this->date != date("Ymd")) {
            exit("reboot\n");
        }
        sleep($cd);
    }

    /**
     * [run description]
     * @return [type] [description]
     */
    abstract public function run();
}
