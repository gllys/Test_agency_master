<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-14
 * Time: 上午11:38
 */


Class LandscapeController extends Base_Controller_Api {

    public function listsAction() {
        $params = array();
        $params['keyword'] = trim(Tools::safeOutput($this->body['keyword'])); //要搜索的字段
        $params['organization_id'] = intval($this->body['organization_id']);
        $params['province_ids'] = trim(Tools::safeOutput($this->body['province_ids']));
        $params['city_ids'] = trim(Tools::safeOutput($this->body['city_ids']));
        $params['district_ids'] = trim(Tools::safeOutput($this->body['district_ids']));
        $params['show_poi'] = trim(Tools::safeOutput($this->body['show_poi']));
        $params['show_poi_flag'] = trim(Tools::safeOutput($this->body['show_poi_flag']));
        $params['is_manage'] = intval($this->body['is_manage']);
        $params['ids'] = trim(Tools::safeOutput($this->body['ids']));
        $params['show_district_name']=intval($this->body['show_district_name']);
        !isset($this->body['show_district_name']) && $params['show_district_name']=1;

        $take_from_poi = 0; //是否先从运营POI获取数据，1是，0否
        if ($take_from_poi > 0) {
            $this->listsFromPOIAction($params);
        } else {
            $ids = $list = array();
            $where = array();

            if ($params['ids'] && preg_match("/[\d,]+/", $params['ids'])) {
                $ids = explode(',', $params['ids']);
            }

            if ($params['organization_id'] > 0) {
                if ($params['is_manage'] > 0) {
                    $where['organization_id'] = $params['organization_id'];
                } else {
                    $maps = LandOrgModel::model()->setListKey('landscape_id')->search(array('organization_id' => $params['organization_id']), 'id,landscape_id', null, null, 0);
                    if (!empty($maps)) {
                        $ids = empty($ids) ? array_keys($maps) : array_intersect($ids, array_keys($maps));
                    } else {
                        $result = array(
                            'data' => array(),
                            'pagination' => array('count' => 0, 'current' => 0, 'items' => $this->items, 'total' => 0)
                        );
                        Lang_Msg::output($result);
                    }
                }
            }

            if ($params['province_ids'] || $params['city_ids'] || $params['district_ids']) {
                preg_match("/[\d,]+/", $params['province_ids']) && $where['province_id|in'] = explode(',', $params['province_ids']);
                preg_match("/[\d,]+/", $params['city_ids']) && $where['city_id|in'] = explode(',', $params['city_ids']);
                preg_match("/[\d,]+/", $params['district_ids']) && $where['district_id|in'] = explode(',', $params['district_ids']);
            }
            !empty($ids) && $where['id|in'] = $ids;

            if(!empty($params['keyword'])) {
//                $where['name|like'] = array("%{$params['keyword']}%");
                $where['or'] = array(
                    'name|like' => array("%{$params['keyword']}%"),
                    'py|like' => array("{$params['keyword']}%"),
                    'pinyin|like' => array("{$params['keyword']}%"),
                );
            }

            if(!isset($this->body['parent_id']) ||!$this->body['parent_id']) $where['parent_id|exp'] = 'IS NULL';
            if(isset($this->body['deleted_at']) && $this->body['deleted_at']) $where['deleted_at|exp'] = 'IS NULL';
            // 是否绑定供应商
            if( isset($this->body['has_bind_org']) && in_array($this->body['has_bind_org'],array(0,1))){
                $where['has_bind_org'] = intval($this->body['has_bind_org']);
            }
            $LandscapeModel = new LandscapeModel();
            $this->count = $LandscapeModel->countResult($where);
            $this->pagenation();
            $data = $this->count > 0 ? $LandscapeModel->search($where, $this->getFields(), $this->getSortRule(), $this->limit) : array();
            if (!empty($data)) {
                $poiList = array();
                if ($params['show_poi'] > 0) {
                    $poiWhere = array();
                    $poiWhere['landscape_id|IN'] = array_keys($data);
                    if (!$params['show_poi_flag']) $poiWhere['status'] = 1;
                    $pois = PoiModel::model()->search($poiWhere, "id,name,landscape_id");
                    foreach ($pois as $pv) {
                        $poiList[$pv['landscape_id']][] = array('poi_id' => $pv['id'], 'poi_name' => $pv['name']);
                    }
                }

                // 是否已绑定供应商 @todo
                /*$list_LandscapeOrganization = array();
                $LandscapeOrganization = LandscapeOrganizationModel::model()->search('landscape_id in ('.implode(',',array_keys($data)).') group by landscape_id','id,landscape_id');
                foreach($LandscapeOrganization as $k=>$v){
                    $list_LandscapeOrganization[$v['landscape_id']] = $v['landscape_id'];
                }*/

                $landscape_levels = LandscapeLevelModel::model()->search(array(),'id,name');
                $districtIds = array();
                foreach ($data as $k => $v) {
                    $data[$k]['landscape_level_name'] = $landscape_levels[$v['landscape_level_id']]['name'];
                    if ($params['show_poi'] > 0) {
                        $data[$k]['poi_list'] = isset($poiList[$v['id']]) ? $poiList[$v['id']] : array();
                    }

                    if ($v['district_id']>0) {
                        $districtIds[] = $v['district_id'];
                    }

                    /*if(in_array($v['id'],$list_LandscapeOrganization)){
                        $data[$k]['has_bind_org'] = 1;
                    }else{
                        $data[$k]['has_bind_org'] = 0;
                    }*/
                }
                if($params['show_district_name']>0 && !empty($districtIds)) {
                    $districts = DistrictModel::model()->getDistrictsByIds($districtIds);
                    foreach ($data as $k => $v) {
                        if ($v['district_id']>0) {
                            if(!empty($districts[$v['district_id']])) {
                                $data[$k]['province_id'] = $districts[$v['district_id']]['province_id'];
                                $data[$k]['city_id'] = $districts[$v['district_id']]['city_id'];
                                $data[$k]['district'][] = $data[$k]['province_name'] = $districts[$v['district_id']]['province_name'];
                                $data[$k]['district'][] = $data[$k]['city_name'] = $districts[$v['district_id']]['city_name'];
                                $data[$k]['district'][] = $data[$k]['district_name'] = $districts[$v['district_id']]['district_name'];
                            }
                        }
                    }
                }
            }

            $result = array(
                'data' => $data,
                'pagination' => array(
                    'count' => $this->count,
                    'current' => $this->current,
                    'items' => $this->items,
                    'total' => $this->total,
                )
            );
            Lang_Msg::output($result);
        }
    }

    /**
    * 获取景区列表
    */
    private function  listsFromPOIAction($params) {
        $isLocal = $this->config['landscape_data']['use_local']; //数据是否以本地为准

        intval($this->body['current']) && $this->current = intval($this->body['current']);
        intval($this->body['items']) && $this->items = intval($this->body['items']);

        $ids = $list = array();
        if ($params['organization_id']>0) {
            if($params['is_manage']) {
                $maps = LandscapeModel::model()->search(array('organization_id' => $params['organization_id']));
            }
            else {
                $maps = LandOrgModel::model()->setListKey('landscape_id')->search(array('organization_id' => $params['organization_id']),'id,landscape_id',null,null,0);
            }
            if ($maps)
                $ids = array_keys($maps);
            else {
                $result = array(
                    'data'=>array(),
                    'pagination'=>array( 'count'=>0,'current'=>0,'items'=>$this->items,'total'=>0)
                );
                Lang_Msg::output($result);
            }
        }
        if ($params['province_ids'] || $params['city_ids'] || $params['district_ids']) {
            $where = array();
            $params['organization_id']>0 && $ids && $where['id|in'] = $ids;
            preg_match("/[\d,]+/",$params['province_ids']) && $where['province_id|in'] = explode(',', $params['province_ids']);
            preg_match("/[\d,]+/",$params['city_ids']) && $where['city_id|in'] = explode(',', $params['city_ids']);
            preg_match("/[\d,]+/",$params['district_ids']) && $where['district_id|in'] = explode(',', $params['district_ids']);
            $maps = LandscapeModel::model()->setCd(0)->search($where, 'id,province_id');
            if ($maps)
                $ids = array_keys($maps);
            else{
                $result = array(
                    'data'=>array(),
                    'pagination'=>array( 'count'=>0,'current'=>0,'items'=>$this->items,'total'=>0)
                );
                Lang_Msg::output($result);
            }
        }
        $showChild = false; //是否显示子景区
        if($params['ids'] && preg_match("/[\d,]+/",$params['ids'])){
            $ids = explode(',',$params['ids']);
            $showChild = true;
        }
        $showChild = intval($this->body['show_child'])?true:$showChild;

        $data = LandscapeInfoModel::model()->getList($params['keyword'], $ids, $this->current, $this->items,$showChild);

        if($data){
            $fields = $this->getFields();
            $fields = ($fields && $fields!='*') ? explode(',',$fields):array();

            $this->count = $data['pagination']['count'];
            $this->pagenation();
            $ids = $landscape_levels = array();
            foreach ($data['data'] as $value) {
                $ids[] = $value['id'];
            }
            $details = $ids ? LandscapeModel::model()->setCd(0)->search(array('id|in'=>$ids)) : array();
            $poiList = array();
            if($params['show_poi']){
                $poiWhere = array();
                $poiWhere['landscape_id|IN'] = $ids;
                if(!$params['show_poi_flag']) $poiWhere['status'] = 1;
                $pois = PoiModel::model()->search($poiWhere,"id,name,landscape_id");
                foreach($pois as $pv){
                    $poiList[$pv['landscape_id']][] = array('poi_id'=>$pv['id'],'poi_name'=>$pv['name']);
                }
            }
            $landscape_levels = LandscapeLevelModel::model()->search(array('id|>'=>0),"id,name");
            foreach ($data['data'] as $value) {
                $detail = $details && isset($details[$value['id']]) ? $details[$value['id']]: array();

                $district_id = $detail['district_id'] ? $detail['district_id'] : $value['district_id'];
                if ($district_id) {
                    $districts = DistrictModel::model()->getListByDistrict($district_id);
                    if($districts) {
                        $detail['province_id'] = $districts[0]['id'];
                        $detail['district'][] = $detail['province_name'] = $districts[0]['name'];
                        $detail['city_id'] = $districts[1]['id'];
                        $detail['district'][] = $detail['city_name'] = $districts[1]['name'];
                        $detail['district_id'] = $district_id;
                        $detail['district'][] = $detail['district_name'] = $districts[$district_id]['name'];
                    }
                }
                $landscape_level_id = $detail['landscape_level_id'] ? intval($detail['landscape_level_id']) : intval($value['landscape_level_id']);
                
                $item = array();
                $item['id'] = $value['id'];
                (!$fields || in_array('name',$fields)) && $item['name'] = $value['name'];
                (!$fields || in_array('landscape_level_id',$fields)) && $item['landscape_level_id'] = $isLocal?$landscape_level_id:$value['level'];
                (!$fields || in_array('landscape_level_name',$fields)) && $item['landscape_level_name'] = $landscape_levels[$landscape_level_id]['name'];
                (!$fields || in_array('province_id',$fields)) && $item['province_id'] = $detail['province_id'];
                (!$fields || in_array('city_id',$fields)) && $item['city_id'] = $detail['city_id'];
                (!$fields || in_array('district_id',$fields)) && $item['district_id'] = $detail['district_id'];
                (!$fields || in_array('district',$fields)) && $item['district'] = $detail['district'];
                (!$fields || in_array('address',$fields)) && $item['address'] = $isLocal?($detail['address']?$detail['address']:$value['address']):($value['address']?$value['address']:$detail['address']);
                (!$fields || in_array('thumbnail_id',$fields)) && $item['thumbnail_id'] = $detail['thumbnail_id']; //封面图
                (!$fields || in_array('thumbnail_img',$fields)) && $item['thumbnail_img'] = '';
                (!$fields || in_array('phone',$fields)) && $item['phone'] = $isLocal?($detail['phone']?$detail['phone']:$value['telephone']):($value['telephone']?$value['telephone']:$detail['phone']);
                (!$fields || in_array('hours',$fields)) && $item['hours'] = $detail['hours'];
                (!$fields || in_array('exaddress',$fields)) && $item['exaddress'] = $detail['exaddress']; //取票地址
                (!$fields || in_array('biography',$fields)) && $item['biography'] = $isLocal?($detail['biography']?$detail['biography']:$value['description']):($value['description'] ?$value['description']:$detail['biography']); //景区介绍
                (!$fields || in_array('note',$fields)) && $item['note'] = $detail['note']; //购票须知
                (!$fields || in_array('transit',$fields)) && $item['transit'] = $detail['transit'];
                (!$fields || in_array('status',$fields)) && $item['status'] = 'normal';
                (!$fields || in_array('organization_id',$fields)) && $item['organization_id'] = intval($detail['organization_id']);
                (!$fields || in_array('impower_id',$fields)) && $item['impower_id'] = $detail['impower_id'];
                (!$fields || in_array('created_at',$fields)) && $item['created_at'] = $value['created_at'];
                (!$fields || in_array('updated_at',$fields)) && $item['updated_at'] = $isLocal?($detail['updated_at']?$detail['updated_at']:$value['updated_at']):($value['updated_at'] ?$value['updated_at']:$detail['updated_at']);
                (!$fields || in_array('deleted_at',$fields)) && $item['deleted_at'] = $value['deleted_at'];
                (!$fields || in_array('lat',$fields)) && $item['lat'] = $value['latitude'];
                (!$fields || in_array('lng',$fields)) && $item['lng'] = $value['longitude'];
                (!$fields || in_array('on_shelf',$fields)) && $item['on_shelf'] = isset($detail['on_shelf'])?intval($detail['on_shelf']):1;

                (!$fields || in_array('province_name',$fields)) && $item['province_name'] = isset($detail['province_name'])?$detail['province_name']:'';
                (!$fields || in_array('city_name',$fields)) && $item['city_name'] = isset($detail['city_name'])?$detail['city_name']:'';
                (!$fields || in_array('district_name',$fields)) && $item['district_name'] = isset($detail['district_name'])?$detail['district_name']:'';

                if($params['show_poi']){
                    $item['poi_list'] = isset($poiList[$item['id']]) ? $poiList[$item['id']] : array();
                }

                $list[] = $item;
            }
        }

        $result = array(
            'data'=>$list,
            'pagination'=>array(
                'count'=>$this->count,
                'current'=>$this->current,
                'items'=>$this->items,
                'total'=>$this->total,
            )
        );
        Lang_Msg::output($result);
    }

    /**
     * 获取机构绑定的所有含子景点的景区列表
     */
    public function  byorgAction() {
        $keyword = trim(Tools::safeOutput($this->body['keyword'])); //要搜索的字段
        $organization_id = intval($this->body['organization_id']);
        !$organization_id && Lang_Msg::error('ERROR_LAND_ORG_4'); //缺少供应商ID参数

        $take_from_poi = 0; //是否先从运营POI获取数据，1是，0否

        $ids = $list = array();

        $maps = LandOrgModel::model()->setListKey('landscape_id')->search(array('organization_id' => $organization_id,'deleted_at|exp'=>'is null'),'id,landscape_id',null,null,0);
        if ($maps)
            $ids = array_keys($maps);
        else {
            Lang_Msg::output('');
        }

        $getIds = trim(Tools::safeOutput($this->body['ids']));
        if($getIds && preg_match("/[\d,]+/",$getIds))
            $ids = explode(',',$getIds);

        $where = $tmpIds = $tmp = array();
        if(!empty($keyword)) {
            $where['name|like'] = array("%{$keyword}%");
        }

        if($take_from_poi>0){ //从运营POI获取数据
            $data = LandscapeInfoModel::model()->getList($keyword, $ids, 1, count($ids));
            foreach ($data['data'] as $value) {
                $tmpIds[] = $value['id'];
                $tmp[$value['id']] = $value;
            }
        } else {
            $tmpIds = $ids;
        }
        $where['id|in'] = $tmpIds;

        $PoiModel = new PoiModel();
        $where['id|EXP'] = "IN (SELECT landscape_id FROM ".$PoiModel->getTable()." WHERE status=1)";

        $LandscapeModel = new LandscapeModel();
        $data = $LandscapeModel->setCd(0)->search($where,"id,name",null,null);
        foreach($data as $id=>$v){
            if($take_from_poi>0 && !empty($tmp[$id]['name'])) {
                $data[$id]['name'] = $tmp[$id]['name'];
            }
        }
        Lang_Msg::output(array_values($data));
    }

    public function detailAction(){
        $isLocal = $this->config['landscape_data']['use_local']; //数据是否以本地为准
        $id = intval($this->body['id']);
        !$id && Lang_Msg::error("ERROR_DETAIL_1");

        $take_from_poi = 0; //是否先从运营POI获取数据，1是，0否
        $value = array();
        if($take_from_poi>0){
            $value = LandscapeInfoModel::model()->getInfo($id);
            if (empty($value)) Lang_Msg::error("ERROR_DETAIL_2");
        }
        $noVal = empty($value) ? true : false; //是否没获取POI数据

        $detail = LandscapeModel::model()->setCd(0)->getById($id);
        if(empty($detail)){
            if($take_from_poi>0) $detail = array();
            else Lang_Msg::error("ERROR_DETAIL_2");
        }
        $maps = LandOrgModel::model()->setListKey('organization_id')->search(array('landscape_id'=>$id), 'id,organization_id', null, null, 0);
        $landscape_organization = $maps ? array_keys($maps) : array();
        $imageinfo = $detail['thumbnail_id'] ? LandscapeImageModel::model()->getById($detail['thumbnail_id']) : array();
        $images = LandscapeImageModel::model()->search(array('landscape_id'=>$id));
        sort($images);

        $district_id = ($detail['district_id']>0 || $noVal===true) ? $detail['district_id'] : $value['district_id'];
        if ($district_id>0) {
            $districts = DistrictModel::model()->getListByDistrict($district_id);
            if($districts) {
                $detail['province_id'] = $districts[0]['id'];
                $detail['province_name'] = $districts[0]['name'];
                $detail['city_id'] = $districts[1]['id'];
                $detail['city_name'] = $districts[1]['name'];
                $detail['district_id'] = $district_id;
                $detail['district_name'] = $districts[$district_id]['name'];
            }
        }
        
        $result['id'] = $noVal===true ? $detail['id'] : $value['id'];
        $result['name'] = $noVal===true ? $detail['name'] : $value['name'];
        $result['landscape_level_id'] = ($isLocal || $noVal===true)?$detail['landscape_level_id']:$value['level'];
        $result['province_id'] = $detail['province_id'];
        $result['city_id'] = $detail['city_id'];
        $result['district_id'] = $district_id;
        $result['address'] = $isLocal ?
            (($detail['address']!='' || $noVal===true) ? $detail['address']:$value['address']) :
            (($noVal===false && $value['address']!='')?$value['address']:$detail['address']);
        $result['thumbnail_id'] = $detail['thumbnail_id']; //封面图
        $result['thumbnail_img'] = $imageinfo['url'];
        $result['phone'] = $isLocal ?
            (($detail['phone']!='' || $noVal===true) ? $detail['phone'] : $value['telephone']) :
            (($noVal===false && $value['telephone']!='') ? $value['telephone'] : $detail['phone']);
        $result['hours'] = $detail['hours'];
        $result['exaddress'] = $detail['exaddress']; //取票地址
        $result['biography'] = $isLocal ?
            (($detail['biography']!='' || $noVal===true) ? $detail['biography'] : $value['description']) :
            (($noVal===false && $value['description']!='') ?$value['description']:$detail['biography']); //景区介绍
        $result['note'] = $detail['note']; //购票须知
        $result['transit'] = $detail['transit']; //交通指南
        $result['status'] = 1;
        $result['organization_id'] = intval($detail['organization_id']);
        $result['impower_id'] = $detail['impower_id']; //授权书
        $result['created_at'] = $noVal===true ? $detail['created_at'] : $value['created_at'];
        $result['updated_at'] = $isLocal ?
            (($detail['updated_at']>0 || $noVal===true) ? $detail['updated_at'] : $value['updated_at']) :
            (($noVal===false && $value['updated_at']>0) ? $value['updated_at']: $detail['updated_at']);
        $result['deleted_at'] = $noVal===true ? $detail['deleted_at'] : $value['deleted_at'];
        $result['lat'] = $noVal===true ? $detail['lat'] : $value['latitude'];
        $result['lng'] = $noVal===true ? $detail['lng'] : $value['longitude'];
        $result['on_shelf'] = intval($detail['on_shelf']);
        $result['images'] = $images;
        $result['landscape_organization'] = $landscape_organization;

        $areaIds = array();
        $result['province_id'] && $areaIds[] = $result['province_id'];
        $result['city_id'] && $areaIds[] = $result['city_id'];
        $result['district_id'] && $areaIds[] = $result['district_id'];
        $result['province_name'] = $result['city_name'] = $result['district_name'] = '';
        if ($areaIds) {
            $areaIds = array_unique($areaIds);
            $areas = DistrictModel::model()->getByIds($areaIds);
            $areas[$result['province_id']] && $result['province_name'] = $areas[$result['province_id']]['name'];
            $areas[$result['city_id']] && $result['city_name'] = $areas[$result['city_id']]['name'];
            $areas[$result['district_id']] && $result['district_name'] = $areas[$result['district_id']]['name'];
        }

        $levelinfo = $result['landscape_level_id'] ? LandscapeLevelModel::model()->getById($result['landscape_level_id']) : array();
        $result['landscape_level_name'] = $levelinfo['name'];

        Lang_Msg::output($result);
    }

    public function addAction(){
        $operator = $this->getOperator(); //获取操作者

        $data = array();
        $data['name'] = trim(Tools::safeOutput($this->body['name']));
        $data['landscape_level_id'] = intval($this->body['landscape_level_id']);
        $data['province_id'] = intval($this->body['province_id']);
        $data['city_id'] = intval($this->body['city_id']);
        $data['district_id'] = intval($this->body['district_id']);
        $data['address'] = trim(Tools::safeOutput($this->body['address']));
        $data['thumbnail_id'] = intval($this->body['thumbnail_id']);
        $data['phone'] = trim(Tools::safeOutput($this->body['phone']));
        $data['hours'] = trim(Tools::safeOutput($this->body['hours'])); //开放时间
        $data['exaddress'] = trim(Tools::safeOutput($this->body['exaddress'])); //取票地址
        $data['biography'] = trim(Tools::safeOutput($this->body['biography'])); //景区介绍
        $data['note'] = trim(Tools::safeOutput($this->body['note'])); //购票须知
        $data['transit'] = trim(Tools::safeOutput($this->body['transit'])); //交通指南
        $data['organization_id'] = intval($this->body['organization_id']);
        $data['impower_id'] = intval($this->body['impower_id']);
        $data['lat'] = trim(Tools::safeOutput($this->body['lat'])); //经度
        $data['lng'] = trim(Tools::safeOutput($this->body['lng'])); //维度
        $data['api_channel_id'] = intval($this->body['api_channel_id']); //对接渠道编号
        $data['created_by'] = $operator['user_id']; //操作者uid

        !$data['name'] &&  Lang_Msg::error("ERROR_LANDSCAPE_2"); //景区名称不能为空
        !$data['landscape_level_id']  &&  Lang_Msg::error("ERROR_LANDSCAPE_3"); //请选择景区级别
        !$data['province_id'] &&  Lang_Msg::error("ERROR_DISTRICT_1");
        !$data['hours'] &&  Lang_Msg::error("ERROR_HOURS_1");
        !$data['phone'] &&  Lang_Msg::error("ERROR_PHONE_1");
        !$data['address'] &&  Lang_Msg::error("ERROR_ADDRESS_1");
        !$data['exaddress'] &&  Lang_Msg::error("ERROR_EXADDRESS_1");
        !$data['biography'] &&  Lang_Msg::error("ERROR_BIOGRAPHY_1");
        !$data['note'] &&  Lang_Msg::error("ERROR_NOTE_1");
        !$data['transit'] &&  Lang_Msg::error("ERROR_TRANSIT_1");

        $LandscapeModel = new LandscapeModel();
        $has = $LandscapeModel->search(array('name'=>$data['name']));
        $has && Lang_Msg::error('ERROR_LANDSCAPE_4'); //已存在此名称的景区，景区名称不能重复
        $LandscapeModel->begin();
        $r = $LandscapeModel->addNew($data);
        if($r){
            $LandscapeModel->commit();
            Yaf_Application::app()->getDispatcher()->getRequest()->setParam('landscape_id',$r['id']);
            Log_Landscape::model()->add(array('type'=>1,'num'=>1,'content'=>Lang_Msg::getLang('INFO_LANDSCAPE_1').'【'.$data['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_ADD_0'), $r);
        }
        else{
            $LandscapeModel->rollback();
            Lang_Msg::error("ERROR_ADD_1");
        }
    }

    public function updateAction(){
        $isLocal = $this->config['landscape_data']['use_local']; //数据是否以本地为准
        $operator = $this->getOperator(); //获取操作者

        $id = intval($this->body['id']);
        !$id && Lang_Msg::error("ERROR_LANDSCAPE_1"); //缺少景区ID参数

        $take_from_poi = 0; //是否先从运营POI获取数据，1是，0否
        $value = array();
        if($take_from_poi>0){
            $value = LandscapeInfoModel::model()->getInfo($id);
            if (empty($value)) Lang_Msg::error("ERROR_DETAIL_2");
        }
        $noVal = empty($value) ? true : false; //是否没获取POI数据

        $LandscapeModel = new LandscapeModel();
        $detail = $LandscapeModel->getById($id);
        $needAdd = false;
        if(empty($detail)){
            if($take_from_poi>0) $detail = array();
            else Lang_Msg::error("ERROR_DETAIL_2");
            $needAdd=true;
        }

        $district_id = ($detail['district_id']>0 || $noVal===true) ? $detail['district_id'] : $value['district_id'];
        if ($district_id>0) {
            $districts = DistrictModel::model()->getListByDistrict($district_id);
            if($districts) {
                $detail['province_id'] = $districts[0]['id'];
                $detail['city_id'] = $districts[1]['id'];
                $detail['district_id'] = $district_id;
            }
        }

        $data = $detail;
        $data['name'] = $noVal===true ? $detail['name'] : $value['name'];
        $data['landscape_level_id'] = ($isLocal || $noVal===true)?$detail['landscape_level_id']:$value['level'];
        $data['province_id'] = isset($detail['province_id'])?$detail['province_id']:0;
        $data['city_id'] = isset($detail['city_id'])?$detail['city_id']:0;
        $data['district_id'] = (isset($detail['district_id']) || $noVal===true) ? $detail['district_id'] : $value['district_id'];
        $data['address'] = $isLocal ?
            (($detail['address'] || $noVal===true) ? $detail['address'] : $value['address']) :
            (($noVal===false && $value['address']) ? $value['address'] : $detail['address']);
        $data['thumbnail_id'] = isset($detail['thumbnail_id'])?$detail['thumbnail_id']:0; //封面图
        $data['phone'] = $isLocal ?
            (($detail['phone'] || $noVal===true) ? $detail['phone'] : $value['telephone']) :
            (($noVal===false && $value['telephone']!='') ? $value['telephone'] : $detail['phone']);
        $data['hours'] = isset($detail['hours'])?$detail['hours']:'';
        $data['exaddress'] = isset($detail['exaddress'])?$detail['exaddress']:''; //取票地址
        $data['biography'] = $isLocal ?
            (($detail['biography'] || $noVal===true) ? $detail['biography'] : $value['description']) :
            (($noVal===false && $value['description']!='') ?$value['description']:$detail['biography']); //景区介绍

        $data['note'] = isset($detail['note'])?$detail['note']:''; //购票须知
        $data['transit'] = isset($detail['transit'])?$detail['transit']:''; //交通指南
        $data['status'] = 1;
        $data['organization_id'] = isset($detail['organization_id'])?intval($detail['organization_id']):0;
        $data['impower_id'] = isset($detail['impower_id'])?$detail['impower_id']:0; //授权书
        $data['created_at'] = $noVal===true ? $detail['created_at'] : $value['created_at'];
        $data['updated_at'] = $isLocal ?
            (($detail['updated_at']>0 || $noVal===true) ? $detail['updated_at'] : $value['updated_at']) :
            (($noVal===false && $value['updated_at']>0) ? $value['updated_at']: $detail['updated_at']);
        $data['deleted_at'] = $noVal===true ? $detail['deleted_at'] : $value['deleted_at'];
        $data['lat'] = $noVal===true ? $detail['lat'] : $value['latitude'];
        $data['lng'] = $noVal===true ? $detail['lng'] : $value['longitude'];

        $data['on_shelf'] = isset($detail['on_shelf'])?intval($detail['on_shelf']):1;

        $now = date("Y-m-d H:i:s");

        //$name = trim(Tools::safeOutput($this->body['name']));
        $landscape_level_id = intval($this->body['landscape_level_id']);
        $province_id = intval($this->body['province_id']);
        $city_id = intval($this->body['city_id']);
        $district_id = intval($this->body['district_id']);
        $address = trim(Tools::safeOutput($this->body['address']));
        $thumbnail_id = intval($this->body['thumbnail_id']);
        $phone = trim(Tools::safeOutput($this->body['phone']));
        $hours = trim(Tools::safeOutput($this->body['hours'])); //开放时间
        $exaddress = trim(Tools::safeOutput($this->body['exaddress'])); //取票地址
        $biography = trim(Tools::safeOutput($this->body['biography'])); //景区介绍
        $note = trim(Tools::safeOutput($this->body['note'])); //购票须知
        $transit = trim(Tools::safeOutput($this->body['transit'])); //交通指南
        $status = trim(Tools::safeOutput($this->body['status']));
        $organization_id = intval($this->body['organization_id']);
        $impower_id = intval($this->body['impower_id']);
        $lat = trim(Tools::safeOutput($this->body['lat'])); //经度
        $lng = trim(Tools::safeOutput($this->body['lng'])); //维度
        $api_channel_id = intval($this->body['api_channel_id']); //对接渠道编号
        $on_shelf = intval($this->body['on_shelf']);
        $deleted = intval($this->body['deleted']);

        //isset($_POST['name']) && !$name &&  Lang_Msg::error("ERROR_LANDSCAPE_2"); //景区名称不能为空
        //isset($_POST['landscape_level_id']) && !$landscape_level_id &&  Lang_Msg::error("ERROR_LANDSCAPE_3"); //请选择景区级别
        isset($_POST['province_id']) && !$province_id &&  Lang_Msg::error("ERROR_DISTRICT_1");
        isset($_POST['hours']) && !$hours &&  Lang_Msg::error("ERROR_HOURS_1");
        isset($_POST['exaddress']) && !$exaddress &&  Lang_Msg::error("ERROR_EXADDRESS_1");
        isset($_POST['note']) && !$note &&  Lang_Msg::error("ERROR_NOTE_1");
        isset($_POST['transit']) && !$transit &&  Lang_Msg::error("ERROR_TRANSIT_1");

        isset($_POST['phone']) && empty($phone) && empty($value['telephone']) && Lang_Msg::error("ERROR_PHONE_1");
        isset($_POST['address']) && empty($address) && empty($value['address']) &&  Lang_Msg::error("ERROR_ADDRESS_1");
        isset($_POST['biography']) && empty($biography) && empty($value['description']) &&  Lang_Msg::error("ERROR_BIOGRAPHY_1");

        //isset($_POST['name']) && $data['name']= $name;
        isset($_POST['province_id']) && $data['province_id']= $province_id;
        isset($_POST['city_id']) && $data['city_id']= $city_id;
        isset($_POST['district_id']) && $data['district_id']= $district_id;
        $thumbnail_id && $data['thumbnail_id']= $thumbnail_id;
        isset($_POST['hours']) && $data['hours']= $hours;
        isset($_POST['exaddress']) && $data['exaddress']= $exaddress;
        isset($_POST['note']) && $data['note']= $note;
        isset($_POST['transit']) && $data['transit']= $transit;
        isset($_POST['organization_id']) && $data['organization_id']= $organization_id;
        isset($_POST['impower_id']) && $data['impower_id']= $impower_id; //授权书
        isset($_POST['lat']) && $data['lat'] = $lat;
        isset($_POST['lng']) && $data['lng'] = $lng;
        isset($_POST['api_channel_id']) && $data['api_channel_id']= $api_channel_id;
        isset($_POST['on_shelf']) && $data['on_shelf']= $on_shelf?1:0;

        isset($_POST['landscape_level_id']) && (
            $data['landscape_level_id']= (($isLocal || $noVal===true)?$landscape_level_id:$value['level'])
        );
        isset($_POST['phone']) && (
            $data['phone'] = $isLocal ?
                (($phone!='' || $noVal===true)?$phone:$value['telephone']) :
                (($noVal===false && $value['telephone'])?$value['telephone']:$phone)
        );
        isset($_POST['address']) && (
            $data['address'] = $isLocal ?
                (($address!='' || $noVal===true) ? $address : $value['address']) :
                (($noVal===false && $value['address']) ? $value['address'] : $address)
        );
        isset($_POST['biography']) && (
            $data['biography'] = $isLocal ?
                (($biography!='' || $noVal===true) ? $biography : $value['description']) :
                (($noVal===false && $value['description']) ? $value['description'] : $biography)
        );

        if($status){ //更改审核状态
            !in_array($status,array('normal','unaudited','failed')) && Lang_Msg::error("ERROR_UPDATE_2"); //状态参数有错
            $data['status'] = $status;
            $data['audited_by']= $operator['user_id']; //审核人
            $data['audited_at']= $now; //审核时间
            $status=='normal' && $data['normal_before']=1;  //曾经审核通过
            $operation_lang_id = $status=='normal'?'INFO_LANDSCAPE_4':($status=='failed'?'INFO_LANDSCAPE_5':'INFO_LANDSCAPE_6');
        }
        else if($deleted){
            $data['deleted_at']= $now;
            $operation_lang_id = 'INFO_LANDSCAPE_3';
        }
        else{
            $data['updated_at']= $now;
            $operation_lang_id = 'INFO_LANDSCAPE_2';
        }

        $LandscapeModel->begin();
        if($needAdd===true) {
            $data['id'] = $id;
            $r = $LandscapeModel->add($data);
        } else {
            $r = $LandscapeModel->updateByAttr($data,array('id'=>$id));
        }
        if($r){
            $LandscapeModel->commit();
            Yaf_Application::app()->getDispatcher()->getRequest()->setParam('landscape_id',$id);
            Yaf_Application::app()->getDispatcher()->getRequest()->setParam('organization_id',$data['organization_id']);
            Log_Landscape::model()->add(array('type'=>($deleted?3:2),'num'=>1,'content'=>Lang_Msg::getLang($operation_lang_id).'【'.$data['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang($deleted?'ERROR_DEL_0':'ERROR_OPERATE_0'),$data);
        }
        else{
            $LandscapeModel->rollback();
            Lang_Msg::error($deleted?'ERROR_DEL_1':'ERROR_OPERATE_1');
        }
    }
    
    /**
     * 修改景区机构ID
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function updateOrganizationIdAction(){
        $id = intval($this->body['id']);
        !$id && Lang_Msg::error("ERROR_LANDSCAPE_1"); //缺少景区ID参数
        $organization_id = intval($this->body['organization_id']);
        !$organization_id && Lang_Msg::error("缺少机构ID参数");

        $LandscapeModel = new LandscapeModel();
        $detail = $LandscapeModel->setCd(0)->getById($id);
        if(empty($detail)) Lang_Msg::error("ERROR_DETAIL_2");
        
        $oldOrganizationId = $detail['organization_id'];
        
        $detail['organization_id'] = $organization_id;
        $LandscapeModel->update($detail, ['id'=>$id]);
		
		$landscapeOrgs = LandOrgModel::model()->search(['organization_id'=>$detail['organization_id'], 'landscape_id'=>$id]);
		if (empty($landscapeOrgs)) {
			$userId = intval($this->body['user_id']);
			LandOrgModel::model()->addNew([
				'landscape_id'    => $id,
				'organization_id' => $detail['organization_id'],
				'release_right'   => 1,
				'check_right'     => 1,
				'check_log_right' => 1,
				'scenic_manage_right' => 0,
				'poi_manage_right'    => 1,
				'created_by'      => empty($userId)? 0: $userId
			]);
		}
        
        TicketTemplateBaseModel::model()->updateOrganizationId($oldOrganizationId, $id, $organization_id);

        Lang_Msg::output($detail);
    }

    public function usedListAction(){ //获取在使用的景区列表
        $where = array();

        $keyword = trim(Tools::safeOutput($this->body['keyword'])); //要搜索的字段
        $keyword && $where['name|like'] = array("%{$keyword}%");

        $organization_id = intval($this->body['organization_id']);
        if ($organization_id) {
            $maps = LandOrgModel::model()->setListKey('landscape_id')->search(array('organization_id' => $organization_id),'id,landscape_id',null,null,0);
            if ($maps)
                $ids = array_keys($maps);
            else {
                $result = array(
                    'data'=>array(),
                    'pagination'=>array( 'count'=>0,'current'=>0,'items'=>$this->items,'total'=>0)
                );
                Lang_Msg::output($result);
            }
        }
        $getIds = trim(Tools::safeOutput($this->body['ids']));
        if($getIds && preg_match("/[\d,]+/",$getIds))
            $ids = explode(',',$getIds);
        $ids && $where['id|in'] = $ids;

        $status = trim(Tools::safeOutput($this->body['status']));
        $status && $where['status|in'] = $status;

        $on_shelf = intval($this->body['on_shelf']);
        isset($this->body['on_shelf']) && $where['on_shelf'] = $on_shelf;

        $show_district_name = trim(Tools::safeOutput($this->body['show_district_name']));
        $show_poi = trim(Tools::safeOutput($this->body['show_poi']));

        $province_ids = trim(Tools::safeOutput($this->body['province_ids']));
        $city_ids = trim(Tools::safeOutput($this->body['city_ids']));
        $district_ids = trim(Tools::safeOutput($this->body['district_ids']));
        if ($province_ids || $city_ids || $district_ids) {
            $where = array();
            $organization_id && $ids && $where['id|in'] = $ids;
            preg_match("/[\d,]+/",$province_ids) && $where['province_id|in'] = explode(',', $province_ids);
            preg_match("/[\d,]+/",$city_ids) && $where['city_id|in'] = explode(',', $city_ids);
            preg_match("/[\d,]+/",$district_ids) && $where['district_id|in'] = explode(',', $district_ids);
        }
        
        $where['has_bind_org']=1;

        if(intval($this->body['show_all'])){
            $data = LandscapeModel::model()->setCd(0)->search($where, $this->getFields(),$this->getSortRule());
        } else {
            $this->count = LandscapeModel::model()->countResult($where);
            $this->pagenation();
            $data = LandscapeModel::model()->setCd(0)->search($where, $this->getFields(),$this->getSortRule(),$this->limit);
        }


        $arr = array();
        if($data){
            $ids = array_keys($data);
            $poiList = array();
            if($show_poi){
                $poiWhere = array();
                $poiWhere['landscape_id|IN'] = $ids;
                $poiWhere['status'] = 1;
                $pois = PoiModel::model()->search($poiWhere,"id,name,landscape_id");
                foreach($pois as $pv){
                    $poiList[$pv['landscape_id']][] = array('poi_id'=>$pv['id'],'poi_name'=>$pv['name']);
                }
            }
            $landscape_levels = LandscapeLevelModel::model()->search(array('id|>'=>0),"id,name");
            foreach ($data as $value) {
                if($show_district_name){
                    $district_id = $value['district_id'];
                    if ($district_id) {
                        $districts = DistrictModel::model()->getListByDistrict($district_id);
                        if($districts) {
                            $value['province_id'] = $districts[0]['id'];
                            $value['district'][] = $value['province_name'] = $districts[0]['name'];
                            $value['city_id'] = $districts[1]['id'];
                            $value['district'][] = $value['city_name'] = $districts[1]['name'];
                            $value['district_id'] = $district_id;
                            $value['district'][] = $value['district_name'] = $districts[$district_id]['name'];
                        }
                    }
                }

                $value['landscape_level_name'] = $value['landscape_level_id']?$landscape_levels[$value['landscape_level_id']]['name']:'';

                if($show_poi){
                    $value['poi_list'] = isset($poiList[$value['id']]) ? $poiList[$value['id']] : array();
                }

                $arr[] = $value;
            }
        }

        $result = array(
            'data'=>$arr,
            'pagination'=>array(
                'count'=>$this->count,
                'current'=>$this->current,
                'items'=>$this->items,
                'total'=>$this->total,
            )
        );
        if(intval($this->body['show_all'])){
            $result['pagination'] = array('count'=>count($arr));
        }
        Lang_Msg::output($result);
    }

    //按名称查询景区列表，zqf 2015-03-10
    public function listByNameAction(){
        $name = trim(Tools::safeOutput($this->body['name']));
        if(!$name) Lang_Msg::output(array());
        $data = LandscapeModel::model()->search(array('name|like'=>array("%{$name}%")),$this->getFields());
        Lang_Msg::output($data);
    }

}