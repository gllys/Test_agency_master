<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2014/12/18
 * Time: 18:04
 */

class TickettemplatebaseController extends Base_Controller_Api
{
    //根据票ID来获取票信息
    public  function ticketinfoAction()
    {
        //票ID
        $ticket_id = null == $this->body[ 'ticket_id' ] ? Lang_Msg::error( 'ERROR_TICKET_1' ): $this->body[ 'ticket_id' ];
        $where[ 'id' ]= $ticket_id;
        $where[ 'is_del' ] =0;
        //分销商ID
        $return = TicketTemplateBaseModel::model()->get( $where );
        if( !$return ) Lang_Msg::error( '票不存在！');

        //如果有分销商ID -----------------------------------
        $distributor_id = intval($this->body['distributor_id']);
        $use_day = trim($this->body['use_day']);
        if($distributor_id) {
            $return[ 'can_buy' ] = true; //散客权限

            if(!$return['is_full']){
                $return[ 'can_buy' ] = false;
            }
            //检查分销售的价格模版
            $r = ApiOrganizationModel::model()->getSupplyAgency($return['organization_id'],$distributor_id);
            if($r['price_tpl_id']){
                $priceTplInfo = reset(PriceTplItemModel::model()->search(array(
                    'price_tpl_id'=>$r['price_tpl_id'], 'ticket_template_base_id'=> $ticket_id
                )));
                $priceTplInfo && $return['price_tpl']= $priceTplInfo;
            }
        }
        //检查游玩日期是否可游玩
        if($use_day){
            if( $return[ 'state' ] !=1 ) Lang_Msg::error( 'ERRO_TICKET_15' );

            $return[ 'can_play' ] = false;
            if(!$return[ 'date_available' ]){
                $return[ 'can_play' ] = true;
            } else {
                $t = strtotime($use_day.' 00:00:00');
                $ticket_time = explode( ',', $return[ 'date_available' ] );
                if( $t >= $ticket_time[ 0 ] && $t <= $ticket_time[ 1 ] ) {
                    $return[ 'can_play' ] = true;
                }
            }
        }

        Lang_Msg::output( $return );
    }

    //根据景区id,机构ID来显示列表，分页显示 @TODO ids,field,name
    public  function listsAction() {
        $where = array('is_del'=>0);
        if(isset($this->body['is_full'])) $where['is_full'] = intval($this->body['is_full']);
        if(intval($this->body['organization_id'])) $where['organization_id'] = intval($this->body['organization_id']);
        else if(intval($this->body['or_id'])) $where['organization_id'] = intval($this->body['or_id']);

        if(isset($this->body['source_type'])) $where['source_type'] = intval($this->body['source_type']);

        if(isset($this->body['p']))
            $this->current = $this->body['p'];

        $scenic_id = trim($this->body[ 'scenic_id']);
        if($scenic_id && preg_match("/^[\d,]+$/",$scenic_id)) {
            $scenic_ids = explode( ',', $scenic_id );
            $where['scenic_id|in']= $scenic_ids;
        }

        $view_point = trim($this->body['view_point']);
        if($view_point && preg_match("/^[\d,]+$/",$view_point)) {
            $poiIds = explode( ',', $view_point );
            $poidAnd = array();
            foreach($poiIds as $poi){
                array_push($poidAnd,"find_in_set({$poi},view_point)");
            }
            $where['AND'] = implode(' OR ',$poidAnd);
        }

        $poiId= array();
        $name = '';
        $poi_name = trim(Tools::safeOutput($this->body['poi_name']));
        if($poi_name){
            $poiParams = array('keyword'=>$poi_name,'status'=>1,'fields'=>'id','show_all'=>1);
            isset($where['organization_id']) && $poiParams['organization_ids'] = $where['organization_id'];
            $scenic_id && $poiParams['landscape_ids']= $scenic_id;
            $view_point && $poiParams['ids']= $view_point;
            $poiIds = ApiPoiModel::model()->GetPoiIds($poiParams);
        }

        $name = trim(Tools::safeOutput($this->body['name']));
        if($poiIds && !$name){
            $poidAnd = array();
            foreach($poiIds as $poi){
                array_push($poidAnd,"find_in_set({$poi},view_point)");
            }
            $where['AND'] = implode(' OR ',$poidAnd);
        }
        else if(!$poiIds && $name){
            $where['name|like'] = array("%{$name}%");
        }
        else if($poiIds && $name){
            $poidAnd = array();
            foreach($poiIds as $poi){
                array_push($poidAnd,"find_in_set({$poi},view_point)");
            }
            $where['OR'] = array(
                'AND'=>implode(' OR ',$poidAnd),
                'name|like'=> array("%{$name}%"),
            );
        }



        $state = intval($this->body[ 'state' ]);
        if(preg_match("/^\d+$/",$this->body[ 'state' ])) $where['state'] = $state;
        else if($this->body[ 'state' ]) $where['state|>'] = 0;

        $ids = trim(Tools::safeOutput($this->body['ids']));
        preg_match("/^[\d,]+$/",$ids) && $ids = explode(',',$ids);
        $ids && $where['id|in']= $ids;

        $gid =  trim(Tools::safeOutput($this->body['gid']));
        $gid && $where['gid'] = $gid;

        $types = trim(Tools::safeOutput($this->body['types']));
        preg_match("/^[\d,]+$/",$types) && $types = explode(',',$types);
        $types && $where['type|in']= $types;

        $show_all = intval($this->body['show_all']);
        $show_group = intval($this->body['show_group']);

        $TicketTemplateBaseModel = new TicketTemplateBaseModel();

        $groupBy = $show_group?'gid,organization_id,scenic_id,created_at':'';
        $fields = $show_group?$this->getFields('id').",group_concat(id,'_',type,'_',sale_price,'_',mult_enter,'_',enter_by_card,'_',num_by_card,'_',enter_by_qrcode,'_',poi_enter_num) as type_prices":$this->getFields('id');

        if($show_all) {
            $data = $TicketTemplateBaseModel->setGroupBy($groupBy)->search( $where, $fields, $this->getSortRule('updated_at'));
        } else {
            if($show_group)
                $this->count = count($TicketTemplateBaseModel->setGroupBy($groupBy)->search($where,"id"));
            else
                $this->count = $TicketTemplateBaseModel->countResult($where);
            $this->pagenation();
            $data = $this->count > 0 ? $TicketTemplateBaseModel->setGroupBy($groupBy)->search( $where,$fields, $this->getSortRule('updated_at'), $this->limit ):array();
        }
        
        if($show_group) {
            $tmp_arr = array();
            foreach($data as $k=>$v) {
                $type_prices = explode(',',$v['type_prices']);
                $v['type_prices'] = array();
                foreach($type_prices as $tpv) {
                    $tp = explode('_',$tpv);
                    $v['type_prices'][] = array(
                        'id'=>$tp[0],
                        'type'=>$tp[1],
                        'sale_price'=>$tp[2],
                        'mult_enter'=>$tp[3],
                        'enter_by_card'=>$tp[4],
                        'num_by_card'=>$tp[5],
                        'enter_by_qrcode'=>$tp[6],
                        'poi_enter_num'=>$tp[7],
                    );
                }
                $tmp_arr[$v['gid'].'_'.$v['organization_id'].'_'.$v['scenic_id'].'_'.$v['created_at']] = $v;
            }
            $data = $tmp_arr;
            unset($tmp_arr);
        }

        $return = array();
        $return['data'] = $data;
        if($show_all){
            $return['pagination'] = array('count'=>count($return['data']));
        } else {
            $return['pagination'] = array('count'=>$this->count, 'current'=>$this->current, 'items'=>$this->items, 'total'=>$this->total);
        }
        $return['types'] = TicketTypeModel::model()->getAll();
        Lang_Msg::output($return);
    }

