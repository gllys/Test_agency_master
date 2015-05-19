<?php

/**
 * Class BillitemModel
 */
class BillitemModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'bills_items';
    protected $basename = 'bills_items';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|BillitemModel|';
    protected $autoShare = 0;

    public function getTable() {
        return $this->tblname;
    }

    public function share($ts = 0) {
    	// if (!$ts) $ts = time();
    	// $this->tblname = $this->basename . date('Ym', $ts);
    	return $this;
    }

    public function shareById($id) {
    	// $this->tblname = $this->basename . Util_Common::payid2date($id);
    	return $this;
    }
}
