<?php
class CreditlogModel extends Base_Model_Abstract
{
   	protected $dbname = 'itourism';
    protected $tblname = 'credit_log';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|creditlogModel|';
    
    public function getTable() {
        return $this->tblname;
    }
}