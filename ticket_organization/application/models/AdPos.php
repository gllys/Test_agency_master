<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/1/19
 * Time: 15:09
 */
class AdPosModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ad_pos';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|AdPosModel|';

    public function getTable() {
        return $this->tblname;
    }
}