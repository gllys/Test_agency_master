<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-16
 * Time: 下午2:25
 */

Class PoiController extends Base_Controller_Api {

    public function listsAction(){
        $where = array('deleted_at|EXP'=>'IS NULL');

        if(isset($_POST['status']) && 'all'!=$this->getParam('status'))
            $where['status'] = intval($this->getParam('status'))?1:0;

        $organization_ids = $this->getParam('organization_ids');
        if(!is_array($organization_ids) && preg_match("/^[\d,]+$/",$organization_ids))
            $organization_ids = explode(',',$organization_ids);
        if($organization_ids)
            $where['organization_id|IN'] = $organization_ids; //按机构查找

        $landscape_ids = $this->getParam('landscape_ids');
        if(!is_array($landscape_ids) && preg_match("/^[\d,]+$/",$landscape_ids))
            $landscape_ids = explode(',',$landscape_ids);
        if($landscape_ids)
            $where['landscape_id|IN'] = $landscape_ids; //按景区查找

        $ids = $this->getParam('ids');
        if(!is_array($ids) && preg_match("/^[\d,]+$/",$ids))
            $ids = explode(',',$ids);
        if($ids)
            $where['id|IN'] = $ids; //按景点ID查找

        //if(!$organization_ids && !$landscape_ids && !$ids)
        //    Lang_Msg::error("ERROR_POI_1"); //缺少参数：机构ID、景区ID或景点ID

        $searchBy = trim($this->getParam('search_by')); //要搜索的字段
        $searchBy = $searchBy ? $searchBy : 'name';
        $keyword = trim(Tools::safeOutput($this->getParam('keyword')));
        if($searchBy && !in_array($searchBy,array('name','description')))
            Lang_Msg::error("ERROR_SEARCH_1"); //该属性不支持搜索
        elseif($searchBy && $keyword)
            $where[$searchBy.'|LIKE'] = array("%{$keyword}%");

        $show_all = intval($this->body['show_all']);

        $PoiModel = new PoiModel();
        if($show_all){
            $data = $PoiModel->search($where,$this->getFields(),$this->getSortRule());
            $result = array(
                'data'=>$data,
                'pagination'=>array('count'=>count($data))
            );
        }
        else {
            $this->count = $PoiModel->countResult($where,"count(*) as count");
            $this->pagenation();
            $data = $this->count>0  ? $PoiModel->search($where,$this->getFields(),$this->getSortRule(),$this->limit) : array();

            $result = array(
                'data'=>array_values($data),
                'pagination'=>array('count'=>$this->count, 'current'=>$this->current, 'items'=>$this->items, 'total'=>$this->total,)
            );
        }

        Lang_Msg::output($result);
    }

    public function detailAction(){
        $where = array('deleted_at|EXP'=>'IS NULL');
        $id = intval($this->getParam('id'));
        if(!$id)
            Lang_Msg::error("ERROR_POI_3");
        $where['id'] = $id;

        $organization_id = intval($this->getParam('organization_id'));
        if($organization_id>0)
            $where['organization_id'] = $organization_id;

        $landscape_id = intval($this->getParam('landscape_id'));
        if($landscape_id>0)
            $where['landscape_id'] = $landscape_id;

        // if(!$organization_id && !$landscape_id)
            // Lang_Msg::error("ERROR_POI_2"); //缺少参数：机构ID或景区ID

        $detail = PoiModel::model()->search($where,$this->getFields());
        if(!$detail)
            Lang_Msg::error("ERROR_DETAIL_2");
        $detail = $detail[$id];

        Lang_Msg::output($detail);
    }

    public function addAction(){
        $operator = $this->getOperator(); //获取操作者

        $data = array();
        $data['name'] = trim(Tools::safeOutput($this->getParam('name')));
        $data['description'] = trim(Tools::safeOutput($this->getParam('description')));
        $data['organization_id'] = intval($this->getParam('organization_id'));
        $data['landscape_id'] = intval($this->getParam('landscape_id'));
        $data['created_by'] = $operator['user_id']; //添加者

        isset($_POST['status']) && $data['status'] = intval($this->getParam('status'));
        !$data['name'] &&  Lang_Msg::error("ERROR_POI_4"); //景点名称不能为空
        !$data['organization_id'] &&  Lang_Msg::error("ERROR_POI_5"); //请选择景点所属机构
        !$data['landscape_id'] &&  Lang_Msg::error("ERROR_POI_6"); //请选择景点所属景区

        $PoiModel = new PoiModel();

        $has = $PoiModel->search(array('landscape_id'=>$data['landscape_id'],'name'=>$data['name'],'deleted_at|EXP'=>'IS NULL'));
        $has && Lang_Msg::error('ERROR_POI_8'); //该景区下已存在此名称的景点

        $PoiModel->begin();
        $r = $PoiModel->addNew($data);
        if($r){
            $PoiModel->commit();
            Log_Landscape::model()->add(array('type'=>Log_Test::$type['CREATE'],'num'=>1,'poi_ids'=>$r['id'],'content'=>Lang_Msg::getLang('INFO_POI_1').'【'.$data['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_ADD_0'), $r);
        }
        else{
            $PoiModel->rollback();
            Lang_Msg::error("ERROR_ADD_1");
        }
    }

    public function updateAction(){
        $operator = $this->getOperator(); //获取操作者

        $where = array('deleted_at|EXP'=>'IS NULL');
        $id = intval($this->getParam('id'));
        !$id && Lang_Msg::error("ERROR_POI_3");
        $where['id'] = $id;

        $organization_id = intval($this->getParam('organization_id'));
        $organization_id>0 && $where['organization_id'] = $organization_id;

        $landscape_id = intval($this->getParam('landscape_id'));
        $landscape_id>0 && $where['landscape_id'] = $landscape_id;

        (!$organization_id && !$landscape_id) && Lang_Msg::error("ERROR_POI_2"); //缺少参数：机构ID或景区ID

        $PoiModel = new PoiModel();
        $detail = $PoiModel->search($where);
        !$detail && Lang_Msg::error("ERROR_DETAIL_2");
        $data = $detail[$id];

        $name = trim(Tools::safeOutput($this->getParam('name')));
        $description = trim(Tools::safeOutput($this->getParam('description')));
        $deleted = intval($this->getParam('deleted'));
        $status = intval($this->getParam('status'))?1:0;

        $data['name']= $name ? $name : $data['name'];
        $data['description']= $description ? $description : $data['description'];
        $data['status'] = isset($_POST['status']) ? $status : $data['status'];
        if($deleted){
            $data['deleted_at'] = date("Y-m-d H:i:s", time() );
            $operation_lang_id = 'INFO_POI_5';
        }
        else{
            $has = $PoiModel->search(array('landscape_id'=>$data['landscape_id'],'name'=>$data['name'],'id|!='=>$id,'deleted_at|EXP'=>'IS NULL'));
            $has && Lang_Msg::error('ERROR_POI_8'); //该景区下已存在此名称的景点

            $data['updated_at']= date("Y-m-d H:i:s", time());
            $operation_lang_id = isset($_POST['status']) ? ($status?'INFO_POI_3':'INFO_POI_4'):'INFO_POI_2';
        }

        $PoiModel->begin();
        $r = $PoiModel->updateById($id,$data);
        if($r){
            $PoiModel->commit();
            Log_Landscape::model()->add(array('type'=>Log_Test::$type[($deleted?'DEL':'UPDATE')],'num'=>1,'poi_ids'=>$id,'content'=>Lang_Msg::getLang($operation_lang_id).'【'.$data['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang($deleted?'ERROR_DEL_0':'ERROR_OPERATE_0'),$data);
        }
        else{
            $PoiModel->rollback();
            Lang_Msg::error($deleted?'ERROR_DEL_1':'ERROR_OPERATE_1');
        }
    }
	
   /**
    *根据景区ID来获取景点 
    * 
    */
	public function  poilistAction()
	{
		!Validate::isString( $this->body[  'ids' ] ) && Lang_Msg::error( 'no ids');
  	    $id_arr = explode( ',', trim( $this->body[ 'ids' ]) );
  	    $return = array();
  	    foreach ( $id_arr as $id )
  	    {
  	    	$tmp = PoiModel::model()->search(  array( 'landscape_id' => $id ) );
  	    	if( $tmp )
  	    	{
  	    		 foreach( $tmp as $v )
  	    		 {
  	    		 	$return[ $id ][ $v[ 'id' ] ] = $v[ 'name' ] ;
  	    		 }
  	    	}
  	    	else
  	    	{
  	    		$return[ $id ] = '';
  	    	}
  	    }
  	    Lang_Msg::output( $return );
	}

    /**
     * 进园出园人数
     * author : yinjian
     */
    public function instantAction()
    {
        !Validate::isInt($this->body['num']) && Lang_Msg::error('人数不正确');
        $num = intval($this->body['num']);
        $code = trim($this->body['code']);
        $landscape_id = intval($this->body['landscape_id']);
        (!$landscape_id && !$code) && Lang_Msg::error('设备号或景区ID不能为空');
		
		if (empty($landscape_id)) {
			$equipment = reset(EquipmentModel::model()->search(array('code'=>$code)));
			!$equipment && Lang_Msg::error('设备不存在');
			$landscape_id = $equipment['landscape_id'];
		}
        !$landscape_id && Lang_Msg::error('设备不存在');
		
		$Pv = PvModel::model();
        $day_poi_num_cache_key = $Pv->redisCacheKey.':'.date('Ymd');
		$arr = [0,0];
		if($old = $Pv->redis->hget($day_poi_num_cache_key,$landscape_id)){
			$arr = explode('|', $old);
			if ($num > 0) { // 入园
				$arr[0] += $num;
			} else { // 出园
				$arr[1] += abs($num);
			}
		}
		$Pv->redis->hset($day_poi_num_cache_key,$landscape_id, implode('|', $arr));
		
		// 流量统计数据写入或增减
		$pv = $Pv->search(['landscape_id'=>$landscape_id, 'date'=>date('Y-m-d'), 'hour'=>date('H')], '*', null, 1);
		if (empty($pv)) {
			$Pv->add([
				'landscape_id' => $landscape_id,
				'date'		   => date('Y-m-d'),
				'in_num'       => $num>0 ? $num: 0,
				'out_num'      => $num<0 ? abs($num): 0,
				'hour'         => date('H'),
				'created_at'   => time()
			]);
		} else {
			$pv = current($pv);
			if ($num > 0) {
				$update['in_num'] = $pv['in_num']+$num;
			} else {
				$update['out_num'] = $pv['out_num']+abs($num);
			}
			$update['updated_at'] = time();
			$Pv->update($update, ['id' => $pv['id']]);
		}

        Lang_Msg::output();
    }

    /**
     * 统计景区下所有景点实时人数
     * author : yinjian
     */
    public function statAction()
    {
        $landscape_id = intval($this->body['landscape_id']);
        $landscape_id<=0 && Lang_Msg::error('景区id不能为空');
		
        $day_poi_num_cache_key = PvModel::model()->redisCacheKey.':'.date('Ymd');
        $cache = PvModel::model()->redis->hget($day_poi_num_cache_key, $landscape_id);
		if ($cache) {
			$counts = explode('|', $cache);
		} else {
			$counts = [0,0];
		}
		
        $poi = PoiModel::model()->search(array('landscape_id'=>$landscape_id,'deleted_at|exp'=>'is null'));
        // 获取所有子景点
		
        foreach($poi as $k=>$v){
            $poi[$k]['num'] = max(0, ($counts[0] - $counts[1]));
        }
		
        Lang_Msg::output($poi);
    }
}