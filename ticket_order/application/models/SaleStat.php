<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 15-04-29
 * Time: 下午01:01
 * 销售统计模型，最小保存单位：日
 */

class SaleStatModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'sale_stat';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|SaleStatModel|';

    public function getTable() {
        return $this->tblname;
    }

}

