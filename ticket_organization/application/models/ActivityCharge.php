<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/19
 * Time: 18:17
 */
class ActivityChargeModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'activity_charge';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|ActivityChargeModel|';

    public function getTable()
    {
        return $this->tblname;
    }
}