    //修改票信息
    public  function updateAction()
    {
        $where = array('is_del'=>0);
        $id = intval( $this->body[ 'id' ] );
        if( !$id )Lang_Msg::error( 'ERROR_TICKET_1' );
        $where['id'] = $id;

        $organization_id = intval( $this->body[ 'organization_id' ] );
        $organization_id && $where['organization_id'] = $organization_id;
        //根据ID来查询
        $return = TicketTemplateBaseModel::model()->search($where);
        !$return && Lang_Msg::error('基础票不存在');
        $return = reset($return);
        //获取参数
        $args = array();
        if( isset(  $this->body[ 'name']))$args[ 'name' ] = $this->body[ 'name'] ;
        if( isset(  $this->body[ 'fat_price']))$args[ 'fat_price' ] = $this->body[ 'fat_price'] ;
        if( isset(  $this->body[ 'group_price']))$args[ 'group_price' ] = $this->body[ 'group_price'] ;
        if( isset(  $this->body[ 'sale_price']))$args[ 'sale_price' ] = $this->body[ 'sale_price'] ;
        if( isset(  $this->body[ 'listed_price']))$args[ 'listed_price' ] = $this->body[ 'listed_price'] ;
        if( isset(  $this->body[ 'valid']))$args[ 'valid' ] = $this->body[ 'valid'] ;
        if( isset(  $this->body[ 'valid_flag']))$args[ 'valid_flag' ] = intval($this->body[ 'valid_flag']) ; //预定后是否一直有效 0否 1是
        if( isset(  $this->body[ 'max_buy']))$args[ 'max_buy' ] = $this->body[ 'max_buy'] ;
        if( isset(  $this->body[ 'mini_buy']))$args[ 'mini_buy' ] = $this->body[ 'mini_buy'] ;
        if( isset(  $this->body[ 'payment']))$args[ 'payment' ] = $this->body[ 'payment'] ;
        if( isset(  $this->body[ 'scenic_id']))$args[ 'scenic_id' ] = intval($this->body[ 'scenic_id']) ;
        if( isset(  $this->body[ 'is_fit']))$args[ 'is_fit' ] = $this->body[ 'is_fit' ] ;
        if( isset(  $this->body[ 'is_full']))$args[ 'is_full' ] = $this->body[ 'is_full'] ;
        if( isset(  $this->body[ 'scheduled_time']))$args[ 'scheduled_time' ] = $this->body[ 'scheduled_time'] ;
        if( isset(  $this->body[ 'is_del']) && $this->body['is_del']==1){
            $ticketTemplateItem = TicketTemplateItemModel::model()->search(array('base_id'=>$id,'deleted_at'=>0));
            $product_id = array();
            foreach($ticketTemplateItem as $k=>$v){
                $product_id[] = $v['product_id'];
            }
            $ticketTemplate = TicketTemplateModel::model()->search(array('id|in'=>$product_id,'state'=>1));
            if($ticketTemplate){
                Lang_Msg::error('该基础票有产品上架不能删除');
            }
            $args[ 'is_del' ] = 1 ;
        }
        if( isset(  $this->body[ 'week_time']))$args[ 'week_time' ] = $this->body[ 'week_time'] ;
        if( isset(  $this->body[ 'type']))$args[ 'type' ] = intval($this->body[ 'type']) ;
        if( isset(  $this->body[ 'remark']))$args[ 'remark' ] = $this->body[ 'remark'] ;
        if( isset(  $this->body[ 'rule_id'])) $args[ 'rule_id' ] = intval($this->body[ 'rule_id']) ;
        if( isset(  $this->body[ 'namelist_id'])) $args[ 'namelist_id' ] = intval($this->body[ 'namelist_id']) ;
        if( isset(  $this->body[ 'discount_id'])) $args[ 'discount_id' ] = intval($this->body[ 'discount_id']) ;
        if( isset(  $this->body[ 'view_point'])) $args[ 'view_point' ] = $this->body[ 'view_point'] ;
        if(isset($this->body['province_id'])) $args['province_id'] = intval($this->body['province_id']);
        if(isset($this->body['city_id'])) $args['city_id'] = intval($this->body['city_id']);
        if(isset($this->body['district_id'])) $args['district_id'] = intval($this->body['district_id']);
        if(isset($this->body['is_infinite'])) $args['is_infinite'] = intval($this->body['is_infinite']);
        if(isset($this->body['state'])) $args['state'] = intval($this->body['state']);
        if(isset($this->body['source_type'])) $args['source_type'] = intval($args['source_type'])?1:0;

        if((isset($args['state']) && $args['state']==2 && $return['state']==1) || 1===$args['is_del']){ //下架或删除需检查是否有相关上架产品
            $productItems = TicketTemplateItemModel::model()->search(array('base_id'=>$id));
            if($productItems){
                $productIds = array();
                foreach ($productItems as $v) {
                    $productIds[] = $v['product_id'];
                }
                $productLists = TicketTemplateModel::model()->search(array('id|in'=>$productIds,'state'=>1));
                $productLists && Lang_Msg::error('ERROR_AddGenerate_28');
            }
        }

        if(isset($args['scenic_id']) && $args['scenic_id']!=$return['scenic_id'] && !$args['province_id']){
            $scenicInfo = ScenicModel::model()->getScenicInfo(array('id'=>$args['scenic_id']));
            if($scenicInfo && !empty($scenicInfo['body'])) {
                $args['province_id'] = $scenicInfo['body']['province_id'];
                $args['city_id'] = $scenicInfo['body']['city_id'];
                $args['district_id'] = $scenicInfo['body']['district_id'];
            }
        }

        if( isset( $this->body[ 'date_available' ] ))
        {
            $args['date_available'] = $this->body['date_available'];
            $args['expire_start'] = reset(explode(',',$this->body['date_available']));
            $args['expire_end'] = end(explode(',',$this->body['date_available']));
            $args['real_expire_end'] = $args[ 'expire_end']-$args[ 'scheduled_time' ];
        }
        if((isset($this->body['date_available']) && $this->body['date_available']==0) || $this->body['is_infinite']==1){
            $args['expire_start'] = 0;
            $args['expire_end'] = 4294967295;
            $args['is_infinite'] = 1;
            $args['real_expire_end'] = 2147483647;
        }elseif($this->body['date_available']){
            $args['is_infinite'] = 0;
        }

        if(isset($this->body['mult_enter'])) { //是否允许多次入园:0否1是
            $args['mult_enter'] = intval($this->body['mult_enter'])?1:0;
            isset($this->body['enter_by_card']) && $args['enter_by_card'] = ($args['mult_enter'] && intval($this->body['enter_by_card'])) ? 1:0; //是否允许使用身份证入园:0否1是
            isset($this->body['num_by_card']) && $args['num_by_card'] = $args['enter_by_card'] ? intval($this->body['num_by_card']):0; //使用身份证可购买张数,0不限
            isset($this->body['enter_by_qrcode']) && $args['enter_by_qrcode'] = ($args['mult_enter'] && intval($this->body['enter_by_qrcode'])) ? 1:0; //是否允许使用二维码入园:0否1是
            isset($this->body['poi_enter_num']) && $args['poi_enter_num'] = ($args['mult_enter'] && intval($this->body['poi_enter_num'])) ? intval($this->body['poi_enter_num']):1; //每个景点在使用有效期内可使用次数
            isset($this->body['enter_by_palm']) && $args['enter_by_palm'] = intval($this->body['enter_by_palm']); //是否允许掌纹
        }

        if( empty( $args ) )Lang_Msg::error('没有需要修改的参数');
        $args['updated_at'] = time();
        $r = TicketTemplateBaseModel::model()->updateById( $id, $args );
        if($r) {
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else {
            Lang_Msg::error( 'ERRO_TICKET_11' );
        }
    }

    /*
     * 发布基础票（批量）
     * @author: zhaqinfeng
     * */
    public function addBatchAction() {
        $params = $this->getOperator();
        $params['name'] = trim(Tools::safeOutput($this->body['name']));
        $params['organization_id'] = intval($this->body['organization_id']);
        $params['scenic_id'] = intval($this->body['scenic_id']);
        $params['view_point'] = trim(Tools::safeOutput($this->body['view_point']));
        $params['valid'] = intval($this->body['valid']); //门票有效期，预定后多少天内有效
        $params['valid_flag'] = intval($this->body['valid_flag']); //预定后是否一直有效 0否 1是
        $params['scheduled_time'] = intval($this->body['scheduled_time']); //需提前X天/X小时预定
        $params['date_available'] = trim(Tools::safeOutput($this->body['date_available'])); //可玩日期  int(11),int(11) 表示一个时间段 ，逗号分隔
        $params['week_time'] = trim(Tools::safeOutput($this->body['week_time'])); //周几使用
        $params['province_id'] = intval($this->body['province_id']);
        $params['city_id'] = intval($this->body['city_id']);
        $params['district_id'] = intval($this->body['district_id']);
        $params['remark'] = trim(Tools::safeOutput($this->body['remark']));
        $params['source_type'] = intval($this->body['source_type'])?1:0;
        $params['state'] = null !== $this->body['state'] ? intval($this->body['state']) : 2;
        $items = json_decode($this->body['items'],true);
        if(isset($this->body['date_available']) && $params['date_available']){
            $params['expire_start'] = reset(explode(',',$params['date_available']));
            $params['real_expire_end'] = $params['expire_end'] = end(explode(',',$params['date_available']));
        }
        !$params['name'] && Lang_Msg::error("ERROR_AddGenerate_1");
        !$params['scenic_id'] && Lang_Msg::error("ERROR_AddGenerate_5");
        !$params['view_point'] && Lang_Msg::error("ERROR_AddGenerate_6");
        !$items && Lang_Msg::error("ERROR_AddGenerate_26");


        $params['payment'] = '5,6,7';
        $params['ota_type'] = null !== $this->body['ota_type'] ? $this->body[ 'ota_type' ] : 'system';

        if($params['scenic_id'] && !$params['province_id']){
            $scenicInfo = ScenicModel::model()->getScenicInfo(array('id'=>$params['scenic_id']));
            if($scenicInfo && !empty($scenicInfo['body'])) {
                $params['province_id'] = $scenicInfo['body']['province_id'];
                $params['city_id'] = $scenicInfo['body']['city_id'];
                $params['district_id'] = $scenicInfo['body']['district_id'];
            }
        }

        $now = time();
        $params['created_by'] = $params['user_id'];
        $params['created_at'] = $now;
        $params['updated_at'] = $now;

        $params['gid'] = Cache_Redis::factory()->incr('TicketTemplatebaseGid');
        if(empty($params['gid'])) {
            $params['gid'] = microtime();
            $params['gid'] = substr($params['gid'],11).substr($params['gid'],2,5).mt_rand(10,99);
        }
        $data = array();
        foreach($items as $item){
            $tmp = $params;
            !isset($item['sale_price']) && Lang_Msg::error("ERROR_AddGenerate_25");
            !isset($item['type']) && Lang_Msg::error("ERROR_AddGenerate_27");
            $tmp['sale_price'] = doubleval($item['sale_price']);
            $tmp['listed_price'] = (isset($item['listed_price']) && doubleval($item['listed_price']))>0?doubleval($item['listed_price']):$tmp['sale_price'];
            $tmp['type'] = $item['type'];

            $tmp['mult_enter'] = (isset($item['mult_enter']) && intval($item['mult_enter']))?1:0; //是否允许多次入园:0否1是
            $tmp['enter_by_card'] = (isset($item['enter_by_card']) && $tmp['mult_enter'] && intval($item['enter_by_card'])) ? 1:0; //是否允许使用身份证入园:0否1是
            $tmp['num_by_card'] = (isset($item['num_by_card']) && $tmp['enter_by_card']) ? intval($item['num_by_card']):0; //使用身份证可购买张数,0不限
            $tmp['enter_by_qrcode'] = (isset($item['enter_by_qrcode']) && $tmp['mult_enter'] && intval($item['enter_by_qrcode'])) ? 1:0;; //是否允许使用二维码入园:0否1是
            $tmp['poi_enter_num'] = (isset($item['poi_enter_num']) && $tmp['mult_enter'] && intval($item['poi_enter_num'])) ? intval($item['poi_enter_num']):1; //每个景点在使用有效期内可使用次数
            isset($item['enter_by_palm']) && $tmp['enter_by_palm'] = intval($item['enter_by_palm']); //是否允许掌纹

            $data[] = $tmp;
        }
        array_unshift($data,array_keys(reset($data)));
        $r = TicketTemplateBaseModel::model()->add($data);
        !$r && Lang_Msg::error("ERROR_AddGenerate_24");
        Tools::lsJson(true,'ok');
    }

    /*
     * 更改基础票（批量）
     * @author: zhaqinfeng
     * */
    public function updateBatchAction() {
        $where = array('is_del'=>0);
        $gid = trim(Tools::safeOutput($this->body['gid']));
        $ids = trim(Tools::safeOutput($this->body['ids']));
        if(!$gid && !$ids) Lang_Msg::error('ERROR_TICKET_1');
        $gid && $where['gid'] = $gid;
        $ids && $where['id|in'] = explode(',',$ids);

        $organization_id = intval( $this->body[ 'organization_id' ] );
        $organization_id && $where['organization_id'] = $organization_id;

        $data = TicketTemplateBaseModel::model()->search($where);
        !$data && Lang_Msg::error('基础票不存在');

        $params = $this->getOperator();
        isset($this->body['name']) && $params['name'] = trim(Tools::safeOutput($this->body['name']));
        isset($this->body['organization_id']) && $params['organization_id'] = intval($this->body['organization_id']);
        isset($this->body['scenic_id']) && $params['scenic_id'] = intval($this->body['scenic_id']);
        isset($this->body['view_point']) && $params['view_point'] = trim(Tools::safeOutput($this->body['view_point']));
        isset($this->body['valid']) && $params['valid'] = intval($this->body['valid']); //门票有效期，预定后多少天内有效
        isset($this->body['valid_flag']) && $params['valid_flag'] = intval($this->body['valid_flag']); //预定后是否一直有效 0否 1是
        isset($this->body['scheduled_time']) && $params['scheduled_time'] = intval($this->body['scheduled_time']); //需提前X天/X小时预定
        if(isset($this->body['date_available'])) {
            //可玩日期  int(11),int(11) 表示一个时间段 ，逗号分隔
            $params['date_available'] = trim(Tools::safeOutput($this->body['date_available']));
            $params['expire_start'] = reset(explode(',',$params['date_available']));
            $params['real_expire_end'] = $params['expire_end'] = end(explode(',',$params['date_available']));
        }
        isset($this->body['week_time']) && $params['week_time'] = trim(Tools::safeOutput($this->body['week_time'])); //周几使用
        isset($this->body['province_id']) && $params['province_id'] = intval($this->body['province_id']);
        isset($this->body['city_id']) && $params['city_id'] = intval($this->body['city_id']);
        isset($this->body['district_id']) && $params['district_id'] = intval($this->body['district_id']);
        isset($this->body['remark']) && $params['remark'] = trim(Tools::safeOutput($this->body['remark']));
        isset($this->body['state']) && $params['state'] = null !== $this->body['state'] ? intval($this->body['state']) : 2;
        isset($this->body['is_del']) && $params['is_del'] = intval($this->body['is_del'])?1:0;
        isset($this->body['source_type']) && $params['source_type'] = intval($this->body['source_type'])?1:0;

        $TicketTemplateBaseModel = new TicketTemplateBaseModel();
        $ids = array_keys($data);
        if(isset($this->body['is_del']) && $params['is_del']==1){ //检查门票是否有上架产品
            if($TicketTemplateBaseModel->haveProduct($ids)){
                Lang_Msg::error('有包含所选门票的上架产品，所选门票不能删除');
            }
        }

        $items = json_decode($this->body['items'],true);

        isset($this->body['name']) && !$params['name'] && Lang_Msg::error("ERROR_AddGenerate_1");
        isset($this->body['scenic_id']) && !$params['scenic_id'] && Lang_Msg::error("ERROR_AddGenerate_5");
        isset($this->body['view_point']) && !$params['view_point'] && Lang_Msg::error("ERROR_AddGenerate_6");
        isset($this->body['items']) && !$items && Lang_Msg::error("ERROR_AddGenerate_26");

        $info = reset($data);
        if(isset($params['scenic_id']) && isset($params['province_id']) && $info['scenic_id'] != $params['scenic_id'] && !$params['province_id']){
            $scenicInfo = ScenicModel::model()->getScenicInfo(array('id'=>$params['scenic_id']));
            if($scenicInfo && !empty($scenicInfo['body'])) { //获取景区省市区id保存到门票
                $params['province_id'] = $scenicInfo['body']['province_id'];
                $params['city_id'] = $scenicInfo['body']['city_id'];
                $params['district_id'] = $scenicInfo['body']['district_id'];
            }
        }

        $now = time();
        $params['updated_at'] = $now;

        $TicketTemplateBaseModel->begin();

        if($items){ //逐条更新
            $upItems = array(); //需更新的门票
            $addItems = array(); //需新增的门票
            foreach($items as $id=>$item){
                $item['mult_enter'] = (isset($item['mult_enter']) && intval($item['mult_enter']))?1:0; //是否允许多次入园:0否1是
                $item['enter_by_card'] = (isset($item['enter_by_card']) && $item['mult_enter'] && intval($item['enter_by_card'])) ? 1:0; //是否允许使用身份证入园:0否1是
                $item['num_by_card'] = (isset($item['num_by_card']) && $item['enter_by_card']) ? intval($item['num_by_card']):0; //使用身份证可购买张数,0不限
                $item['enter_by_qrcode'] = (isset($item['enter_by_qrcode']) && $item['mult_enter'] && intval($item['enter_by_qrcode'])) ? 1:0;; //是否允许使用二维码入园:0否1是
                $item['poi_enter_num'] = (isset($item['poi_enter_num']) && $item['mult_enter'] && intval($item['poi_enter_num'])) ? intval($item['poi_enter_num']):1; //每个景点在使用有效期内可使用次数
                $item['enter_by_palm'] = (isset($item['mult_enter']) && $item['enter_by_palm']) ? intval($item['enter_by_palm']):0; //掌纹

                $item['id'] && $upItems[$item['id']] = $item;
                !$item['id'] && $addItems[] = $item;
            }

            $delIds = array_diff($ids,array_keys($upItems)); //需删除的门票
            if($delIds){
                if($TicketTemplateBaseModel->haveProduct($delIds)){
                    Lang_Msg::error('有包含所选门票的上架产品，所选门票不能删除');
                }
                $r= $TicketTemplateBaseModel->update(array('is_del'=>1),array('id|in'=>$delIds));
                if(!$r) {
                    $TicketTemplateBaseModel->rollback();
                    Lang_Msg::error("ERROR_AddGenerate_24");
                }
            }

            foreach($upItems as $id=>$item){ //需更新的门票
                $tmp = $params;
                !isset($item['sale_price']) && Lang_Msg::error("ERROR_AddGenerate_25");
                !isset($item['type']) && Lang_Msg::error("ERROR_AddGenerate_27");
                $tmp['sale_price'] = doubleval($item['sale_price']);
                $tmp['type'] = $item['type'];
                $tmp['mult_enter'] = $item['mult_enter'];
                $tmp['enter_by_card'] = $item['enter_by_card'];
                $tmp['num_by_card'] = $item['num_by_card'];
                $tmp['enter_by_qrcode'] = $item['enter_by_qrcode'];
                $tmp['poi_enter_num'] = $item['poi_enter_num'];
                $tmp['enter_by_palm'] = $item['enter_by_palm'];


                $r = $TicketTemplateBaseModel->update($tmp,array('id'=>$item['id']));
                if(!$r) {
                    $TicketTemplateBaseModel->rollback();
                    Lang_Msg::error("ERROR_AddGenerate_24");
                }
            }

            if($addItems){  //需新增的门票
                $newData = array();
                foreach($addItems as $item){
                    $tmpN = array_merge($info,$params);
                    unset($tmpN['id']);
                    !isset($item['sale_price']) && Lang_Msg::error("ERROR_AddGenerate_25");
                    !isset($item['type']) && Lang_Msg::error("ERROR_AddGenerate_27");
                    $tmpN['sale_price'] = doubleval($item['sale_price']);
                    $tmpN['listed_price'] = (isset($item['listed_price']) && doubleval($item['listed_price']))>0?doubleval($item['listed_price']):$tmpN['sale_price'];
                    $tmpN['type'] = $item['type'];
                    $tmpN['mult_enter'] = $item['mult_enter'];
                    $tmpN['enter_by_card'] = $item['enter_by_card'];
                    $tmpN['num_by_card'] = $item['num_by_card'];
                    $tmpN['enter_by_qrcode'] = $item['enter_by_qrcode'];
                    $tmpN['poi_enter_num'] = $item['poi_enter_num'];
                    $tmpN['enter_by_palm'] = $item['enter_by_palm'];

                    $newData[] = $tmpN;
                }
                array_unshift($newData,array_keys(reset($newData)));
                $r = $TicketTemplateBaseModel->add($newData);
                if(!$r) {
                    $TicketTemplateBaseModel->rollback();
                    Lang_Msg::error("ERROR_AddGenerate_24");
                }
            }
        }
        else { //批量更新
            $r= $TicketTemplateBaseModel->update($params,$where);
            if(!$r) {
                $TicketTemplateBaseModel->rollback();
                Lang_Msg::error("ERROR_AddGenerate_24");
            }
        }
        $TicketTemplateBaseModel->commit();
        Tools::lsJson(true,'ok');
    }

    /**
     * 发布基础票
     * author : yinjian
     */
    public function addGenerateAction()
    {
        // 验证参数
        !Validate::isUnsignedInt($this->body['organization_id']) && Lang_Msg::error("ERRO_TICKET_3"); //没有机构ID参数
        !Validate::isString($this->body['name']) && Lang_Msg::error("ERROR_AddGenerate_1");
        isset($this->body['fat_price']) && !Validate::isPrice(floatval($this->body['fat_price'])) && Lang_Msg::error("ERROR_AddGenerate_2");
        isset($this->body['group_price']) && !Validate::isPrice(floatval($this->body['group_price'])) && Lang_Msg::error("ERROR_AddGenerate_3");
        isset($this->body['valid']) && !Validate::isUnsignedInt($this->body['valid']) && Lang_Msg::error("ERROR_AddGenerate_4");
        !Validate::isUnsignedInt($this->body['scenic_id']) && Lang_Msg::error("ERROR_AddGenerate_5");
        !Validate::isString($this->body['view_point']) && Lang_Msg::error("ERROR_AddGenerate_6");
        isset($this->body['scheduled_time']) && !Validate::isString($this->body['scheduled_time']) && Lang_Msg::error("ERROR_AddGenerate_7");
        isset($this->body['date_available']) && !Validate::isString($this->body['date_available']) && Lang_Msg::error("ERROR_AddGenerate_8");
        isset($this->body['week_time']) && !Validate::isString($this->body['week_time']) && Lang_Msg::error("ERROR_AddGenerate_9");
        isset($this->body['remark']) && !Validate::isString($this->body['remark']) && Lang_Msg::error("ERROR_AddGenerate_12");
        if(isset($this->body['mini_buy']) && intval($this->body['mini_buy'])<=0) Lang_Msg::error("ERROR_AddGenerate_16");
        if(isset($this->body['mini_buy']) && !Validate::isUnsignedInt($this->body['mini_buy'])) Lang_Msg::error("ERROR_AddGenerate_17");
        if(isset($this->body['max_buy']) && !Validate::isUnsignedInt($this->body['max_buy'])) Lang_Msg::error("ERROR_AddGenerate_18");
        !Validate::isUnsignedId($this->body['user_id']) && Lang_Msg::error("ERROR_AddGenerate_22");
        if(isset($this->body['is_fit']) && !in_array(intval($this->body['is_fit']),array(0,1))) Lang_Msg::error('是否散客参数出错');
        if(isset($this->body['is_full']) && !in_array(intval($this->body['is_full']),array(0,1))) Lang_Msg::error('是否团客参数出错');
        //微信发的票加个标记
        $ota_type = null !== $this->body['ota_type'] ? $this->body[ 'ota_type' ] : 'system';
        //上下架
        $state = null !== $this->body[ 'state' ] ? $this->body[ 'state' ] : 2;
        // 省市区关联暂不验证 @TODO
        // 发布票模板
        $ticketTemplateBaseModel = new TicketTemplateBaseModel();
        $now = time();
        $date_available = trim($this->body['date_available'])?trim($this->body['date_available']):0;
        $arr_date_available = explode(',',$date_available);

        $mult_enter = (isset($this->body['mult_enter']) && intval($this->body['mult_enter'])) ?1:0; //是否允许多次入园:0否1是
        $enter_by_card = (isset($this->body['enter_by_card']) && $mult_enter && intval($this->body['enter_by_card']))?1:0;  //是否允许使用身份证入园:0否1是
        $enter_by_qrcode = (isset($this->body['enter_by_qrcode']) && $mult_enter && intval($this->body['enter_by_qrcode']))?1:0;  //是否允许使用二维码入园:0否1是

        $data = array(
            'organization_id' => intval($this->body['organization_id']),
            'name' => $this->body['name'],
            'scenic_id' => intval($this->body['scenic_id']),
            'view_point' => $this->body['view_point'],
            'fat_price' => floatval($this->body['fat_price']),
            'group_price' => floatval($this->body['group_price']),
            'sale_price' => floatval($this->body['sale_price'])>0?floatval($this->body['sale_price']):0,
            'listed_price' => floatval($this->body['listed_price'])>0?floatval($this->body['listed_price']):0,
            'scheduled_time' => intval($this->body['scheduled_time']),
            'mini_buy' => intval($this->body['mini_buy'])?intval($this->body['mini_buy']):1,
            'max_buy' => intval($this->body['max_buy'])?intval($this->body['max_buy']):100,
            'type' => intval($this->body['type']),
            'remark' => trim($this->body['remark'])?trim($this->body['remark']):'',
            'date_available' => $date_available,
            'valid' => intval($this->body['valid']),
            'payment' => '5,6,7',
            'week_time' => trim($this->body['week_time'])?trim($this->body['week_time']):'',
            'created_by' => intval($this->body['user_id']),
            'created_at' => $now,
            'updated_at' => $now,
            'province_id' => intval($this->body['province_id'])>0 ? intval($this->body['province_id']):0,
            'city_id' => intval($this->body['city_id'])>0 ? intval($this->body['city_id']):0,
            'district_id' => intval($this->body['district_id'])>0 ? intval($this->body['district_id']):0,
            'is_fit' => $ota_type == "weixin" ? 1:intval($this->body['is_fit']),
            'is_full' => intval($this->body['is_full']),
            'rule_id' => intval($this->body['rule_id'])>0 ? intval($this->body['rule_id']):0,
            'expire_start' => reset($arr_date_available),
            'expire_end' => end($arr_date_available),
            'ota_type' => $ota_type,
            'state' => $state,
            'is_infinite'=>isset($this->body['is_infinite'])?$this->body['is_infinite']:0,
            'valid_flag' => intval($this->body['valid_flag']),
            'mult_enter'=> $mult_enter,
            'enter_by_card'=>$enter_by_card,
            'num_by_card'=> (isset($this->body['num_by_card']) && $enter_by_card) ?intval($this->body['num_by_card']):0, //使用身份证可购买张数,0不限
            'enter_by_qrcode'=>$enter_by_qrcode,
            'poi_enter_num'=> (isset($this->body['poi_enter_num']) && $mult_enter && intval($this->body['poi_enter_num'])) ? intval($this->body['poi_enter_num']) : 1,
            'enter_by_palm'=> isset($this->body['enter_by_palm']) ? intval($this->body['enter_by_palm']) : 0,
        );
        if(isset($this->body['source_type'])){
            $data['source_type'] = intval($this->body['source_type'])?1:0;
        }
        $data['listed_price'] = $data['listed_price']>0?$data['listed_price']:$data['sale_price'];
        $data['gid'] = Cache_Redis::factory()->incr('TicketTemplatebaseGid');
        if(empty($data['gid'])) {
            $data['gid'] = microtime();
            $data['gid'] = substr($data['gid'],11).substr($data['gid'],2,5).mt_rand(10,99);
        }

        if($data['scenic_id'] && !$data['province_id']){
            $scenicInfo = ScenicModel::model()->getScenicInfo(array('id'=>$data['scenic_id']));
            if($scenicInfo && !empty($scenicInfo['body'])) {
                $data['province_id'] = $scenicInfo['body']['province_id'];
                $data['city_id'] = $scenicInfo['body']['city_id'];
                $data['district_id'] = $scenicInfo['body']['district_id'];
            }
        }

        $data = $data + $this->getOperator();
        $data['real_expire_end'] = $data['expire_end']-$data['scheduled_time'];
        if($data['date_available']==0 || $this->body['is_infinite']==1){
            $data['expire_start'] = 0;
            $data['expire_end'] = 4294967295;
            $data['is_infinite'] = 1;
            $data['real_expire_end'] = 2147483647;
        }
        if($data['scenic_id'] && !$data['province_id']){
            $scenicInfo = ScenicModel::model()->getScenicInfo(array('id'=>$data['scenic_id']));
            if($scenicInfo && !empty($scenicInfo['body'])) {
                $data['province_id'] = $scenicInfo['body']['province_id'];
                $data['city_id'] = $scenicInfo['body']['city_id'];
                $data['district_id'] = $scenicInfo['body']['district_id'];
            }
        }
        $res = $ticketTemplateBaseModel->add($data);
        !$res && Lang_Msg::error("ERROR_AddGenerate_24");
        Tools::lsJson(true,'ok',array('id'=>$ticketTemplateBaseModel->getInsertId()));
    }

    /**
     * 库存列表
     * author : yinjian
     */
    public function store_listAction()
    {
        intval($this->body['organization_id'])<1 && Lang_Msg::error('机构id不能为空');
        !in_array(intval($this->body['type']),array(0,1,2)) && Lang_Msg::error('类型不正确');
        $organization_id = $this->body['organization_id'];
        $type = intval($this->body['type']);
        // 联票单独处理
        if(intval($this->body['type'] == 2)){
            $this->store_union($organization_id);
            exit();
        }
        // 电子票和任务单搜索单独处理
        if(isset($this->body['scenic_id']) && intval($this->body['scenic_id'])>0){
            $this->store_list_search($this->body['scenic_id']);
            exit();
        }
        // 统计数目
        $count_where['type'] = $type;
        $count_where['is_del'] = 0;
        $count_where['state'] = 0;
        $count_where['organization_id'] = $organization_id;
        $count_where['is_union'] = 0;
        $this->count = TicketTemplateBaseModel::model()->countResult($count_where);
        parent::pagenation();
        // 景区id 按时间排序
        $where = "type=".$type." AND state=0 AND is_union=0 AND organization_id = ".$organization_id." group by scenic_id order by max(created_at) desc";
        $scenic_ids_arr = TicketTemplateBaseModel::model()->search($where,'scenic_id,id');
        $scenic_ids = array();
        // 景区id格式规整
        foreach($scenic_ids_arr as $v){
            $scenic_ids[] = $v['scenic_id'];
        }
        $scenic_ids_str = join(',',$scenic_ids);
        // 门票
        $ticket_template_sql = "organization_id=".$organization_id." AND is_union=0 AND type=".$type." AND state=0 AND is_del=0 AND scenic_id in (".$scenic_ids_str.") ORDER BY find_in_set(scenic_id,'".$scenic_ids_str."')";
        $ticket_template = TicketTemplateBaseModel::model()->search($ticket_template_sql,'*',null,$this->limit);
        foreach($ticket_template as $v){
            $scenic_ticket_arr['data'][$v['scenic_id']][] = $v;
        }
        $scenic_ticket_arr['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true,'ok',$scenic_ticket_arr);
    }

    /**
     * 搜索某个景区门票
     * author : yinjian
     */
    private function store_list_search($scenic_id)
    {
        $where = array(
            'scenic_id' => $scenic_id,
            'organization_id' => intval($this->body['organization_id']),
            'is_del' => 0,
            'state' => 0,
            'type' => intval($this->body['type']),
        );
        $this->count = TicketTemplateBaseModel::model()->countResult($where);
        parent::pagenation();
        $data['data'][$scenic_id] = TicketTemplateBaseModel::model()->search($where,'*',$this->getSortRule(),$this->limit);
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true,'ok',$data);
    }

    /**
     * 联票库存 @todo
     * author : yinjian
     */
    private function store_union($organization_id)
    {
        $where = 'organization_id = '.$organization_id.' AND is_union = 1 AND is_del=0 AND state=0';
        Validate::isString($this->body['name']) && $where.=' AND name like \'%'.$this->body['name'].'%\'';
        intval($this->body['scenic_id'])>0 && $where.=' AND find_in_set('.intval($this->body['scenic_id']).',scenic_id)';
        $this->count = TicketTemplateBaseModel::model()->countResult($where);
        parent::pagenation();
        $data['data'] = TicketTemplateBaseModel::model()->search($where,'*',$this->getSortRule(),$this->limit);
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true,'ok',$data);
    }

