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

    public function getListByDistrict($district_id){  //按地区id获取省、市、区名称及id
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

    public function getDistrictsByIds($districtIds){ //批量按地区id获取省、市、区名称及id
        $ids = $districts= array();
        foreach($districtIds as $district_id){
            $districts[$district_id]['province_id'] = intval($district_id/10000) * 10000;
            $districts[$district_id]['city_id'] = intval($district_id/100) * 100;
            $districts[$district_id]['district_id'] = $district_id;
            $ids = array_merge($ids,array_values($districts[$district_id]));
        }
        $data = $this->getByIds($ids);
        $arrIds = array_keys($data);
        foreach($districts as $id=>$v) {
            in_array($v['province_id'],$arrIds) && $districts[$id]['province_name']= $data[$v['province_id']]['name'];
            in_array($v['city_id'],$arrIds) && $districts[$id]['city_name']= $data[$v['city_id']]['name'];
            in_array($v['district_id'],$arrIds) && $districts[$id]['district_name']= $data[$v['district_id']]['name'];
        }
        return $districts;
    }
}
