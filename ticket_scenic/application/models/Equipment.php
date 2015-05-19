<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/8
 * Time: 11:34
 */

class EquipmentModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'equipment';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|equipmentModel|';
    public $equipmentdaynumCacheKey = 'EquipmentModel_cache';

    public function getTable() {
        return $this->tblname;
    }
}