    /**
     * 门票预订列表
     * author : yinjian
     * modify by zhaqinfeng 2014-11-11
     */
    public function reserve_listAction()
    {
        $where = " is_del=0 and ota_type = 'system' ";
        $now = time();

        $organization_id = intval($this->body['organization_id']);
        $organization_id = $organization_id ? $organization_id : $this->body['or_id'];
        if(preg_match("/^[\d,]+$/",$organization_id)) {
            $where.=" AND organization_id IN (".$organization_id.")";
        }

        // 地区
        if($this->body['province_id'] && !$this->body['city_id'] && !$this->body['district_id']){    // 省份筛选
            $where.=" AND province_id IN (".trim(Tools::safeOutput($this->body['province_id'])).") ";
        }elseif($this->body['city_id'] && !$this->body['district_id']){    // 市筛选            // 市筛选
            $where.=" AND city_id IN (".trim(Tools::safeOutput($this->body['city_id'])).") ";
        }elseif(isset($this->body['district_id']) && Validate::isUnsignedId($this->body['district_id'])){   // 区筛选
            $where.=" AND district_id IN (".trim(Tools::safeOutput($this->body['district_id'])).") ";
        }

        if(isset($this->body['source_type'])) $where.= " AND source_type = ".intval($this->body['source_type'])." ";

        if(isset($this->body['state']) && in_array(intval($this->body['state']), array(0,1)))       // 上下架
            $where.= " AND state=".intval($this->body['state'])." ";

        if(isset($this->body['name']) && Validate::isString($this->body['name']))       // 名称模糊查询
            $where.= " AND name LIKE '%".trim(Tools::safeOutput($this->body['name']))."%' ";

        if(isset($this->body['scenic_id']) && Validate::isString($this->body['scenic_id']))        // 景区列表
            $where.=" AND scenic_id IN (".intval($this->body['scenic_id']).") ";

        $types = trim(Tools::safeOutput($this->body['types']));
        $types && preg_match("/^[\d,]+$/",$types) && $where.= " AND type IN (".$types.") ";

        if($this->body['use_time']) $where .= ' and expire_start<='.$now.' and (real_expire_end=0 or real_expire_end>='.$now.') and FIND_IN_SET( '.date('w').', week_time)';
        // fix 当天票
        $today = strtotime(date('Y-m-d'));
        if(!isset($this->body['expire_end']) || !$this->body['expire_end'])        // 筛选过有效期的门票，1显示过期，0不显示（默认）
            $where.= " AND (real_expire_end=0 or real_expire_end>=".$today.") ";

        $TicketTemplateBaseModel = TicketTemplateBaseModel::model();
        $this->count = $TicketTemplateBaseModel->countResult($where);
        $this->pagenation();
        $data = $this->count>0  ?$TicketTemplateBaseModel->search($where,'*',$this->getSortRule(),$this->limit) : array();


        $show_scenicname = intval($this->body['show_scenic_name']);
        $show_poiname = intval($this->body['show_poi_name']);
        if($show_scenicname || $show_poiname){
            $scenicIds = $poiIds = $secnicList = $poiList = array();
            foreach($data as $v){
                $show_scenicname && $v['scenic_id'] && $scenicIds[] = $v['scenic_id'];
                $show_poiname && $v['view_point'] && $poiIds[] = $v['view_point'];
            }
            if($show_scenicname){
                $scenicIds = array_unique(explode(',',implode(',',$scenicIds)));
                sort($scenicIds);
                $scenics = ScenicModel::model()->getScenicList(array('ids'=>implode(',',$scenicIds),'items'=>count($scenicIds)));
                if(isset($scenics['body']['data'])){
                    foreach ($scenics['body']['data'] as $sv) {
                        $secnicList[$sv['id']]=$sv['name'];
                    }
                }
            }
            if($show_poiname){
                $poiIds = array_unique(explode(',',implode(',',$poiIds)));
                sort($poiIds);
                $pois = ScenicModel::model()->getPoiList(array('ids'=>implode(',',$poiIds),'items'=>count($poiIds),'fields'=>'id,name','sort_by'=>'id:asc'));
                if(isset($pois['body']['data'])){
                    foreach ($pois['body']['data'] as $sv) {
                        $poiList[$sv['id']]=$sv['name'];
                    }
                }
            }
            foreach($data as $k=>$v){
                if($show_scenicname && $secnicList) {
                    $v['scenic_id'] = explode(',',$v['scenic_id']);
                    sort($v['scenic_id']);
                    $data[$k]['scenic_id'] = implode(',',$v['scenic_id']);
                    $data[$k]['scenic_name'] = implode(',',array_intersect_key($secnicList,array_flip($v['scenic_id'])));
                }
                if($show_poiname && $poiList) {
                    $v['view_point'] = explode(',',$v['view_point']);
                    sort($v['view_point']);
                    $data[$k]['view_point'] = implode(',',$v['view_point']);
                    $data[$k]['poi_name'] = implode(',',array_intersect_key($poiList,array_flip($v['view_point'])));
                }
            }
        }

        $result = array(
            'data'=>array_values($data),
            'pagination'=>array( 'count'=>$this->count, 'current'=>$this->current, 'items'=>$this->items, 'total'=>$this->total )
        );
        $result['types'] = TicketTypeModel::model()->getAll();
        Tools::lsJson(true,'ok',$result);
    }
	
    /**
     * 修改票模板机构ID
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function updateOrganizationIdAction() {
        $scenic_id = intval($this->body['scenic_id']);
        !$scenic_id && Lang_Msg::error("ERROR_LANDSCAPE_1"); //缺少景区ID参数
        $organization_id = intval($this->body['organization_id']);
        !$organization_id && Lang_Msg::error("缺少机构ID参数");
        $new_organization_id = intval($this->body['new_organization_id']);
        !$new_organization_id && Lang_Msg::error("缺少新机构ID参数");

        TicketTemplateBaseModel::model()->update(['organization_id'=>$new_organization_id], ['organization_id'=>$organization_id, 'scenic_id'=>$scenic_id]);
        TicketTemplateItemModel::model()->update(['base_org_id'=>$new_organization_id], ['base_org_id'=>$organization_id, 'scenic_id'=>$scenic_id]);

        Tools::lsJson(true,'ok',$result);
	}
}