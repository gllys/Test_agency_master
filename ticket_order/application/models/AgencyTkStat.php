<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-20
 * Time: 下午7:59
 */

class AgencyTkStatModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'agency_tk_stat';
    protected $pkKey = '';
    protected $preCacheKey = 'cache|AgencyTkStatModel|';

    public function getTable() {
        return $this->tblname;
    }

}

