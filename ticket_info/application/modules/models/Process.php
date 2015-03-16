<?php

/**
 * Class ProcessModel
 */
class ProcessModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'process_config';
    protected $pkKey = 'id';
    protected $preCacheKey = '';

    public function getTable()
    {
        return $this->tblname;
    }
}
