<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-14
 * Time: 上午11:38
 */


Class LandscapeController extends Base_Controller_Api {

    /**
    * 获取景区列表
    */
    public function  listsAction(){
        Tools::safePostVars();
        $current = intval($_POST['current']); //当前页
        $items = intval($_POST['items']); //每页记录数
        if($current<=0) $current=1;
        if($items<=0) $items=15;

        $fields = "*"; //初始值
        $order = "created_at desc"; //初始值 也可array('created_at'=>'desc')
        $limit = ($items*($current-1)).",".$items; //初始值 也可array( ($items*($current-1)) ,$items)
        $where = array('status'=>'normal');
        $organization_id = intval($_POST['organization_id']);
        if($organization_id>0)
            $where['organization_id'] = $organization_id; //按机构查找

        $district_id = intval($_POST['district_id']);
        if($district_id>0)
            $where['district_id'] = $district_id; //按地区查找

        $ids = $_POST['ids'];
        if(!is_array($ids) && preg_match("/^[\d,]+$/",$ids))
            $ids = explode(',',$ids);
        if($ids)
            $where['id|IN'] = $ids; //按地区查找

        $searchBy = trim($_POST['search_by']); //要搜索的字段
        $keyword = trim($_POST['keyword']);
        if($searchBy && !in_array($searchBy,array('name','address','exaddress')))
            Lang_Msg::error("ERROR_SEARCH_1"); //该属性不支持搜索
        //elseif($searchBy && !$keyword)
        //    Lang_Msg::error("ERROR_SEARCH_2"); //缺少搜索关键词
        elseif($searchBy && $keyword)
            $where[$searchBy.'|EXP'] = "LIKE '%{$keyword}%'";

        $count = LandscapeModel::model()->get($where,"count(*) as count");
        $data = $count['count']>0  ? LandscapeModel::model()->select($where,$fields,$order,$limit):array();

        if($data){
            //列表数据加上供应商名称、景区等级、地区信息
            $landscapeLevelNames = $landscapeLevelIds = $districtNames = $districtIds = array();
            //$organizationNames =  $organizationIds = array();
            foreach($data as $v){
                if($v['landscape_level_id']>0)
                    array_push($landscapeLevelIds,$v['landscape_level_id']);
                array_push($districtIds,$v['district_id']);
                if($v['organization_id']>0)
                    array_push($organizationIds,$v['organization_id']);
            }
            $landscapeLevelIds = array_unique($landscapeLevelIds);
            $landscapeLevels = LandscapeLevelModel::model()->getByIds($landscapeLevelIds);
            foreach($landscapeLevels as $v){
                $landscapeLevelNames[$v['id']]=$v['name'];
            }
            foreach($data as $k=>$v){
                $data[$k]['landscape_level_name']= isset($landscapeLevelNames[$v['landscape_level_id']]) ? $landscapeLevelNames[$v['landscape_level_id']] : '';
            }
            unset($landscapeLevelNames,$landscapeLevelIds,$landscapeLevels);

            $districtIds = array_unique($districtIds);
            $districts = DistrictModel::model()->getListByIds($districtIds);
            foreach($districts as $v){
                $districtNames[$v['id']]=$v['level']==3? $v['parent_name']:$v['name'];
            }
            foreach($data as $k=>$v){
                $data[$k]['district_name']= isset($districtNames[$v['district_id']]) ? $districtNames[$v['district_id']] : '';
            }
            unset($districtNames,$districtIds,$districts);

            //$organizationIds = array_unique($organizationIds);
            //$organizations = OrganizationModel::model()->getListByIds($organizationIds);
            //foreach($organizations as $v){
            //    $organizationNames[$v['id']]=$v['name'];
            //}
        }

        $result = array(
            'data'=>$data,
            'pagination'=>array(
                'count'=>$count['count'],
                'current'=>$current,
                'items'=>$items,
                'total'=>ceil($count['count']/$items),
            )
        );
        Lang_Msg::output($result);
    }

    public function detail(){
        Tools::safePostVars();
        $id = intval($_POST['id']);
        if(!$id)
            Lang_Msg::error("ERROR_ID_1");

    }

    public function update(){

    }


}