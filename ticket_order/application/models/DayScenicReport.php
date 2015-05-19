<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/24
 * Time: 15:24
 */

class DayScenicReportModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'day_scenic_report';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|DayScenicReport|';

    public function getTable() {
        return $this->tblname;
    }

}