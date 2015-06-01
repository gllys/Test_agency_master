<?php

/**
 * Class RefundApplyModel
 */
class ChannelModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'channel';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|ChannelModel|';

    public function getTable() {
        return $this->tblname;
    }

}

