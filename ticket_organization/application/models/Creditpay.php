<?php
class CreditpayModel extends Base_Model_Abstract
{
   	protected $dbname = 'itourism';
    protected $tblname = 'credit_pay';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|creditPayModel|';
    
    public function getTable() {
        return $this->tblname;
    }
}