<?php

/**
 * Class SupplyAgencyModel
 */
class SupplyAgencyHistoryModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'supply_agency_history';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|supply_agency_history|';

    public function getTable() {
        return $this->tblname;
    }
}
