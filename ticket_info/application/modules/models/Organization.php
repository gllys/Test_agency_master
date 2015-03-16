<?php

/**
 * Class OrganizationModel
 */
class OrganizationModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'organizations';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|OrganizationModel|';
    
    public function getTable() {
        return $this->tblname;
    }
}
