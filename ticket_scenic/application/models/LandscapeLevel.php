<?php
/**
 * Created by PhpStorm.
 * User: liuyong
 * Date: 14-10-15
 * Time: 上午9:41
 */


class LandscapeLevelModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'landscape_levels';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|LandscapeLevelModel|';

    public function getTable() {
        return $this->tblname;
    }
}
