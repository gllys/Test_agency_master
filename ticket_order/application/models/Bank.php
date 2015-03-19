<?php

/**
 * Class RefundApplyModel
 */
class BankModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'banks';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|BankModel|';

    public function getTable() {
        return $this->tblname;
    }

}

