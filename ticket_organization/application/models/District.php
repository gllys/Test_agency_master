<?php

/**
 * Class DistrictModel
 */
class DistrictModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'districts_config';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|DistrictModel|';
    
    public function getTable() {
        return $this->tblname;
    }
}
