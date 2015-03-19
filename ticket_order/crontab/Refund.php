<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-19
 * Time: 下午11:49
 */

require dirname(__FILE__) . '/Base.php';

class Crontab_Refund extends Process_Base
{
    protected $limit = 100;
    protected $interval = 600; //循环间隔（秒）

    public function run()
    {
        while (true) {


        }

    }

}