<?php
require dirname(__FILE__) . '/Base.php';

class Crontab_Worker1 extends Process_Base 
{
    public function run() {
        while (true) {
        	echo "i am running..\n"
            $this->sleep(5);
        }
    }
}

$test = new Crontab_Worker1;
