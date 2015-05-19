<?php
/**
 * Created by PhpStorm.
 * User: liuyong
 * Date: 14-10-18
 * Time: 上午9:31
 * 景区和供应商关联模型
 */

class LandOrgModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'landscape_organization';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|LandOrgModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addNew($data){
        $data['created_at'] = date("Y-m-d H:i:s");
        $r = $this->add($data);
        $data['id'] =  $this->getInsertId();
        return $r ? $data : false ;
    }
}
