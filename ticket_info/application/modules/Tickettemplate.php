
<?php

class TickettemplateController extends Base_Controller_Api
{	
	//根据景区id,机构ID来显示列表，分页显示 
    public  function listsAction() {
        $where = array();
        $is_del = intval($this->body['is_del']);
        $is_del>0 && $where['is_del'] = 1;
        $is_del==0 && $where['is_del'] = 0;

        $organization_id = intval($this->body['organization_id']);
        $organization_id = $organization_id ? $organization_id : intval($this->body['or_id']);
        $organization_id && $where['organization_id'] = $organization_id;

        if(isset($this->body['p']) && $this->body['p']>=0)
            $this->current = intval($this->body['p']);

        $ota_type = trim(Tools::safeOutput($this->body['ota_type']));
        $ota_type = $ota_type ? $ota_type:'system';
        $where['ota_type'] = $ota_type;


        $scenic_name = trim(Tools::safeOutput($this->body[ 'scenic_name']));
        $scenic_id = trim($this->body[ 'scenic_id']);
        $view_point = trim($this->body['view_point']);
        $province_id = trim($this->body[ 'province_id']);
        $city_id = trim($this->body[ 'city_id']);
        $district_id = trim($this->body[ 'district_id']);
        $piWhere = array();
        if($scenic_name){
            $piWhere['sceinc_name|like'] = array("%{$scenic_name}%");
        }
        if($scenic_id && preg_match("/^[\d,]+$/",$scenic_id)) {
            $piWhere['scenic_id|in'] = explode( ',', $scenic_id );
        }
        if($view_point && preg_match("/^[\d,]+$/",$view_point)) {
            foreach(explode( ',', $view_point ) as $poi){
                $piWhere['FIND_IN_SET|EXP'] = "({$poi},view_point)";
            }
        }
        if($province_id && preg_match("/^[\d,]+$/",$province_id)){
            $piWhere['province_id|in'] = explode( ',', $province_id );
        }
        if($city_id && preg_match("/^[\d,]+$/",$city_id)){
            $piWhere['city_id|in'] = explode( ',', $city_id );
        }
        if($district_id && preg_match("/^[\d,]+$/",$district_id)){
            $piWhere['district_id|in'] = explode( ',', $district_id );
        }
        if($piWhere) {
            $productItems = TicketTemplateItemModel::model()->search($piWhere,"product_id");
            $productIds = array();
            foreach($productItems as $iv) {
                $productIds[] = $iv['product_id'];
            }
            $productIds && $where['id|in']= $productIds;
            !$productIds && Lang_Msg::output(array('data'=>array(),'pagination'=>array('count'=>0)));
        }

        $state = intval($this->body[ 'state' ]);
        if(preg_match("/^\d+$/",$this->body[ 'state' ])) $where['state'] = $state;
        else if($this->body['state']) $where['state|>'] = 0;

        $type = intval($this->body[ 'type' ]);
        if(in_array($type,array(0,1))) $where['type'] = $type;
        else $where['is_union'] = 1;

        $is_union = intval($this->body[ 'is_union' ]);
        (isset($this->body[ 'is_union' ]) && $is_union>=0) && $where['is_union'] = $is_union>0?1:0;

        $name = trim(Tools::safeOutput($this->body['name']));
        $name && $where['name|like'] = array("%{$name}%");

        $ids = trim(Tools::safeOutput($this->body['ids']));
        preg_match("/^[\d,]+$/",$ids) && $ids = explode(',',$ids);
        $ids && $where['id|in']= $ids;

        $show_all = intval($this->body['show_all']);
        if($show_all) {
            $data = TicketTemplateModel::model()->search( $where, $this->getFields('id'), $this->getSortRule('updated_at'));
        } else {
            $this->count = TicketTemplateModel::model()->countResult($where);
            $this->pagenation();
            $data = $this->count > 0 ? TicketTemplateModel::model()->search( $where, $this->getFields('id'), $this->getSortRule('updated_at'), $this->limit ):array();
        }

        $show_items = intval($this->body['show_items']);
        if($show_items && $data){
            $productItems = TicketTemplateItemModel::model()->search(array('product_id|in'=>array_keys($data)));
            foreach($productItems as $v){
                $data[$v['product_id']]['items'][] = $v;
            }
        }
        $show_policy_name = intval($this->body['show_policy_name']);
        if($show_policy_name){
            $policyIds = array();
            foreach($data as $k=>$v){
                $v['policy_id'] && $policyIds[] = $v['policy_id'];
            }
            if($policyIds){
                $policyIds = array_unique($policyIds);
                $policyList = TicketPolicyModel::model()->setCd(2)->getByIds($policyIds);
                foreach($data as $k=>$v){
                    $data[$k]['policy_name'] = '';
                    $v['policy_id'] && $data[$k]['policy_name'] = isset($policyList[$v['policy_id']]['name'])?$policyList[$v['policy_id']]['name']:'';
                }
            }
        }

        $return = array('data'=>$data);
        if($show_all){
            $return['pagination'] = array('count'=>count($data));
        } else {
            $return['pagination'] = array('count'=>$this->count, 'current'=>$this->current, 'items'=>$this->items, 'total'=>$this->total);
        }
        Lang_Msg::output($return);
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
        $this->count = TicketTemplateModel::model()->countResult($count_where);
        parent::pagenation();
        // 景区id 按时间排序
        $where = "type=".$type." AND state=0 AND is_union=0 AND organization_id = ".$organization_id." group by scenic_id order by max(created_at) desc";
        $scenic_ids_arr = TicketTemplateModel::model()->search($where,'scenic_id,id');
        $scenic_ids = array();
        // 景区id格式规整
        foreach($scenic_ids_arr as $v){
            $scenic_ids[] = $v['scenic_id'];
        }
        $scenic_ids_str = join(',',$scenic_ids);
        // 门票
        $ticket_template_sql = "organization_id=".$organization_id." AND is_union=0 AND type=".$type." AND state=0 AND is_del=0 AND scenic_id in (".$scenic_ids_str.") ORDER BY find_in_set(scenic_id,'".$scenic_ids_str."')";
        $ticket_template = TicketTemplateModel::model()->search($ticket_template_sql,'*',null,$this->limit);
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
        $this->count = TicketTemplateModel::model()->countResult($where);
        parent::pagenation();
        $data['data'][$scenic_id] = TicketTemplateModel::model()->search($where,'*',$this->getSortRule(),$this->limit);
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
        $this->count = TicketTemplateModel::model()->countResult($where);
        parent::pagenation();
        $data['data'] = TicketTemplateModel::model()->search($where,'*',$this->getSortRule(),$this->limit);
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true,'ok',$data);
    }


    //根据票ID来获取票信息
    public  function ticketinfoAction()
    {
        //票ID
        $where = array();
        $ticket_id = intval($this->body[ 'ticket_id' ]);
        $ticket_id && $where[ 'id' ]= $ticket_id;

        $ota_code = trim(Tools::safeOutput($this->body[ 'ota_code' ]));
        $ota_code && $where[ 'ota_code' ]= $ota_code;

        $ticket_id<=0 && !$ota_code && Lang_Msg::error( 'ERROR_TICKET_1' );

        $is_del = intval($this->body[ 'is_del' ]);
        if($is_del !=2) $where[ 'is_del' ] =$is_del;
        //分销商ID
        $return = TicketTemplateModel::model()->get( $where );
        if( !$return ) Lang_Msg::error( '票不存在！');
        $return['items'] = TicketTemplateItemModel::model()->search(array('product_id'=>$return['id']));
        //如果有分销商ID

        $return['payment'] = '1,2,3,4'; //默认都有支付权限，再由策略限制
        $distributor_id = intval($this->body['distributor_id']);
        $use_day = trim($this->body['use_day']);
        if($distributor_id) {
            $now = time();
            //type => 0 散客 ， type=> 1团客
            $type = $this->body[ 'type' ]!=null ? $this->body[ 'type' ]:( $this->body[ 'price_type' ]!=null? $this->body[ 'price_type' ]:Lang_Msg::error( 'ERRO_TICKET_14' ));
            if($type==0) $return['mini_buy']=1;

            $return[ 'can_buy' ] = true; //散客权限
            if(($return['sale_start_time'] && $now < $return['sale_start_time']) || ($return[ 'sale_end_time'] && $now > $return['sale_end_time'])) {
                $return['can_buy'] = false;
            }

            if(($type==0 && !$return['is_fit']) || ($type==1 && !$return['is_full'])){
                $return[ 'can_buy' ] = false;
            }

            $return[ 'fat_discount' ] = $return[ 'group_discount' ] = 0;

            if($return['policy_id']) { //检查分销商是否有权限订购
                $payment = explode(',',$return['payment']);
                $policyInfo = TicketPolicyModel::model()->getDetail($return['policy_id'],$distributor_id);
                if($policyInfo) { //有分销策略
                    //获取供应商绑定的分销商
                    $distributorIds = ApiOrganizationModel::model()->agencyIdsOfSupplier($return['organization_id'],'distributor_id');
                    $return['policyInfo']= $policyInfo;
                    if(!in_array($distributor_id,$distributorIds)){ //如分销商ID不在与供应商绑定关系内，则看非合作权限
                        unset($policyInfo['items']);
                    }

                    if($policyInfo['items']) {
                        if($policyInfo['items']['blackname_flag']) {
                            $return['can_buy'] = false;
                        } else {
                            $return['can_buy'] = true;
                            $return['fat_discount'] = doubleval($policyInfo['items']['fat_price']);
                            $return['group_discount'] = doubleval($policyInfo['items']['group_price']);
                            //$policyInfo['items']['credit_flag'] && array_push($payment,2);
                            //$policyInfo['items']['advance_flag'] && array_push($payment,3);
                            !$policyInfo['items']['credit_flag'] && $payment = array_diff($payment,array('2'));
                            !$policyInfo['items']['advance_flag'] && $payment = array_diff($payment,array('3'));
                        }
                    } else if($policyInfo['other_blackname_flag']) {
                        $return['can_buy'] = false;
                    } else { //未合作分销售
                        $return['can_buy'] = true;
                        $return['fat_discount'] = doubleval($policyInfo['other_fat_price']);
                        $return['group_discount'] = doubleval($policyInfo['other_group_price']);
                        //$policyInfo['other_credit_flag'] && array_push($payment,2);
                        //$policyInfo['other_advance_flag'] && array_push($payment,3);
                        !$policyInfo['other_credit_flag'] && $payment = array_diff($payment,array('2'));
                        !$policyInfo['other_advance_flag'] && $payment = array_diff($payment,array('3'));
                    }
                } else {
                    $return['can_buy'] = true;
                }
                sort($payment);
                $payment = array_unique($payment);
                $return['payment'] = implode(',',$payment);
            } else {
                $return['can_buy'] = true;
            }

            if($use_day){
                $t = strtotime( $use_day.' 00:00:00' );

                $return['can_play'] = false;
                $play_time = explode( ',', $return[ 'date_available' ] );

                if( $return[ 'state' ] !=1 ) Lang_Msg::error( 'ERRO_TICKET_15' );
                if($play_time && $t >= $play_time[ 0 ] && $t <= $play_time[ 1 ] ) {
                    $return['can_play'] = true;
                }
                if($return['can_play'] && $return['rule_id']){ //检查游玩日期当天的日价格和日库存
                    $ruleInfo = TicketRuleItemModel::model()->search(array('rule_id'=>$return['rule_id'],'date'=>$use_day));
                    if($ruleInfo){
                        $ruleInfo = reset($ruleInfo);
                        $return[ 'day_fat_price' ] = $ruleInfo[ 'fat_price' ];
                        $return[ 'day_group_price' ] = $ruleInfo[ 'group_price' ];
                        if($ruleInfo[ 'reserve' ]>0){
                            $ticketDayUsedReserveKey = 'TicketRuleItem|'.$ticket_id.'|'.$return['rule_id'].'|'.$use_day;
                            $ticketDayUsedReserve = Cache_Redis::factory()->get($ticketDayUsedReserveKey);
                            $return[ 'day_reserve' ] = $ruleInfo[ 'reserve' ];
                            $return[ 'used_reserve' ] = intval($ticketDayUsedReserve);
                            $return[ 'remain_reserve' ] = $ruleInfo[ 'reserve' ]-$return[ 'used_reserve' ];
                        }
                    }
                }
            }
        }

        $return['price'] =  $return[($type==0?'fat_price':'group_price')];
        if($type==0 && isset($return['fat_discount']) && 0!=$return['fat_discount']){
            $return['price'] += $return['fat_discount'];
        }
        else if($type==1 && isset($return['group_discount']) && 0!=$return['group_discount']){
            $return['price'] += $return['group_discount'];
        }

        if($type==0 && isset($return['day_fat_price']) && 0!=$return['day_fat_price']){
            $return['price'] += $return['day_fat_price'];
        }
        else if($type==1 && isset($return['day_group_price']) && 0!=$return['day_group_price']){
            $return['price'] += $return['day_group_price'];
        }
        $return['price']<0 && $return['price']=0;
        Lang_Msg::output( $return );
    }
	
	//修改票信息
	public  function updateAction() {
        $args = $this->getOperator();

        $id = intval( $this->body[ 'id' ] );
		if( !$id )Lang_Msg::error( 'ERROR_TICKET_1' );
		$or_id = intval( $this->body[ 'or_id'] );
		if( !$or_id ) Lang_Msg::error( 'ERRO_TICKET_3' );
		//根据ID来查询
		$return = TicketTemplateModel::model()->getById( $id );
		if( $return[ 'organization_id'] != $or_id )
		{
			Lang_Msg::error('这张票不属于你的机构');
		}
        $args['user_id'] && $return['user_id']=$args['user_id'];
        $args['user_account'] && $return['user_account']=$args['user_account'];
        $args['user_name'] && $return['user_name']=$args['user_name'];

		//获取参数
		if(isset($this->body[ 'name'])) $args[ 'name' ] = $this->body[ 'name'] ;
		if(isset($this->body[ 'fat_price'])) $args[ 'fat_price' ] = $this->body[ 'fat_price'] ;
		if(isset($this->body[ 'group_price'])) $args[ 'group_price' ] = $this->body[ 'group_price'] ;
		if(isset($this->body[ 'sale_price'])) $args[ 'sale_price' ] = $this->body[ 'sale_price'] ;
		if(isset($this->body[ 'listed_price'])) $args[ 'listed_price' ] = $this->body[ 'listed_price'] ;
		if(isset($this->body[ 'valid']))$args[ 'valid' ] = $this->body[ 'valid'] ;
        if(isset($this->body[ 'valid_flag']))$args[ 'valid_flag' ] = intval($this->body[ 'valid_flag']) ; //预定后是否一直有效 0否 1是
		if(isset($this->body[ 'max_buy']))$args[ 'max_buy' ] = $this->body[ 'max_buy'] ;
		if(isset($this->body[ 'mini_buy']))$args[ 'mini_buy' ] = $this->body[ 'mini_buy'] ;
		if(isset($this->body[ 'payment']))$args[ 'payment' ] = $this->body[ 'payment'] ;
		if(isset($this->body[ 'is_fit']))$args[ 'is_fit' ] = $this->body[ 'is_fit' ] ;
		if(isset($this->body[ 'is_full']))$args[ 'is_full' ] = $this->body[ 'is_full'] ;
		if(isset($this->body[ 'scheduled_time']))$args[ 'scheduled_time' ] = $this->body[ 'scheduled_time'] ;
		if(isset($this->body[ 'is_del']))$args[ 'week_time' ] = $this->body[ 'is_del'] ;
		if(isset($this->body[ 'week_time']))$args[ 'week_time' ] = $this->body[ 'week_time'] ;
		if(isset($this->body[ 'refund']))$args[ 'refund' ] = $this->body[ 'refund'] ;
		if(isset($this->body[ 'organization_id']))$args[ 'organization_id' ] = $this->body[ 'or_id'] ;
		if(isset($this->body[ 'type']))$args[ 'type' ] = $this->body[ 'type'] ;
		if(isset($this->body[ 'remark']))$args[ 'remark' ] = $this->body[ 'remark'] ;
        if(isset($this->body[ 'rule_id'])) $args[ 'rule_id' ] = intval($this->body[ 'rule_id']) ;
        if(isset($this->body[ 'policy_id'])) $args[ 'policy_id' ] = intval($this->body[ 'policy_id']) ;
        if(isset($this->body[ 'sale_start_time'])) $args[ 'sale_start_time' ] = intval($this->body[ 'sale_start_time']) ;
        if(isset($this->body[ 'sale_end_time'])) $args[ 'sale_end_time' ] = intval($this->body[ 'sale_end_time']) ;
        if(isset($this->body[ 'sms_template'])) $args[ 'sms_template' ] = trim(Tools::safeOutput($this->body['sms_template']));

        if(isset( $this->body[ 'date_available' ] ))
        {
         	$args[ 'date_available' ] = $this->body['date_available'];
        	$args[ 'expire_start' ] = reset(explode(',',$this->body['date_available']));
            $args[ 'expire_end'] = end(explode(',',$this->body['date_available']));
            $args['real_expire_end'] = $args[ 'expire_end']-$args[ 'scheduled_time' ];
        }


        if( empty( $args ) )Lang_Msg::error('没有需要修改的参数');

        $TicketTemplateModel = new TicketTemplateModel();
        $TicketTemplateModel->begin();

        if(isset($this->body['base_items'])){
            $baseItems = json_decode($this->body['base_items'],true);
            if($baseItems) {
                foreach($baseItems as $base_id=>$num) {
                    !$base_id && Lang_Msg::error('ERROR_AddGenerate_26'); //请添加门票信息
                }
                $base_ids = array_keys($baseItems);
                $baseLists = TicketTemplateBaseModel::model()->getByIds($base_ids);
                if($baseLists) {
                    $scenic_ids = $view_points = $baseOrgIds = array();
                    foreach($baseLists as $k=>$v){
                        $baseLists[$k]['num'] = $baseItems[$v['id']];
                        $scenic_ids[] = $v['scenic_id'];
                        $view_points[] = $v['view_point'];
                        $baseOrgIds[] = $v['organization_id'];
                    }
                    $scenic_ids = array_unique($scenic_ids);
                    $view_points = array_unique(explode(',',implode(',',$view_points)));
                    $baseOrgIds = array_unique($baseOrgIds);
                    $args['scenic_id'] = implode(',',$scenic_ids);
                    $args['view_point'] = implode(',',$view_points);
                    $args['base_org_num'] = count($baseOrgIds);

                    $info = array_merge($return,$args);
                    $r = TicketTemplateItemModel::model()->addList($info, $baseLists);
                    !$r && $TicketTemplateModel->rollback();
                }
            }
        }

        $r = $TicketTemplateModel->updateById( $id, $args );
		if( $r ) {
            //价格修改的时候发送消息
            if(isset( $args[ 'fat_price' ] ) || isset( $args[ 'group_price' ] ) )
            {
                SubscribesModel::model()->sendMsg( $id, $or_id, $args , $return );
            }
            $TicketTemplateModel->commit();
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
		}
		else
		{
			Lang_Msg::error( 'ERRO_TICKET_11' );
		}
	}
	
	//修改上下架
	public function stateAction()
	{ 
		$id = trim( $this->body[ 'id' ] );
		if( !$id )Lang_Msg::error( 'ERROR_TICKET_1' );
        preg_match("/^[\d,]+$/",$id) && $ids = explode(',',$id);
		$or_id = intval( $this->body[ 'or_id'] );
		if( !$or_id ) Lang_Msg::error( 'ERRO_TICKET_3' );
		//check
        $args['state'] = null !==( $this->body[ 'state' ] ) ? intval( $this->body[ 'state'] ):Lang_Msg::error( 'ERRO_TICKET_12' );

        $data = TicketTemplateModel::model()->search( array('id|in'=>$ids) );
        foreach ($data as $detail) {
            if( $detail[ 'organization_id'] != $or_id )
            {
                Lang_Msg::error('产品［'.$detail['name'].'］不是该供应商发布的无法操作');
            }

            //if( $detail['state'] == $args['state'] ) {
            //    Lang_Msg::error('产品［'.$detail['name'].'］当前状态与修改状态一样，不需要重复提交');
            //}
        }
        $data = array();
        if($args['state']==1){ //上架需检查门票是否删除或下架状态
            $productItems = TicketTemplateItemModel::model()->search(array('product_id|in'=>$ids));
            !$productItems && Lang_Msg::error( 'ERROR_AddGenerate_26' );
            $baseIds = array();
            foreach($productItems as $v){
                $baseIds[] = $v['base_id'];
            }
            $baseIds = array_unique($baseIds);
            // 已上架未删除的base_id
            $baseLists = TicketTemplateBaseModel::model()->search(array('id|in'=>$baseIds,'is_del'=>0,'state'=>1));
            //上架失败，该产品存在未上架门票
            if(count($baseLists)<count($baseIds)){
                $diff_base_id = array_diff($baseIds,array_keys($baseLists));
                // 已删除或者未上架的base_id
                $diff_TicketTemplateItem = TicketTemplateItemModel::model()->search(array('base_id|in'=>$diff_base_id,'product_id'=>$ids));
                $diff_product_id = array();
                // 迭代产品id
                foreach($diff_TicketTemplateItem as $k=>$v){
                    $diff_product_id[] = $v['product_id'];
                }
                // 未上架的产品
                $diff_product_id && $data = TicketTemplateModel::model()->search(array('id|in'=>$diff_product_id));
                // 获取可以上架的产品id
                $ids = array_diff($ids,array_keys($data));
            }
        }
        if($ids){
            $r = TicketTemplateModel::model()->updateByAttr( $args,array('id|in'=>$ids) );
            $msg = '';
            if(!$data){
                $msg = '操作成功';
            }else{
                foreach($data as $k=>$v){
                    $msg .= $v['name'].',';
                }
                $msg = trim($msg,',').'有基础票删除不能上架';
            }
            if( $r )
            {
                Tools::lsJson(true,$msg);
            }else{
                Tools::lsJson(true,'操作失败');
            }
        }
		Lang_Msg::error( '你有基础票被删除不能上架' );
	}
	
	//设置分销商价格   接收josn数据格式  data=>{ "city_id":{"agency_id":["fit_price","full_price"]},"city_id1":{"agency_id1":["fit_price1","full_price1"]}}
	public function setfxpAction()
	{
		$id = intval( $this->body[ 'ticket_id' ] );
		if( !$id )Lang_Msg::error( 'ERRO_TICKET_2' );
		$or_id = intval( $this->body[ 'or_id'] );
		if( !$or_id ) Lang_Msg::error( 'ERRO_TICKET_3' );
		//check
		$tmp = TicketAgencyPriceModel::model()->search( ' ticket_id='.$id );
		
		/*
		if( $tmp[key( $tmp )][ 'organization_id'] != $or_id )
		{
			Lang_Msg::error('ERRO_TICKET_3');
		}
		*/
  		$data = null !==( $this->body[ 'data' ] ) ?  $this->body[ 'data' ] :Lang_Msg::error( 'ERRO_TICKET_13' );
  		$info = json_decode( $data );
  		//print_r(json_encode( array( 1=>array(2=>array(3,4) ), 2=> array(3 => array( 4,5 ) ) ))   );
  		$update = array();
  		$k=0;
  		foreach ( $info as $city_id => $value )
  		{
  			foreach( $value as $agency_id => $p )
  			{	
  				$update[ $k ][ 'agency_id'] = $agency_id;
  				$update[ $k ][ 'fit_price'] = $p[ 0 ];
  				$update[ $k ][ 'full_price'] = $p[ 1 ];
  				$update[ $k ][ 'organization_id'] = $or_id;
  				$update[ $k ][ 'ticket_id'] = $id;
  				$update[ $k ][ 'city_id' ] = $city_id;
  				$k++;
  			}
  		}
		$return = TicketAgencyPriceModel::model()->updatefx( $update );
		
	}
	
	
	//添加一个分销商id
	public function addfxpAction()
	{
		$arg[ 'ticket_id' ] = intval( $this->body[ 'ticket_id' ] );
		$arg[ 'organization_id' ] = intval( $this->body[ 'or_id' ]);

		$arg = array_filter( $arg );
		$return =  TicketAgencyPriceModel::model()->insert( $arg );
		if($return) {
			 Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
		}
		else
		{
			Lang_Msg::error( 'ERRO_TICKET_11' );
		}
	}
	
	//根据城市ID来查出分销商
	public  function listByCityAction()
	{
		$or_id	= null !==( $this->body[ 'or_id' ] ) ?  $this->body[ 'or_id' ] :Lang_Msg::error( 'ERRO_TICKET_2' );
		$ticket_id	= null !==( $this->body[ 'ticket_id' ] ) ?  $this->body[ 'ticket_id' ] :Lang_Msg::error( 'ERROR_TICKET_1' );
		$where ='  is_del = 0'.' and organization_id ='.$or_id. ' and ticket_id = '.$ticket_id;
		if(isset( $this->body[ 'city_id' ] ) )
		{
			$city_id = $this->body[ 'city_id' ];
			$where =  ' and city_id = '.$city_id ;
		}
		
		$re = TicketAgencyPriceModel::model()->search(  $where );
		Lang_Msg::output( $re );
	}
	
	
	//删除票数据
	public function deleteAction()
	{
		$id = intval( $this->body[ 'id' ] );
		if( !$id )Lang_Msg::error( 'ERRO_TICKET_2' );
		$or_id = intval( $this->body[ 'or_id'] );
		if( !$or_id ) Lang_Msg::error( 'ERRO_TICKET_3' );
		
		$info = TicketTemplateModel::model()->getById( $id );
		if( !$info ) Lang_Msg::error( '没有个条订单');
		
		//update
		$return = TicketTemplateModel::model()->updateById( $id ,  array( 'is_del' => 1) );
		if( $return )
		{
			 Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
		}
		else
		{
			Lang_Msg::error( 'ERRO_TICKET_11' );
		}
		
	}

    /**
     * 发布电子票
     * author : yinjian
     */
    public function addGenerateAction()
    {
        // 验证参数
        $baseItems = json_decode($this->body['base_items'],true);
        !$baseItems && Lang_Msg::error('ERROR_AddGenerate_26'); //请添加门票信息

        foreach($baseItems as $base_id=>$num){
            !$base_id && Lang_Msg::error('ERROR_AddGenerate_26'); //请添加门票信息
        }

        !Validate::isString($this->body['name']) && Lang_Msg::error("ERROR_AddGenerate_1");
        !Validate::isPrice(floatval($this->body['fat_price'])) && Lang_Msg::error("ERROR_AddGenerate_2");
        !Validate::isPrice(floatval($this->body['group_price'])) && Lang_Msg::error("ERROR_AddGenerate_3");
//        !Validate::isUnsignedInt($this->body['valid']) && Lang_Msg::error("ERROR_AddGenerate_4");
        !Validate::isString($this->body['scheduled_time']) && Lang_Msg::error("ERROR_AddGenerate_7");
        !Validate::isString($this->body['date_available']) && Lang_Msg::error("ERROR_AddGenerate_8");
//        !Validate::isString($this->body['week_time']) && Lang_Msg::error("ERROR_AddGenerate_9");
        !Validate::isUnsignedId($this->body['refund']) && Lang_Msg::error("ERROR_AddGenerate_10");
        !Validate::isUnsignedId($this->body['organization_id']) && Lang_Msg::error("ERROR_AddGenerate_11");
//        !Validate::isString($this->body['remark']) && Lang_Msg::error("ERROR_AddGenerate_12");
        if(!in_array(intval($this->body['refund']),array(0,1))) Lang_Msg::error("ERROR_AddGenerate_13");
        // if(!in_array(intval($this->body['fit_platform']),array(0,1))) Lang_Msg::error("ERROR_AddGenerate_14");
        // if(!in_array(intval($this->body['full_platform']),array(0,1))) Lang_Msg::error("ERROR_AddGenerate_15");
        if(isset($this->body['mini_buy']) && intval($this->body['mini_buy'])<=0) Lang_Msg::error("ERROR_AddGenerate_16");
        if(isset($this->body['mini_buy']) && !Validate::isUnsignedInt($this->body['mini_buy'])) Lang_Msg::error("ERROR_AddGenerate_17");
        if(isset($this->body['max_buy']) && !Validate::isUnsignedInt($this->body['max_buy'])) Lang_Msg::error("ERROR_AddGenerate_18");
//        if(intval($this->body['mini_buy'])>intval($this->body['max_buy'])) Lang_Msg::error("ERROR_AddGenerate_19");
        // if(intval($this->body['fit_platform']==0) && !Validate::isString($this->body['fit_platform_list'])) Lang_Msg::error("ERROR_AddGenerate_20");
        // if(intval($this->body['full_platform'])==0 && !Validate::isString($this->body['full_platform_list'])) Lang_Msg::error("ERROR_AddGenerate_21");
        !Validate::isUnsignedId($this->body['user_id']) && Lang_Msg::error("ERROR_AddGenerate_22");
//        if(floatval($this->body['fat_price'])+floatval($this->body['group_price']) == 0) Lang_Msg::error("ERROR_AddGenerate_23");
 		if(!in_array(intval($this->body['is_fit']),array(0,1))) Lang_Msg::error('是否散客参数出错');
        if(!in_array(intval($this->body['is_full']),array(0,1))) Lang_Msg::error('是否团客参数出错');
 		//微信发的票加个标记
         $ota_type = null !== $this->body['ota_type'] ? $this->body[ 'ota_type' ] : 'system';
         //上下架
         $state = null !== $this->body[ 'state' ] ? $this->body[ 'state' ] : 0;
        // 省市区关联暂不验证 @TODO

        $sms_template = trim(Tools::safeOutput($this->body['sms_template']));
        // 发布票模板
        $ticketTemplateModel = new TicketTemplateModel();
        $data = array(
            'name' => $this->body['name'],
            'fat_price' => floatval($this->body['fat_price']),
            'group_price' => floatval($this->body['group_price']),
            'sale_price' => floatval($this->body['sale_price'])>0?floatval($this->body['sale_price']):0,
            'listed_price' => floatval($this->body['listed_price'])>0?floatval($this->body['listed_price']):0,
            'refund' => intval($this->body['refund']),
            'scheduled_time' => intval($this->body['scheduled_time']),
            'mini_buy' => intval($this->body['mini_buy'])?intval($this->body['mini_buy']):1,
            'max_buy' => intval($this->body['max_buy'])?intval($this->body['max_buy']):100,
            // 'fit_platform' => intval($this->body['fit_platform']),
            // 'fit_platform_list' => $this->body['fit_platform_list']?$this->body['fit_platform_list']:'',
            // 'full_platform' => intval($this->body['full_platform']),
            // 'full_platform_list' => $this->body['full_platform_list']?$this->body['fill_platform_list']:'',
            'remark' => isset($this->body['remark'])?$this->body['remark']:'',
            'date_available' => $this->body['date_available'],
            'sale_start_time'=> intval($this->body['sale_start_time']),
            'sale_end_time'=> intval($this->body['sale_end_time']),
            'valid' => isset($this->body['valid'])?intval($this->body['valid']):0,
            'payment' => '1,2,3,4',
            'week_time' => $this->body['week_time']?$this->body['week_time']:'1,2,3,4,5,6,0',
            'organization_id' => intval($this->body['organization_id']),
            'created_by' => intval($this->body['user_id']),
            'is_fit' => $ota_type == "weixin" ? 1:intval($this->body['is_fit']),
            'is_full' => intval($this->body['is_full']),
            'rule_id' => intval($this->body['rule_id'])>0 ? intval($this->body['rule_id']):0,
            'policy_id' => intval($this->body['policy_id'])>0 ? intval($this->body['policy_id']):0,
            'expire_start' => reset(explode(',',$this->body['date_available'])),
            'expire_end' => end(explode(',',$this->body['date_available'])),
            'ota_type' => $ota_type,
            'state' => $state,
            'valid_flag' => intval($this->body['valid_flag']),
            'sms_template'=> $sms_template ? $sms_template : '',
        );
        $data['real_expire_end'] = $data['expire_end']-$data['scheduled_time'];

        $user = $this->getOperator();
        $data = $data+$user;
        try{
            $res = $ticketTemplateModel->addNew($data,$baseItems);
            !$res && Lang_Msg::error("ERR_PRODUCT_1");
            Tools::lsJson(true,'ok',$res);
        }
        catch(Exception $e){
            Lang_Msg::error("ERR_PRODUCT_1");
        }
    }

    /**
     * 发布任务单
     * author : yinjian
     */
    public function addTaskAction()
    {
        // 验证参数
        $baseItems = json_decode($this->body['base_items'],true);
        !$baseItems && Lang_Msg::error('ERROR_AddGenerate_26'); //请添加门票信息
        foreach($baseItems as $base_id=>$num){
            !$base_id && Lang_Msg::error('ERROR_AddGenerate_26'); //请添加门票信息
        }

        !Validate::isString($this->body['name']) && Lang_Msg::error("门票名称不能为空");
        !Validate::isPrice(floatval($this->body['group_price'])) && Lang_Msg::error("团客价不能为空");
        if(intval($this->body['mini_buy'])<=0) Lang_Msg::error("最少订票不能少于等于0张");
        if(!Validate::isUnsignedInt($this->body['mini_buy'])) Lang_Msg::error("最少订票数目设置错误");
        if(!in_array(intval($this->body['refund']),array(0,1))) Lang_Msg::error("是否允许退票设置错误");
        !Validate::isString($this->body['remark']) && Lang_Msg::error("门票说明不能为空");
        !Validate::isUnsignedId($this->body['scheduled_time']) && Lang_Msg::error("提前预定时间不能为空");
        !Validate::isString($this->body['date_available']) && Lang_Msg::error("可用时间段不能为空");
        !Validate::isUnsignedInt($this->body['valid']) && Lang_Msg::error("门票有效期不能为空");
        !Validate::isString($this->body['week_time']) && Lang_Msg::error("适用日期不能为空");
        !Validate::isUnsignedId($this->body['organization_id']) && Lang_Msg::error("机构id不能为空");
        !Validate::isUnsignedId($this->body['user_id']) && Lang_Msg::error("用户id不能为空");
        // 省市区关联暂不验证 @TODO

        $sms_template = trim(Tools::safeOutput($this->body['sms_template']));
        // 发布票模板
        $ticketTemplateModel = new TicketTemplateModel();
        $data = array(
            'name' => $this->body['name'],
            'group_price' => floatval($this->body['group_price']),
            'sale_price' => floatval($this->body['sale_price'])>0?floatval($this->body['sale_price']):0,
            'listed_price' => floatval($this->body['listed_price'])>0?floatval($this->body['listed_price']):0,
            'refund' => intval($this->body['refund']),
            'scheduled_time' => intval($this->body['scheduled_time']),
            'mini_buy' => intval($this->body['mini_buy']),
            'remark' => trim($this->body['remark']),
            'type' => 1,
            'date_available' => $this->body['date_available'],
            'sale_start_time'=> intval($this->body['sale_start_time']),
            'sale_end_time'=> intval($this->body['sale_end_time']),
            'valid' => intval($this->body['valid']),
            'payment' => '1,2,3,4',
            'week_time' => trim($this->body['week_time']),
            'organization_id' => intval($this->body['organization_id']),
            'created_by' => intval($this->body['user_id']),
            'is_full' => 1,
            'rule_id' => intval($this->body['rule_id'])>0 ? intval($this->body['rule_id']):0,
            'policy_id' => intval($this->body['policy_id'])>0 ? intval($this->body['policy_id']):0,
            'expire_start' => reset(explode(',',$this->body['date_available'])),
            'expire_end' => end(explode(',',$this->body['date_available'])),
            'valid_flag' => intval($this->body['valid_flag']),
            'sms_template'=> $sms_template ? $sms_template : '',
        );
        $data['real_expire_end'] = $data['expire_end']-$data['scheduled_time'];

        $user = $this->getOperator();
        $data = $data+$user;
        $res = $ticketTemplateModel->addNew($data,$baseItems);
        !$res && Lang_Msg::error("添加任务单失败");
        Tools::lsJson(true,'ok',$res);
    }

    /**
     * 发布联票
     * author : yinjian
     */
    public function addUnionAction()
    {
        // 验证参数
        $baseItems = json_decode($this->body['base_items'],true);
        !$baseItems && Lang_Msg::error('ERROR_AddGenerate_26'); //请添加门票信息
        foreach($baseItems as $base_id=>$num){
            !$base_id && Lang_Msg::error('ERROR_AddGenerate_26'); //请添加门票信息
        }

        !Validate::isString($this->body['name']) && Lang_Msg::error("联票名称不能为空");
        !Validate::isPrice(floatval($this->body['fat_price'])) && Lang_Msg::error("散客价不正确");
        !Validate::isPrice(floatval($this->body['group_price'])) && Lang_Msg::error("团客价不能为空");
        if(intval($this->body['mini_buy'])<=0) Lang_Msg::error("最少订票不能少于等于0张");
        if(!Validate::isUnsignedInt($this->body['mini_buy'])) Lang_Msg::error("最少订票数目设置错误");
        if(!in_array(intval($this->body['refund']),array(0,1))) Lang_Msg::error("是否允许退票设置错误");
        !Validate::isString($this->body['remark']) && Lang_Msg::error("门票说明不能为空");
        !Validate::isUnsignedId($this->body['scheduled_time']) && Lang_Msg::error("提前预定时间不能为空");
        !Validate::isString($this->body['date_available']) && Lang_Msg::error("可用时间段不能为空");
        !Validate::isUnsignedInt($this->body['valid']) && Lang_Msg::error("门票有效期不能为空");
        !Validate::isString($this->body['week_time']) && Lang_Msg::error("适用日期不能为空");
        !Validate::isUnsignedId($this->body['organization_id']) && Lang_Msg::error("机构id不能为空");
        !Validate::isUnsignedId($this->body['user_id']) && Lang_Msg::error("用户id不能为空");
        // 省市区关联暂不验证 @TODO

        $sms_template = trim(Tools::safeOutput($this->body['sms_template']));
        // 发布票模板
        $ticketTemplateModel = new TicketTemplateModel();
        $data = array(
            'name' => $this->body['name'],
            'fat_price' => floatval($this->body['fat_price']),
            'group_price' => floatval($this->body['group_price']),
            'sale_price' => floatval($this->body['sale_price'])>0?floatval($this->body['sale_price']):0,
            'listed_price' => floatval($this->body['listed_price'])>0?floatval($this->body['listed_price']):0,
            'refund' => intval($this->body['refund']),
            'scheduled_time' => intval($this->body['scheduled_time']),
            'mini_buy' => intval($this->body['mini_buy']),
            'remark' => trim($this->body['remark']),
            'is_union' => 1,
            'date_available' => $this->body['date_available'],
            'sale_start_time'=> intval($this->body['sale_start_time']),
            'sale_end_time'=> intval($this->body['sale_end_time']),
            'valid' => intval($this->body['valid']),
            'payment' => '1,2,3,4',
            'week_time' => trim($this->body['week_time']),
            'organization_id' => intval($this->body['organization_id']),
            'created_by' => intval($this->body['user_id']),
            'is_fit' => floatval($this->body['fat_price'])>0?1:0,
            'is_full' => floatval($this->body['group_price'])>0?1:0,
            'rule_id' => intval($this->body['rule_id'])>0 ? intval($this->body['rule_id']):0,
            'policy_id' => intval($this->body['policy_id'])>0 ? intval($this->body['policy_id']):0,
            'expire_start' => reset(explode(',',$this->body['date_available'])),
            'expire_end' => end(explode(',',$this->body['date_available'])),
            'valid_flag' => intval($this->body['valid_flag']),
            'sms_template'=> $sms_template ? $sms_template : '',
        );
        $data['real_expire_end'] = $data['expire_end']-$data['scheduled_time'];

        $user = $this->getOperator();
        $data = $data+$user;
        $res = $ticketTemplateModel->addNew($data,$baseItems);
        !$res && Lang_Msg::error("添加联票失败");
        Tools::lsJson(true,'ok',$res);
    }

    /**
     * 门票预订列表
     * author : yinjian
     * modify by zhaqinfeng 2014-11-11
     */
    public function reserve_listAction() {
        try {
            $now = time();
            $where = array(
                'is_del' => 0, 'ota_type' => 'system',
                'expire_end|>=' => $now,
                'sale_start_time|<=' => $now,
                'sale_end_time|exp'=>'=0 OR sale_end_time>='.$now,
            );
            if (isset($this->body['p']) && $this->body['p'] >= 0)
                $this->current = intval($this->body['p']);
            // 地区
            $scenic_name = trim(Tools::safeOutput($this->body['scenic_name']));
            $scenic_id = trim($this->body['scenic_id']);
            $view_point = trim($this->body['view_point']);
            $province_id = trim($this->body['province_id']);
            $city_id = trim($this->body['city_id']);
            $district_id = trim($this->body['district_id']);
            $piWhere = array();
            if ($scenic_name) {
                $piWhere['sceinc_name|like'] = array("%{$scenic_name}%");
            }
            if ($scenic_id && preg_match("/^[\d,]+$/", $scenic_id)) {
                $piWhere['scenic_id|in'] = explode(',', $scenic_id);
            }
            if ($view_point && preg_match("/^[\d,]+$/", $view_point)) {
                foreach (explode(',', $view_point) as $poi) {
                    $piWhere['FIND_IN_SET|EXP'] = "({$poi},view_point)";
                }
            }
            if ($province_id && preg_match("/^[\d,]+$/", $province_id)) {
                $piWhere['province_id|in'] = explode(',', $province_id);
            }
            if ($city_id && preg_match("/^[\d,]+$/", $city_id)) {
                $piWhere['city_id|in'] = explode(',', $city_id);
            }
            if ($district_id && preg_match("/^[\d,]+$/", $district_id)) {
                $piWhere['district_id|in'] = explode(',', $district_id);
            }
            if ($piWhere) {
                $productItems = TicketTemplateItemModel::model()->search($piWhere, "product_id");
                $productIds = array();
                foreach ($productItems as $iv) {
                    $productIds[] = $iv['product_id'];
                }
                $productIds && $where['id|in'] = $productIds;
                !$productIds && Lang_Msg::output(array('data' => array(), 'pagination' => array('count' => 0)));
            }

            if (isset($this->body['state']) && in_array(intval($this->body['state']), array(0, 1)))       // 上下架
                $where['state'] = intval($this->body['state']);
            if (isset($this->body['type']) && intval(in_array($this->body['type'], array(0, 1))))        // 类型 电子票 任务单
                $where['type'] = intval($this->body['type']);

            $is_union = intval($this->body[ 'is_union' ]);
            (isset($this->body[ 'is_union' ]) && $is_union>=0) && $where['is_union'] = $is_union>0?1:0;

            $name = trim(Tools::safeOutput($this->body['name']));
            $name && $where['name|like'] = array("%{$name}%");

            if (isset($this->body['is_fit']))       // 散客筛选
                $where['is_fit'] = intval($this->body['is_fit']) ? 1 : 0;
            if (isset($this->body['is_full']))        // 团客筛选
                $where['is_full'] = intval($this->body['is_full']) ? 1 : 0;
            // fix 当天票
            $today = strtotime(date('Y-m-d'));
            if (isset($this->body['expire_end']) && $this->body['expire_end'])        // 筛选过有效期的门票
                $where['real_expire_end|>-'] = $today;

            $agency_id = intval($this->body['agency_id']);
            if ($agency_id) {  //分销商查询相关可用票
                $orgInfo = ApiOrganizationModel::model()->orgInfo($agency_id); //机构详情
                if ($orgInfo) {
                    $TicketPolicyItemModel = new TicketPolicyItemModel();
                    $where['or'] = array(
                        'policy_id' => 0,
                        'policy_id|exp' => "in(SELECT policy_id FROM " . $TicketPolicyItemModel->getTable() . " WHERE distributor_id=" . $agency_id . " AND blackname_flag=0)",
                        'and' => array(
                            'policy_id|in' => "(SELECT id FROM " . TicketPolicyModel::model()->getTable() . " WHERE other_blackname_flag=0)",
                            'policy_id|not in' => "(SELECT policy_id FROM " . $TicketPolicyItemModel->getTable() . " WHERE distributor_id=" . $agency_id . " AND blackname_flag=1)",
                        ),
                    );

                    if ((intval($this->body['is_fit']) && !$orgInfo['is_distribute_person']) || (intval($this->body['is_full']) && !$orgInfo['is_distribute_group'])) { //散客票
                        $supplierIds = ApiOrganizationModel::model()->supplierIdsOfAgency($agency_id);
                        $where['organization_id|in'] = $supplierIds;
                    }
                }
            }
            $TicketTemplateModel = new TicketTemplateModel();
            $this->count = $TicketTemplateModel->countResult($where);
            $this->pagenation();
            $data = $this->count > 0 ? $TicketTemplateModel->search($where, $this->getFields(), $this->getSortRule(), $this->limit) : array();

            $show_scenicname = intval($this->body['show_scenic_name']);
            $show_poiname = intval($this->body['show_poi_name']);
            $secnicList = $poiList = array();
            if ($show_scenicname || $show_poiname) {
                $scenicIds = $poiIds = array();
                foreach ($data as $v) {
                    $show_scenicname && $v['scenic_id'] && $scenicIds[] = $v['scenic_id'];
                    $show_poiname && $v['view_point'] && $poiIds[] = $v['view_point'];
                }
                if ($show_scenicname) {
                    $scenicIds = array_unique(explode(',', implode(',', $scenicIds)));
                    sort($scenicIds);
                    $scenics = ScenicModel::model()->getScenicList(array('ids' => implode(',', $scenicIds), 'items' => count($scenicIds)));
                    if (isset($scenics['body']['data'])) {
                        foreach ($scenics['body']['data'] as $sv) {
                            $secnicList[$sv['id']] = $sv['name'];
                        }
                    }
                }
                if ($show_poiname) {
                    $poiIds = array_unique(explode(',', implode(',', $poiIds)));
                    sort($poiIds);
                    $pois = ScenicModel::model()->getPoiList(array('ids' => implode(',', $poiIds), 'items' => count($poiIds), 'fields' => 'id,name', 'sort_by' => 'id:asc'));
                    if (isset($pois['body']['data'])) {
                        foreach ($pois['body']['data'] as $sv) {
                            $poiList[$sv['id']] = $sv['name'];
                        }
                    }
                }
            }


            $productIds = array_keys($data);
            $ticket_type = !empty($where['is_fit']) ? 1 : (!empty($where['is_full']) ? 0 : 1);

            if($productIds){
                $favorList = FavoritesModel::model()->search(array('ticket_id|in' => $productIds, 'organization_id' => $agency_id, 'type' => $ticket_type));
                $favorProdIds = array();
                foreach ($favorList as $fv) {
                    $favorProdIds[] = $fv['ticket_id'];
                }

                $subList = SubscribesModel::model()->search(array('ticket_id|in' => $productIds, 'organization_id' => $agency_id, 'type' => $ticket_type));
                $subProdIds = array();
                foreach ($subList as $fv) {
                    $subProdIds[] = $fv['ticket_id'];
                }
            }

            foreach ($data as $k => $v) {
                if ($show_scenicname && $secnicList) {
                    $v['scenic_id'] = explode(',', $v['scenic_id']);
                    sort($v['scenic_id']);
                    $data[$k]['scenic_id'] = implode(',', $v['scenic_id']);
                    $data[$k]['scenic_name'] = implode(',', array_intersect_key($secnicList, array_flip($v['scenic_id'])));
                }
                if ($show_poiname && $poiList) {
                    $v['view_point'] = explode(',', $v['view_point']);
                    sort($v['view_point']);
                    $data[$k]['view_point'] = implode(',', $v['view_point']);
                    $data[$k]['poi_name'] = implode(',', array_intersect_key($poiList, array_flip($v['view_point'])));
                }
                $data[$k]['favor'] = in_array($v['id'], $favorProdIds) ? 1 : 0;
                $data[$k]['sub'] = in_array($v['id'], $subProdIds) ? 1 : 0;
            }

            $show_items = intval($this->body['show_items']);
            if ($show_items && $data) {
                $productItems = TicketTemplateItemModel::model()->search(array('product_id|in' => $productIds));
                foreach ($productItems as $piv) {
                    $data[$piv['product_id']]['items'][] = $piv;
                }
            }

            $result = array(
                'data' => array_values($data),
                'pagination' => array('count' => $this->count, 'current' => $this->current, 'items' => $this->items, 'total' => $this->total)
            );
            Tools::lsJson(true, 'ok', $result);
        } catch(Exception $e){
            print_r($e);
            Tools::lsJson(true, 'ok', array('data'=>array(),'pagination'=>array('count'=>0)));
        }
    }

    // 门票快到期
    public function remaindAction()
    {
        !Validate::isUnsignedId($this->body['organization_id']) && Lang_Msg::error('机构id不能为空');
        $expire_start = strtotime(date('Y-m-d'));
        $expire_end = $expire_start+24*7*3600;
        $where = array('is_del'=>0,'ota_type'=>'system','state'=>1,'organization_id'=>$this->body['organization_id'],'sale_end_time|between'=>array($expire_start,$expire_end));
        // 分页
        /*$count = reset(TicketTemplateModel::model()->search($where,'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();*/
        // 数据
        $data['data'] = TicketTemplateModel::model()->search($where,'*','created_at desc');
        /*$data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );*/
        Tools::lsJson(true,'ok',$data);
    }
    
    //根据票ID列表来获取票信息
    public function ticketlistAction()
    {
  		!Validate::isString( $this->body[  'ids' ] ) && Lang_Msg::error( 'no ids');
  	    $id_arr = explode( ',', trim( $this->body[ 'ids' ]) );
  	    $return = array();
  	    foreach( $id_arr as $id )
  	    {
  	    	$tmp = TicketTemplateModel::model()->getbyId( $id );
  	    	$return[ $id ] = '';
  	    	if( $tmp )
  	    	{
  	    		$return[ $id ] = $tmp;
  	    	}
  	    }
  	    Lang_Msg::output( $return );
    }

    //批量删除
    public function delAction() {
        try {
            $ids = trim(Tools::safeOutput($this->body['id']));
            $organization_id = intval($this->body['or_id']);

            !preg_match("/^[\d,]+$/", $ids) && Lang_Msg::error('ERROR_TICKET_1');
            !$organization_id && Lang_Msg::error('ERROR_SUPPLIER_1');

            $ids = explode(',', $ids);

            $TicketTemplateModel = new TicketTemplateModel();
            $data = $TicketTemplateModel->search(array('id|in' => $ids));
            $ableDelIds = array(); //能删除的id
            foreach ($data as $detail) {
                if ($detail['organization_id'] != $organization_id) {
                    Lang_Msg::error('产品［' . $detail['name'] . '］不是该供应商发布的无法操作');
                } else {
                    $detail['state'] != 1 && $ableDelIds[] = $detail['id'];
                }
            }

            if($ableDelIds){
                $TicketTemplateModel->begin();
                $r = $TicketTemplateModel->updateByAttr(
                    array('is_del' => 1),
                    array('id|in' => $ableDelIds, 'organization_id' => $organization_id, 'state|!=' => 1)
                );
                if ($r) {
                    $now = time();
                    $r = TicketTemplateItemModel::model()->update(
                        array('deleted_at' => $now),
                        array('product_id|in' => $ableDelIds)
                    );
                    if ($r) {
                        $TicketTemplateModel->commit();
                        Tools::lsJson(true, '所选' . (count($ableDelIds) < count($ids) ? '产品上架的不能删除，' : '') . '下架的产品已删除');
                    }
                }
                $TicketTemplateModel->rollback();
                Lang_Msg::error('ERROR_OPERATE_1');
            }
            else{
                Lang_Msg::error('上架产品不能删除，没有可删除的下架产品');
            }

        } catch(Exception $e){
            Lang_Msg::error('ERROR_OPERATE_1');
        }
    }

    //按产品id或门票id获取产品关联的门票
    public function getItemsAction(){
        $product_ids = trim(Tools::safeOutput($this->body['product_ids']));
        $base_ids = trim(Tools::safeOutput($this->body['base_ids']));
        !$product_ids && !$base_ids && Lang_Msg::error('ERROR_TICKET_1');
        $where = array();
        $product_ids && $product_ids = explode(',',$product_ids);
        $product_ids && $where['product_id|in'] = $product_ids;
        $base_ids && $base_ids = explode(',',$base_ids);
        $base_ids && $where['base_id|in'] = $base_ids;
        $return['data'] = TicketTemplateItemModel::model()->search($where,"*",$this->getSortRule('product_id','asc'));
        $return['type'] = TicketTypeModel::model()->getAll();
        Lang_Msg::output( $return );
    }
}