<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/4/28
 * Time: 17:31
 */

class LandscapeOrganizationModel extends Base_Model_Abstract {
    protected $dbname = 'itourism';
    protected $tblname = 'landscape_organization';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|LandscapeOrganizationModel|';

    public function getTable() {
        return $this->tblname;
    }
}