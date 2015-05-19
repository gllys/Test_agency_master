<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/21
 * Time: 20:30
 */

class ActivityChargeLogModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'activity_charge_log';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|ActivityChargeLogModel|';

    public function getTable()
    {
        return $this->tblname;
    }
}