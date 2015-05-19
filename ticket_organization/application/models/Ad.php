<?php

/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/19
 * Time: 15:10
 */
class AdModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ad';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|AdModel|';

    public function getTable()
    {
        return $this->tblname;
    }
}