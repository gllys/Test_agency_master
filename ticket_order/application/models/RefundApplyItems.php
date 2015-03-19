<?php

/**
 * Class RefundApplyModel
 */
class RefundApplyItemsModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'refund_apply_items';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|RefundApplyItemsModel|';

    public function getTable() {
        return $this->tblname;
    }

}

