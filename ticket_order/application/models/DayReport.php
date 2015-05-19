<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/24
 * Time: 15:24
 */

class DayReportModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'day_report';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|DayReportModel|';

    public function getTable() {
        return $this->tblname;
    }

}