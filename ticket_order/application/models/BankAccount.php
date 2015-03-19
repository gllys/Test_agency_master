<?php

/**
 * Class RefundApplyModel
 */
class BankAccountModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'bankcard_account';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|BankAccountModel|';

    public function getTable() {
        return $this->tblname;
    }

}

