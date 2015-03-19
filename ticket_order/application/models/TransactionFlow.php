<?php
class TransactionFlowModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'transaction_flow';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|TransactionFlowModel|';

    public function getTable() {
        return $this->tblname;
    }
}