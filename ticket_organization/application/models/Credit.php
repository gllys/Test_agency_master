<?php
class CreditModel extends Base_Model_Abstract
{
   	protected $dbname = 'itourism';
    protected $tblname = 'supply_agency';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|SupplyAgencyModel|';
//    protected $preCacheKey='';
    
    public function getTable() {
        return $this->tblname;
    }
}