<?php
/**
 * 景点流量统计模型
 *
 * @Package Model
 * @Date 2015-4-10
 * @Author Joe
 */
class PvModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'pv';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|PvModel|';
    public $redisCacheKey = 'PvModel_cache';
    
    public function getTable() {
        return $this->tblname;
    }
}