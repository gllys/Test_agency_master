<?php
require dirname(__FILE__) . '/Base.php';

class Crontab_Worker1 extends Process_Base 
{
    public function run() {
        while (true) {
        	$used = memory_get_usage();
        	echo "[".date('Y-m-d H:i:s')."]...";
        	$this->showOrgList();
        	$now = memory_get_usage();
        	$diff = $now - $used;
        	echo "memory_get_usage...{$diff}...\n";
            $this->sleep(5);
        }
    }

    public function showOrgList() {
    	$list = OrganizationModel::model()->search(array('deleted_at|exp'=>'is null'),'id');
    	// $used = memory_get_usage();
    	// echo "memory_get_usage...{$used}...\n";
    }
}

$test = new Crontab_Worker1;
