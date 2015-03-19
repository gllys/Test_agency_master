<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-15
 * Time: 下午2:36
 */


class DistrictModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'districts_config';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|DistrictModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function getListByIds($ids){
        $data = $this->getByIds($ids);
        $parentIds = $parentNames = array();
        foreach($data as $v){
            array_push($parentIds,$v['parent_id']);
        }
        $parents = $this->getByIds($parentIds);
        foreach ($parents as $pv) {
            $parentNames[$pv['id']] = $pv['name'];
        }
        foreach($data as $k=>$v){
            $data[$k]['parent_name'] = isset($parentNames[$v['parent_id']]) ? $parentNames[$v['parent_id']]: '';
        }
        return $data;
    }
}
