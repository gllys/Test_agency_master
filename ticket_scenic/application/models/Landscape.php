<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-15
 * Time: 上午9:40
 */

class LandscapeModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'landscapes';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|LandscapeModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function addNew($data){
        $data['status'] = 'unaudited';
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['on_shelf'] = 1;
        $r = $this->add($data);
        $data['id'] =  $this->getInsertId();
        return $r ? $data : false ;
    }

    //从POI系统同步单个景区数据
    public function syncInfo($id){
        $LandscapeModel = new LandscapeModel();
        $detail = $LandscapeModel->getById($id);
        if(!$detail){
            $value = LandscapeInfoModel::model()->getInfo($id);
            if(!$value) return false;
            $item = array(
               'id' => $id,
               'name' => $value['name'],
               'landscape_level_id' => $value['level'],
               'province_id' =>0,
               'city_id' => 0,
               'district_id' => $value['district_id'],
               'address' => $value['address'],
               'phone' => $value['telephone'],
               'status' => 1,
               'created_at' => $value['created_at'],
               'updated_at' => $value['updated_at'],
               'deleted_at' => $value['deleted_at'],
               'lat' => $value['latitude'],
               'lng' => $value['longitude'],
               'on_shelf' => 1,
            );
            $district_id = $value['district_id'];
            if ($district_id) {
                $districts = DistrictModel::model()->getListByDistrict($district_id);
                if($districts) {
                    $item['province_id'] = $districts[0]['id'];
                    $item['city_id'] = $districts[1]['id'];
                    $item['district_id'] = $district_id;
                }
            }
            $r = $LandscapeModel->add($item);
            if(!$r)
                return false;
        }
        return true;
    }

}
