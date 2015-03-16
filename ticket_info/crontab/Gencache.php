<?php

require dirname(__FILE__) . '/Base.php';

class Crontab_Gencache extends Process_Base 
{
    public function run() {
        $argv = $_SERVER['argv'];
        $gen = new Util_Gencache();
        $gen->create($argv[1]);
    }
}

$test = new Crontab_Gencache;
