<?php

class DeviceController extends Base_Controller_Api
{
	 public $config = array();
     public $body = array();
     
     
     public  function testAction()
     {
     	echo 'test';
     }

    public function getlistAction() { //获取设备列表api
        $where = array('deleted_at'=>0);
        $ids = trim($this->body['ids']);
        if(preg_match("/[\d,]+/",$ids)) {
            $where['id|in'] = explode(',',$ids);
        }

        $codes = trim($this->body['codes']);
        if(preg_match("/[\w,]+/",$codes)) {
            $where['code|in'] = explode(',',$codes);
        }
        $status = intval($this->body['status']);
        if(isset($this->body['status'])) {
            $where['status'] = $status>0?1:0;
        }

        $fell_status = intval($this->body['fell_status']);
        if(isset($this->body['fell_status'])) {
            $where['fell_status'] = $fell_status>0?1:0;
        }

        $type = intval($this->body['type']);
        if(isset($this->body['type'])) {
            $where['type'] = $type;
        }

        $name = trim($this->body['name']);
        if(!empty($name)) {
            $where['name|like'] = array("%{$name}%");
        }

        $organization_id = intval($this->body['organization_id']);
        if($organization_id>0) {
            $where['organization_id'] = $organization_id;
        }

        $landscape_id = intval($this->body['landscape_id']);
        if($landscape_id>0) {
            $where['landscape_id'] = $landscape_id;
        }

        $poi_id = intval($this->body['poi_id']);
        if($poi_id>0) {
            $where['poi_id'] = $poi_id;
        }
        $EquipmentModel = new EquipmentModel();
        $this->count = $EquipmentModel->countResult($where);
        $this->pagenation();
        $data['data'] = $this->count>0 ? $EquipmentModel->search($where,$this->getFields(),$this->getSortRule(),$this->limit) : array();
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true,'ok',$data);
    }
     
     
     //根据ID或CODE获得一个设备
     public  function  detailAction()
     {
     	$where = array();
        if( $this->body[  'id' ] )$where[ 'id' ] = trim( $this->body[ 'id' ] );
        if( $this->body[ 'code' ] )$where[ 'code' ] = trim( $this->body[ 'code' ] );
        if( !$where) Lang_Msg::error('no params' );
     	if( $return = DeviceModel::model()->search( $where ) )
     	{
            $info = reset( $return );
            $landscape_id = $info['landscape_id'];
            if ($landscape_id) {
                $landscape = LandscapeModel::model()->getById($landscape_id);
                if ($landscape) $info['landscape_name'] = $landscape['name'];
            }
     		Lang_Msg::output(  $info  );
     	}
     	else
     	{
     		Lang_Msg::error( 'error');
     	}
     }
     
	//根据ID或CODE获得一个设备 方少专用
     public  function  showAction()
     {
     	$where = array();
        if( $this->body[  'id' ] )$where[ 'id' ] = trim( $this->body[ 'id' ] );
        if( $this->body[ 'code' ] )$where[ 'code' ] = trim( $this->body[ 'code' ] );
        if( !$where) Lang_Msg::error('no params' );
     	if( $return = DeviceModel::model()->search( $where ) )
     	{
     		Lang_Msg::output( reset( $return ));
     	}
     	else
     	{
     		Lang_Msg::error( 'error');
     	}
     }
     
     
     //查看设备
    public  function listsAction()
    {
     	try {
            //页码
            $page = intval($this->body['p']);
            $page = $page == 0 ? 1 : $page;
            //机构ID
            $organization_id = intval($this->body['organization_id']);
            //景区ID
            $landscape_id = intval($this->body['landscape_id']);
            //设备编号
            $code = trim(Tools::safeOutput($this->body['code']));
            //开始时间
            $s_time = intval($this->body['s_time']);
            //结束时间
            $e_time = intval($this->body['e_time']);
            //是否绑定景区
            $is_bind = $this->body['is_bind'];
            //是否安装
            $is_fix = $this->body['is_fix'];

            $where = array();
            $limit = array();

            $page_limit = 15;

            $org_name = trim(Tools::safeOutput($this->body['org_name'])); //按机构名称模糊查询
            if ($org_name) {
                $orgIds = OrganizationModel::model()->getOrgIds(array('type' => 'supply', 'name' => $org_name));
                if ($orgIds) {
                    $where['organization_id|in'] = $orgIds;
                } else {
                    Lang_Msg::output(array('data' => array(), 'pagination' => array('count' => 0)));
                }
            }

            $scenic_name = trim(Tools::safeOutput($this->body['scenic_name'])); //按景区名称模糊查询
            if ($scenic_name) {
                $scenicList = LandscapeModel::model()->search(array('name|like' => array("%{$scenic_name}%")));
                if ($scenicList) {
                    $landscape_ids = array_keys($scenicList);
                    $where['landscape_id|in'] = $landscape_ids;
                } else {
                    Lang_Msg::output(array('data' => array(), 'pagination' => array('count' => 0)));
                }
            }

            if ($landscape_id > 0) $where['landscape_id'] = $landscape_id;
            if ($organization_id > 0) $where['organization_id'] = $organization_id;
            if ($code) $where['code|like'] = array("%{$code}%");
            if ($s_time > 0) $where['updated_at|>='] = $s_time;
            if ($e_time > 0) $where['updated_at|<='] = $e_time;

            if (null !== $is_bind) {
                if ($is_bind == 0) {
                    $where['landscape_id|='] = 0;
                } else {
                    $where['landscape_id|>'] = 0;
                }
            }

            if (null !== $is_fix) {
                if ($is_fix == 0) {
                    $where['poi_id|='] = 0;
                } else {
                    $where['poi_id|>'] = 0;
                }
            }
            $where['deleted_at'] = 0;
            if ($page > 0) $limit = array(($page - 1) * $page_limit, $page_limit);
            $count = DeviceModel::model()->countResult($where);
            $total = ceil($count / $page_limit);
            $arr = DeviceModel::model()->search($where, '*', ' updated_at desc  ', $limit);

            $landscape_ids = $landscapes = $poi_ids = $pois = array();

            foreach ($arr as $k => $v) {
                if ($v['landscape_id'] > 0) $landscape_ids[$v['landscape_id']] = $v['landscape_id'];
                if ($v['poi_id'] > 0) $poi_ids[$v['poi_id']] = $v['poi_id'];
            }

            if ($landscape_ids) $landscapes = LandscapeModel::model()->getByIds($landscape_ids);


            if ($landscape_id > 0) $pois = PoiModel::model()->search(array('landscape_id' => $landscape_id, 'status' => 1));

            else if ($poi_ids) $pois = PoiModel::model()->getByIds($poi_ids);


            foreach ($arr as $k => $v) {
                $arr[$k]['landscape'] = $landscapes[$v['landscape_id']];
                $arr[$k]['poi'] = $pois[$v['poi_id']];
                if ($v['organization_id'] != 0) {
                    //$tmp = OrganizationModel::model()->getInfo( $v[ 'organization_id' ] );
                    $arr[$k]['supply'] = $v['organization_id'];
                } else {
                    $arr[$k]['supply'] = array();
                }

            }

            $poi_names = array();
            foreach ($pois as $key => $poi) {
                $poi_names[$key] = $poi['name'];
            }

            $return = array();
            $return['data'] = $arr;
            $return['pois'] = $poi_names;
            $return['pagination'] = array('count' => $count, 'current' => $page, 'items' => $page_limit, 'total' => $total);
            if ($landscape_id > 0) {

            }
            Lang_Msg::output($return);
        } catch(Exception $e){
            Lang_Msg::output(array('data' => array(), 'pagination' => array('count' => 0)));
        }
    }

    /**
     * 获取绑定的景区和机构
     * author : yinjian
     */
    public function landorgAction()
    {
        $show_all_organization = $this->body['show_all_organization'];
        $show_all_landscape = $this->body['show_all_landscape'];

        $arr = array();
        if(isset($this->body['show_all_organization']) && $show_all_organization){
            $where = ' deleted_at =0 and organization_id>0 GROUP BY organization_id';
            $fields = 'id,organization_id';
            $data = DeviceModel::model()->search($where,$fields);
            foreach ($data as $k => $v) {
                $arr['organization_id'][] = $v['organization_id'];
            }
        }
        if(isset($this->body['show_all_landscape']) && $show_all_landscape){
            $where = ' deleted_at =0 and landscape_id>0 GROUP BY landscape_id';
            $fields = 'id,landscape_id';
            $data = DeviceModel::model()->search($where,$fields);
            foreach ($data as $k => $v) {
                $arr['landscape_id'][] = $v['landscape_id'];
            }
        }
        Lang_Msg::output($arr);
    }
    
     //添加设备
     public  function addAction()
     {
     	
     	$args = array();
     	$args[ 'type' ] 	 = null !==( intval( $this->body[ 'type'] ) )? intval( $this->body[ 'type'] ):Lang_Msg::error( 'ERROR_SB_1' ) ;
     	$args[ 'code' ]		 = null !==( $this->body[ 'code' ] ) ?  addslashes($this->body[ 'code' ] ): Lang_Msg::error( 'ERROR_SB_2' );
     	$args[ 'name' ]		 = addslashes( $this->body[ 'name' ] );
		$args[ 'scene' ]     = intval($this->body['scene']);//设备类型属性
		
     	if(  !in_array( $args[ 'type' ], array(0 ,1 ) ) ) Lang_Msg::error( 'ERROR_SB_1' );
     	if( null ==( $args[ 'code' ] ) || is_null( $args[ 'code' ] )  ) Lang_Msg::error( 'ERROR_SB_2' );
     	//自动补全
     
     	$args[ 'created_at' ]	= time();
     	$args[ 'updated_at' ]	= time();
		//判断code 是否存在
		if( DeviceModel::model()->search( array( 'code' => $args[ 'code' ]))) Lang_Msg::error( '设备编号重复!');
     	$re = DeviceModel::model()->add( $args );
     	if( $re )
     	{	
     		$last_id = DeviceModel::model()->getInsertId();
     		$data = DeviceModel::model()->getById( $last_id );
     		Lang_Msg::output( $data ); 
     	}
     	else 
     	{
     		Lang_Msg::error( 'ERROR_SB_5' );
     	}
     }
     
     //修改设备信息
     public  function updateAction()
     {
     	$args = array();
     	$id= Tools::intval($this->body[ 'id' ] );
     	if( $id <= 0 ) Lang_Msg::error( 'ERROR_SB_8' );
     	if( null !==( $this->body[ 'type' ] ) )
     	{
     		if( in_array( $this->body[ 'type'], array( 1,0)))
     		{
     			$args[ 'type' ] = $this->body[ 'type' ];
     		}
     		else
     		{
     			Lang_Msg::error( 'ERROR_SB_1' );
     		}
     	}
    
     	$args[ 'code' ] = null !==( $this->body[ 'code' ] ) ? $this->body[ 'code'] :Lang_Msg::error( 'ERROR_SB_2' );
     	if( isset( $this->body[ 'name' ] )) $args[ 'name' ] = $this->body[ 'name'] ;
     	if( isset( $this->body[ 'scene' ] )) $args[ 'scene' ] = $this->body[ 'scene'] ; //设备类型属性
     	
     	//自动补全
     	$args[ 'updated_at' ] = time();
     	if( isset( $this->body[ 'update_by' ] ) ) $args[ 'update_by' ] = $this->body[ 'update_by' ];
     	$re =DeviceModel::model()->updateById( $id, $args);
     	if( $re )
     	{
     		Lang_Msg::output( 'update server ok!' );
     	}
     	else
     	{
     		Lang_Msg::error( 'ERROR_SB_7' );
     	}
     }
     
     //删除一个设备
     public  function deleteAction()
     {
     	$id = isset( $this->body[ 'id' ] ) ? $this->body[ 'id' ] : Lang_Msg::error( 'ERROR_SB_8' );
     	$re = DeviceModel::model()->deleteById($id);
     	if( $re )
     	{
     		Lang_Msg::output( 'delelte server!' );
     	}
     	else
     	{
     		Lang_Msg::error( 'ERROR_SB_10' );
     	}
     }
     
     //绑定装备
     public  function bindingAction( )
     {  
     	// type =>array( 0=>景点， 1=>子景点, 2=>机构ID);
     	// statue = array( 0=>解绑， 1=>绑定)；
        $id = intval( $this->body[ 'id' ] );
     	$type = in_array($this->body[ 'type'], array( 0,1,2 ) ) ? $this->body[ 'type' ] : 0;
     	$statue = $this->body[ 'statue' ]==1? 1 : 0;
     	$scene_id = $this->body[ 'scene_id'];
     	if( is_null($scene_id) ) Lang_Msg::error( '景点ID不正确！');
        if ($id<=0) Lang_Msg::error( 'ERROR_DEL_2');
     	$row = DeviceModel::model()->getById( $id );
     	if( !$row ) Lang_Msg::error( '设备ID不正确！');
		
		$scene_id = intval($scene_id);

     	$args = array();

        if (isset($this->body[ 'scene_id']) && is_numeric($this->body[ 'scene_id'])) {
            if($scene_id == 0 )
            {   
                if(  $type == 0 )
                {
                    $args[ 'poi_id' ] = $args[ 'landscape_id' ]  = 0;
                }
                else if( $type ==1 )
                {
                    $args[ 'poi_id' ] = 0;
                }
                else
                {
                    $args[ 'poi_id' ] = $args[ 'landscape_id' ]  = $args[ 'organization_id' ] = 0;
                }
            } else {
                if( $type == 0 )
                {
                    $args[ 'landscape_id' ] = $scene_id;
                }
                else if( $type ==1 )
                {
                    $args[ 'poi_id' ] = $scene_id;
                }
                else if( $type == 2 )
                {
                    $args[ 'organization_id' ] = $scene_id;
                }
            }
        }
     	
        if (isset($this->body[ 'statue']) && is_numeric($this->body[ 'statue'])) {
		      $args[ 'status' ] = $this->body[ 'statue'];
        }
        if (isset($this->body[ 'fell_status']) && is_numeric($this->body[ 'fell_status'])) {
              $args[ 'fell_status' ] = $this->body[ 'fell_status'];
        }
        if (isset($this->body[ 'scene']) && is_numeric($this->body[ 'scene'])) {
              $args[ 'scene' ] = $this->body[ 'scene'];
        }
//        if ($args) $re = DeviceModel::model()->updateById( $id, $args);
         if($args) $re = DeviceModel::model()->updateByAttr($args,array('id'=>$id));

		if( $re )
		{
			Lang_Msg::output( 'ok' );
		}
		else
		{
			Lang_Msg::error( 'ERROR_SB_7' );
		}
     }

    //落杆
    public function fellAction(){
        $id = intval($this->body['id']);
        $landscape_id = intval($this->body['landscape_id']);
        $fell_status = intval($this->body['fell_status'])?1:0; //自动落杆 0:未落杆 1:自动落杆

        !$id && !$landscape_id && Lang_Msg::error('ERROR_DETAIL_1');

        $where = array();
        $landscape_id && $where['landscape_id'] = $landscape_id;
        $id && $where['id'] = $id;

        $re = DeviceModel::model()->updateByAttr(array('fell_status'=>$fell_status),$where);

        if( $re )
        {
            Lang_Msg::output( 'ok' );
        }
        else
        {
            Lang_Msg::error( 'ERROR_SB_7' );
        }
    }
}