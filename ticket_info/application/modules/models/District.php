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

    public function getListByDistrict($district_id){
        $province_id = intval($district_id/10000) * 10000;
        $city_id = intval($district_id/100) * 100;
        $ids = array();
        $ids[$province_id] = $province_id;
        $ids[$city_id] = $city_id;
        $ids[$district_id] = $district_id;
        $data = $this->getByIds($ids);
        if ($data) {
            $data[0] = $data[$province_id];
            $data[1] = $data[$city_id];
            $data[2] = $data[$district_id];
        }
        return $data;
    }
}
