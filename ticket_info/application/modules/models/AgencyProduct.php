<?php
/**
 * 分销商商品模型
 *
 * @Package Models
 * @Date 2015-3-12
 * @Author Joe
 */
class AgencyProductModel extends Base_Model_Abstract {
    protected $dbname = 'itourism';
    protected $tblname = 'agency_product';
    protected $pkKey = 'id';
    protected $preCacheKey = '';

    public function getTable() {
        return $this->tblname;
    }
}