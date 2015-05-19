<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
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

    public function getAll($fields="*"){
        $ret = Cache_Memcache::factory()->get('all_landscapeLevel');
        if($ret) {
            $ret = $this->search(array(),$fields);
            Cache_Memcache::factory()->set('all_landscapeLevel',$ret,3600);
        }
        return $ret;
    }
}
