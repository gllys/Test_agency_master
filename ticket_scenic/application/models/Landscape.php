<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-15
 * Time: 上午9:40
 */

class LandscapeModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'landscapes';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|LandscapeModel|';

    public function getTable() {
        return $this->tblname;
    }
}
