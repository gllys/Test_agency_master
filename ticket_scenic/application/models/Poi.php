<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-16
 * Time: 上午11:58
 */

class PoiModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'poi';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|PoiModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addNew($data){
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['status'] = (isset($data['status']) && !$data['status']) ? 0 : 1;
        $r = $this->add($data);
        $data['id'] =  $this->getInsertId();
        return $r ? $data : false ;
    }
}
