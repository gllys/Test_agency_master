<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-23
 * Time: 下午3:25
 */

class OrderController extends Base_Controller_Api {

    public function listsAction(){
        $OrderModel = new OrderModel();
        $where = array('deleted_at'=>0);

        $distributor_id = intval($this->body['distributor_id']);
        $distributor_id && $where['distributor_id'] = $distributor_id; //按分销商查找

        $supplier_id = intval($this->body['supplier_id']);
        $supplier_id && $where['supplier_id'] = $supplier_id; //按供应商查找

        $landscape_id = intval($this->body['landscape_id']);
        $landscape_id && $where['find_in_set|EXP'] = "({$landscape_id},landscape_ids)"; //按景区查找
        $kind = intval($this->body['kind']); //种类:1单票2联票3套票
        $kind && $where['kind'] = $kind;

        //支付方式类型：线上、线下、信用支付、储值支付,'online','offline','credit','advance','union'
        $pay_type = trim(Tools::safeOutput($this->body['pay_type']));
        $pay_type = Util_Common::intersectExplode($pay_type,array('online','offline','credit','advance','union'));
        if(!empty($pay_type)){
            $where['pay_type|in'] = $pay_type;
        }

        //支付渠道:cash,offline,credit,pos,alipay,advance,union,kuaiqian,taobao
        $payment = trim(Tools::safeOutput($this->body['payment']));
        $payment = Util_Common::intersectExplode($payment,array_keys(PaymentModel::model()->payments));
        if(!empty($payment)){
            $where['payment|in'] = $payment;
        }

        $time_type = intval($this->body['time_type']); //时间查找类型,0创建时间，1游玩日期，2入园日期

        $start_date = trim(Tools::safeOutput($this->body['start_date']));
        ($start_date && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$start_date)) && Lang_Msg::error('ERROR_START_DAY_1');
        if($start_date){
            $time_type==0 && $where['created_at|>='] = strtotime($start_date);
            $time_type==1 && $where['use_day|>='] = $start_date;
            if($time_type==2){
                $where['use_time|>='] = strtotime($start_date);
                //$where['updated_at|exp'] = '>created_at';
                $where['used_nums|>'] = 0;
            }
        }
        $tableYm = $start_date ? strtotime($start_date):'' ;

        $end_date = trim(Tools::safeOutput($this->body['end_date']));
        ($end_date && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$end_date)) && Lang_Msg::error('ERROR_END_DAY_1');
        if($end_date){
            $time_type==0 && $where['created_at|<='] = strtotime($end_date." 23:59:59");
            $time_type==1 && $where['use_day|<='] = $end_date;
            if($time_type==2){
                $where['use_time|<='] = strtotime($end_date." 23:59:59");
                //$where['updated_at|exp'] = '>created_at';
                $where['used_nums|>'] = 0;
            }
        }

        $product_name = trim(Tools::safeOutput($this->body['product_name'])); //按产品票名称查询
        $product_name && $where['name|LIKE'] = array("%{$product_name}%");

        $scenic_name = trim(Tools::safeOutput($this->body['scenic_name'])); //按景区名称查询
        if($scenic_name){
            $scenicIds = LandscapeModel::model()->getIdsByName($scenic_name);
            if($scenicIds){
                $where['AND'] = array();
                foreach($scenicIds as $scenicId){
                    array_push($where['AND'],"find_in_set({$scenicId},landscape_ids)");
                }
                $where['AND'] = implode(' OR ',$where['AND']);
            } else {
                Lang_Msg::output(array('data'=>array(),'pagination'=>array('count'=>0),'statics'=>array()));
            }
        }

        $agency_name = trim(Tools::safeOutput($this->body['agency_name'])); //按分销商名称查询
        if($agency_name){
            $agencyIds = OrganizationModel::model()->getIdsByName($agency_name,'agency');
            if($agencyIds){
                $where['distributor_id|in'] = $agencyIds;
            }
            else{
                Lang_Msg::output(array('data'=>array(),'pagination'=>array('count'=>0),'statics'=>array()));
            }
        }

        $supply_name = trim(Tools::safeOutput($this->body['supply_name'])); //按供应商名称查询
        if($supply_name){
            $supplyIds = OrganizationModel::model()->getIdsByName($supply_name,'supply');
            if($supplyIds){
                $where['supplier_id|in'] = $supplyIds;
            }
            else{
                Lang_Msg::output(array('data'=>array(),'pagination'=>array('count'=>0),'statics'=>array()));
            }
        }

        $id = trim(Tools::safeOutput($this->body['id']));
        $id && $where['id'] = $id; //按订单号查找

        $ids = $this->body['ids'];
        (!is_array($ids) && preg_match("/^[\d,]+$/",$ids)) && $ids = explode(',',$ids);
        $ids && $where['id|IN'] = $ids; //按订单ID查找

        $type = intval($this->body['type']) ? 1:0; //订单类型：0电子票订单1任务票订单
        isset($_POST['type']) && $where['type'] = $type;

        $owner_name = trim(Tools::safeOutput($this->body['owner_name']));
        $owner_name && $where['owner_name|like'] = array("%{$owner_name}%"); //按取票人

        $owner_mobile = trim(Tools::safeOutput($this->body['owner_mobile']));
        $owner_mobile && $where['owner_mobile'] = $owner_mobile; //按取票人手机号

        $owner_card = trim(Tools::safeOutput($this->body['owner_card']));
        $owner_card && $where['owner_card'] = $owner_card; //按取票人身份证

        $product_id = intval($this->body['product_id']);
        $product_id && $where['product_id'] = $product_id; //按产品ID查找

        $ticket_status = intval($this->body['ticket_status']); //按门票状态查询
        if(1==$ticket_status) $where['used_nums|>'] = 0; //有使用
        else if(2==$ticket_status) $where['nums|EXP'] = '>used_nums+refunding_nums+refunded_nums'; //有未使用
        else if(3==$ticket_status) $where['refunded_nums|EXP'] = '>0-refunding_nums'; //有退票

        /* v1.8
         * array( 
         *     'status' => array('unaudited', 'reject', 'unused', 'used', 'finish', 'all'), 
         *     'pay_status' => array('unpaid', 'cancel', 'paid', 'all'), 
         *     'bill_status' => array('unbill', 'billed', 'all'),
         *      'refund_status' => array('refunding', 'refunded', 'all')
         *  )
         * */
        $status = trim(Tools::safeOutput($this->body['status'])); //按状态查找
        if(in_array($status,$OrderModel->allStatus)) {
            $where['status'] = $status;
        }
        else if ($status=='unused') {
            $where['used_nums'] = 0;
        }
        else if ($status=='used') {
            $where['used_nums|>'] = 0;
        }
        $statuses = trim(Tools::safeOutput($this->body['statuses'])); //按状态查找
        if($statuses) {
            $statuses = Util_Common::intersectExplode($statuses,$OrderModel->allStatus);
        } else {
            $statuses = Util_Common::intersectExplode($status,$OrderModel->allStatus);
        }
        if($statuses){
            $where['status|in'] = $statuses;
        }

        $use_status = trim(Tools::safeOutput($this->body['use_status'])); //按使用状态查找
        if(preg_match("/^[\d,]+$/",$use_status)){ //使用状态：0未使用，1已使用
            $where['use_status|in'] = explode(',',$use_status);
        }

        $pay_status = trim(Tools::safeOutput($this->body['pay_status'])); //按支付状态查找
        if(preg_match("/^[\d,]+$/",$pay_status)){ //支付状态：0未支付，1支付中，2已支付
            $where['pay_status|in'] = explode(',',$pay_status);
        } else { //暂留，准备弃用
            if(in_array($pay_status,array('unpaid', 'cancel', 'paid'))) {
                $where['status'] = $pay_status;
            } else {
                $pay_statuses = Util_Common::intersectExplode($pay_status, $OrderModel->allStatus);
                if ($pay_statuses) {
                    $where['status|in'] = $pay_statuses;
                }
            }
        }

        $audit_status = trim(Tools::safeOutput($this->body['audit_status'])); //按审核状态查找
        if(preg_match("/^[\d,]+$/",$audit_status)){ //审核状态：0审核中，1已审核，2已驳回
            $where['audit_status|in'] = explode(',',$audit_status);
        }

        $refund_status = trim(Tools::safeOutput($this->body['refund_status'])); //按退款状态查找
        if(preg_match("/^[\d,]+$/",$refund_status)){ //退款状态：0未退款，1退款中，2已退款
            $where['refund_status|in'] = explode(',',$refund_status);
        } else { //暂留，准备弃用
            if ($refund_status == 'refunding') { //有退款中的订单
                $where['refunding_nums|>'] = 0;
            } else if ($refund_status == 'refunded') { //有已退款的订单
                $where['refunded_nums|>'] = 0;
            }
        }

        $bill_status = trim(Tools::safeOutput($this->body['bill_status'])); //按结算状态查找
        if(preg_match("/^[\d,]+$/",$bill_status)){ //结算状态：0未结算，1已结算
            $where['bill_status|in'] = explode(',',$bill_status);
        } else { //暂留，准备弃用
            if ($bill_status == 'billed') { //已结款的订单
                $where['status'] = $bill_status;
            } else if ($bill_status == 'unbill') { //未结款支付过的订单
                $where['status|in'] = array('paid', 'finish');
            }
        }

        $cancel_status = trim(Tools::safeOutput($this->body['cancel_status'])); //按审核状态查找
        if(preg_match("/^[\d,]+$/",$cancel_status)){ //取消状态：0未取消，1已取消
            $where['cancel_status|in'] = explode(',',$cancel_status);
        }

        $source = intval($this->body['source']);
        isset($this->body['source']) && $where['source'] = $source; //按外部来源查找 0默认 1淘宝 2八爪鱼 3同程 4途牛 5驴妈妈 6携程 7景点通 8度周末 9途家

        $local_source = intval($this->body['local_source']);
        $local_source && $where['local_source'] = $local_source; //按内部来源查找 0分销系统 1OPENAPI 2浙风自由行 3微信

        $source_id = trim(Tools::safeOutput($this->body['source_id']));
        $source_id && $where['source_id'] = $source_id; //按外部来源ID查找

        !empty($this->body['created_by']) && $where['created_by'] = trim($this->body['created_by']); //创建人

        $statWhere = $where;
        $status!='cancel' && $statWhere['nums|EXP'] = ">refunded_nums";
        !$status && !$statuses && $statWhere['status|in'] = array('paid','finish','billed');
        $countRes = $OrderModel->share($tableYm)->search(
            $statWhere,
            "count(*) as order_nums,
                sum(nums) as total_nums,
                sum(used_nums) as total_used_nums,
                sum(refunding_nums) as total_refunding_nums,
                sum(refunded_nums) as total_refunded_nums,
                sum(refunded) as total_refunded,
                sum(payed) as total_payed,
                sum(amount) as total_amount"
        );
        $countRes = empty($countRes)? array(
            "order_nums"=>0, "total_nums"=>0, "total_used_nums"=>0, "total_refunding_nums"=>0,
            "total_refunded_nums"=>0, "total_refunded"=>0, "total_payed"=>0, "total_amount"=>0
        ) : reset($countRes);
        $this->count = $OrderModel->share($tableYm)->countResult($where);
        $this->pagenation();
        $data = $this->count>0 ? $OrderModel->share($tableYm)->search($where,$this->getFields(),$this->getSortRule(),$this->limit) : array();

        $ticketNums = array();
        if($data) {
            if($this->body['with_store'] || $this->body['show_verify_items']){
                $orderIds = array_keys($data);
                $OrderItemModel = new OrderItemModel();
                if($this->body['show_verify_items']){ //是否现实核销明细
                    foreach($data as $k=>$v){
                        $data[$k]['verify_items']=array();
                    }
                    if(isset($this->body['use_date_time']) && $this->body['use_date_time']){
                        $verifyRecode = $OrderItemModel->setGroupBy("order_id,use_date_time")->search(array('order_id|in'=>$orderIds,"use_time|>"=>0),"id,order_id,count(id) as num,date_format(FROM_UNIXTIME( `use_time`),'%Y-%m-%d') use_date_time");
                    }else{
                        $verifyRecode = $OrderItemModel->setGroupBy("order_id,use_time")->search(array('order_id|in'=>$orderIds,"use_time|>"=>0),"id,order_id,count(id) as num,use_time");
                    }
                    $verifyItems = array();
                    foreach($verifyRecode as $v){
                        $verifyItems[$v['order_id']][] = $v;
                    }
                }
                foreach ($data as $orderId=>$ov) {
                    $data['use_time'] && $data['updated_at'] = $data['use_time']; //最后使用时间，暂时，更新时间＝使用时间 zqf 20150313
                    if($this->body['with_store']) {
                        $key = $ov['product_id'] . "_" . $ov['use_day'];
                        if (!isset($ticketNums[$key])) {
                            $ticketNums[$key] = array();
                            $productInfo = TicketTemplateModel::model()->getInfo($ov['product_id'], $ov['price_type'], $ov['distributor_id'], $ov['use_day'], 0);
                            if ($productInfo && !isset($productInfo['code'])) {
                                $ticketNums[$key]['remain_reserve'] = 1 == $productInfo['state'] ? $productInfo['remain_reserve'] : 0;
                            } else {
                                $ticketNums[$key]['remain_reserve'] = 0;
                            }
                            $ticketNums[$key]['state'] = $productInfo['state'];
                        }
                    }
                    if($this->body['show_verify_items'] && $verifyItems){
                        $data[$orderId]['verify_items'] = isset($verifyItems[$orderId])?$verifyItems[$orderId]:array();
                    }
                }
            }
        }

        $result = array(
            'data'=>array_values($data),
            'statics'=>$countRes,
            'pagination'=>array(  'count'=>$this->count,  'current'=>$this->current, 'items'=>$this->items, 'total'=>$this->total, )
        );
        if($this->body['with_store'] && $ticketNums){
            $result['with_store'] = $ticketNums;
        }
        Lang_Msg::output($result);
    }

    //订单详情
    public function detailAction(){
        $where = array('deleted_at'=>0);
        $id = trim(Tools::safeOutput($this->body['id']));
        $id && $where['id'] = $id;

        $source_id = trim(Tools::safeOutput(($this->body['source_id'])));
        $source_id && $where['source_id'] = $source_id; //按source_id查找
		
        $source = trim(Tools::safeOutput(($this->body['source'])));
        $source && $where['source'] = $source; //按source查找

        !$id && !$source_id && Lang_Msg::error("ERROR_ORDER_INFO_1");

        $distributor_id = intval($this->body['distributor_id']);
        $distributor_id && $where['distributor_id'] = $distributor_id; //按分销商查找

        $supplier_id = intval($this->body['supplier_id']);
        $supplier_id && $where['supplier_id'] = $supplier_id; //按供应商查找

        $landscape_id = intval($this->body['landscape_id']);
        $landscape_id && $where['landscape_ids|EXP'] = "REGEXP '^{$landscape_id}$|^{$landscape_id},|,{$landscape_id},|,{$landscape_id}$'"; //按景区查找

        $detail = OrderModel::model()->search($where,$this->getFields());
        !$detail && Lang_Msg::error("ERROR_ORDER_INFO_3");
        $detail = reset($detail);
        $detail['use_time'] && $detail['updated_at'] = $detail['use_time']; //最后使用时间，暂时，更新时间＝使用时间 zqf 20150313
        $detail['ticket_infos'] = preg_replace("/(\\\?)(u[0-9a-f]{4})/",'\\\$2',$detail['ticket_infos']); //修正unicode代码
        $detail['ticket_infos']=json_decode($detail['ticket_infos'],true);
        intval($this->body['show_order_items']) && $detail['order_items'] = OrderItemModel::model()->search(array('order_id'=>$detail['id']));
        intval($this->body['show_tickets']) && $detail['tickets'] = TicketModel::model()->search(array('order_id'=>$detail['id']));
        intval($this->body['show_ticket_items']) && $detail['ticket_items'] = TicketItemModel::model()->search(array('order_id'=>$detail['id']));
        Lang_Msg::output($detail);
    }

    //订单按门票或景区 景点统计
    public function infosAction(){
        $where = array('deleted_at'=>0);
        $id = trim(Tools::safeOutput($this->body['id']));
        $id && $where['id'] = $id;

        $source_id = trim(Tools::safeOutput(($this->body['source_id'])));
        $source_id && $where['source_id'] = $source_id; //按source_id查找

        !$id && !$source_id && Lang_Msg::error("ERROR_ORDER_INFO_1");

        $distributor_id = intval($this->body['distributor_id']);
        $distributor_id && $where['distributor_id'] = $distributor_id; //按分销商查找

        $supplier_id = intval($this->body['supplier_id']);
        $supplier_id && $where['supplier_id'] = $supplier_id; //按供应商查找

        $landscape_id = intval($this->body['landscape_id']);
        $landscape_id && $where['landscape_ids|EXP'] = "REGEXP '^{$landscape_id}$|^{$landscape_id},|,{$landscape_id},|,{$landscape_id}$'"; //按景区查找

        $detail = OrderModel::model()->search($where,$this->getFields());
        !$detail && Lang_Msg::error("ERROR_ORDER_INFO_3");
        $detail = reset($detail);

        $detail['ticket_infos'] = preg_replace("/(\\\?)(u[0-9a-f]{4})/",'\\\$2',$detail['ticket_infos']); //修正unicode代码
        $detail['ticket_infos']=json_decode($detail['ticket_infos'],true);

        $type = intval($this->body['type']); //景点分组统计类型：0门票，1景区

        if($detail['ticket_infos']){
            $baseCountSql = " select *,count(id) as nums ,count(case status when 2 then 2 end) as used_num,count(case status when 1 then 1 end) as unuse_num  ";
            $baseCountSql .=" from (select ti.id,t.ticket_template_id as base_id,ti.landscape_id,ti.poi_id,ti.status ";
            $baseCountSql .=" from ".TicketModel::model()->getTable()." t join ".TicketItemModel::model()->getTable()." ti ON (ti.ticket_id=t.id)  ";
            $baseCountSql .=" where t.order_id='{$detail['id']}'  ";
            $baseCountSql .=" group by ".($type?"":"base_id,")."ti.poi_id,ti.order_item_id ";
            $baseCountSql .=" ) tmp ";
            $baseCountSql .=" group by ".($type?"landscape_id":"base_id").",poi_id ";

            $baseCounts = TicketItemModel::model()->db->selectBySql($baseCountSql);
            if($baseCounts) {
                $tmp = array();
                foreach($baseCounts as $v){
                    $tmp[$v[($type?'landscape_id':'base_id')]][$v['poi_id']] = $v;
                }
                $detail['poi_counts'] = $tmp;
            }

            $poi_ids = array();
            foreach($detail['ticket_infos'] as $v){
                $poi_ids = array_merge($poi_ids,explode(',',$v['view_point']));
                $detail['scenic_names'][$v['scenic_id']] = $v['sceinc_name'];
            }
            $poi_ids = array_unique($poi_ids);
            $poiLists = LandscapeModel::model()->poiLists($poi_ids,'id,name');
            $detail['poi_names'] = array();
            foreach($poiLists as $v) {
                $detail['poi_names'][$v['id']] = $v['name'];
            }
        }
        Lang_Msg::output($detail);
    }

    //统计订单下景区使用产品票张数
    public function scenicUsedAction(){
        $where = array('deleted_at'=>0);
        $id = trim(Tools::safeOutput($this->body['id']));
        $id && $where['id'] = $id;

        $source_id = trim(Tools::safeOutput(($this->body['source_id'])));
        $source_id && $where['source_id'] = $source_id; //按source_id查找

        !$id && !$source_id && Lang_Msg::error("ERROR_ORDER_INFO_1");

        $distributor_id = intval($this->body['distributor_id']);
        $distributor_id && $where['distributor_id'] = $distributor_id; //按分销商查找

        $supplier_id = intval($this->body['supplier_id']);
        $supplier_id && $where['supplier_id'] = $supplier_id; //按供应商查找

        $detail = OrderModel::model()->search($where,$this->getFields());
        !$detail && Lang_Msg::error("ERROR_ORDER_INFO_3");
        $detail = reset($detail);

        $return = array(
            'order_id'=>$detail['id'],
            'status'=>$detail['status'],
            'used_num'=>$detail['used_nums'],
            'unuse_num'=>$detail['nums']-$detail['used_nums']-$detail['refunding_nums']-$detail['refunded_nums'],
        );

        $detail['ticket_infos']=json_decode($detail['ticket_infos'],true);
        foreach($detail['ticket_infos'] as $v){
            $detail['scenic_names'][$v['scenic_id']] = $v['sceinc_name'];
        }

        if($detail['ticket_infos']){
            $countSql = "select landscape_id as id,landscape_id,'' as name,count(case status when 2 then 2 end) as used_num,count(case status when 1 then 1 end) as unuse_num ";
            $countSql .=" from (select id,landscape_id,status,order_item_id from ".TicketModel::model()->getTable()."  where order_id='{$detail['id']}' group by landscape_id,order_item_id) tmp ";
            $countSql .=" group by landscape_id";

            $baseCounts = TicketModel::model()->db->selectBySql($countSql);
            $data = array();
            foreach($baseCounts as $k=>$v){
                $data[$v['landscape_id']] = $v;
                $data[$v['landscape_id']]['name'] = $detail['scenic_names'][$v['landscape_id']];
            }
            $return['list'] = $data;
        }
        Lang_Msg::output($return);
    }


    //统计订单下产品门票使用张数
    public function ticketUsedAction(){
        $where = array('deleted_at'=>0);
        $id = trim(Tools::safeOutput($this->body['id']));
        $id && $where['id'] = $id;

        $source_id = trim(Tools::safeOutput(($this->body['source_id'])));
        $source_id && $where['source_id'] = $source_id; //按source_id查找

        !$id && !$source_id && Lang_Msg::error("ERROR_ORDER_INFO_1");

        $distributor_id = intval($this->body['distributor_id']);
        $distributor_id && $where['distributor_id'] = $distributor_id; //按分销商查找

        $supplier_id = intval($this->body['supplier_id']);
        $supplier_id && $where['supplier_id'] = $supplier_id; //按供应商查找

        $detail = OrderModel::model()->search($where,$this->getFields());
        !$detail && Lang_Msg::error("ERROR_ORDER_INFO_3");
        $detail = reset($detail);

        $return = array(
            'order_id'=>$detail['id'],
            'status'=>$detail['status'],
            'used_num'=>$detail['used_nums'],
            'unuse_num'=>$detail['nums']-$detail['used_nums']-$detail['refunding_nums']-$detail['refunded_nums'],
        );

        $detail['ticket_infos']=json_decode($detail['ticket_infos'],true);
        foreach($detail['ticket_infos'] as $v){
            $detail['base_names'][$v['base_id']] = $v['base_name'];
        }

        if($detail['ticket_infos']){
            $countSql = "select ticket_base_id as id,ticket_base_id,'' as name,count(case status when 2 then 2 end) as used_num,count(case status when 1 then 1 end) as unuse_num ";
            $countSql .=" from (select id,ticket_template_id as ticket_base_id,status,order_item_id from ".TicketModel::model()->getTable()."  where order_id='{$detail['id']}' group by landscape_id,order_item_id) tmp ";
            $countSql .=" group by ticket_base_id";

            $baseCounts = TicketModel::model()->db->selectBySql($countSql);
            foreach($baseCounts as $k=>$v){
                $baseCounts[$k]['name'] = $detail['base_names'][$v['ticket_base_id']];
            }
            $return['list'] = $baseCounts;
        }
        Lang_Msg::output($return);
    }


    /**
     * 添加订单，单个添加，如单个票上预订
     */
    public function addAction(){
        $OrderModel = new OrderModel();
        try{
            $params = $this->getOperator(); //获取操作者

            $params['product_id'] = intval($this->body['product_id']); //票种id
            !$params['product_id'] &&  $params['product_id'] = intval($this->body['ticket_template_id']);
            $params['price_type'] = intval($this->body['price_type']) ? 1 : 0; //0散客1,团客
            $params['distributor_id'] = intval($this->body['distributor_id']); //分销商ID
            $params['use_day'] = trim(Tools::safeOutput($this->body['use_day'])); //游玩日期
            $params['nums'] = intval($this->body['nums']); //订购票数
            $params['owner_name'] = trim(Tools::safeOutput($this->body['owner_name'])); //取票人
            $params['owner_mobile'] = trim(Tools::safeOutput($this->body['owner_mobile'])); //取票人手机
            $params['owner_card'] = trim(Tools::safeOutput($this->body['owner_card'])); //取票人身份证
            $params['remark'] = trim(Tools::safeOutput($this->body['remark']));
            $params['status'] = $params['remark'] ? 'unaudited':'unpaid'; //如有备注，则订单状态为待确认
            $params['ota_type'] = trim(Tools::safeOutput($this->body['ota_type']));
            $params['ota_account'] = intval($this->body['ota_account']);
            $params['ota_name'] = trim(Tools::safeOutput($this->body['ota_name']));

            $params['source'] = intval($this->body['source']);
            $params['local_source'] = trim(Tools::safeOutput($this->body['local_source']));
            $params['source_id'] = trim(Tools::safeOutput($this->body['source_id']));
            $params['source_token'] = trim(Tools::safeOutput($this->body['source_token']));

            isset($this->body['price']) && $params['price'] = doubleval($this->body['price']);

            !$params['product_id'] && Lang_Msg::error('ERROR_TKT_1'); //缺少票种ID参数
            !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
            (!$params['use_day'] || !preg_match("/^\d{4}-\d{2}-\d{2}$/",$params['use_day'])) && Lang_Msg::error('ERROR_USEDAY_1'); //游玩日期不能为空，且格式为xxxx-xx-xx
            !$params['nums'] && Lang_Msg::error('ERROR_TK_NUMS_1');
            !$params['owner_name'] && Lang_Msg::error('ERROR_OWNER_1');
            !$params['owner_mobile'] && Lang_Msg::error('ERROR_OWNER_2');
            //!$params['owner_card'] && Lang_Msg::error('ERROR_OWNER_3');
            $params['ota_type']=='ota' && (!$params['ota_account'] || !$params['ota_name']) && Lang_Msg::error('ERROR_OTA_1');

            $params['visitors']= trim($this->body['visitors']);

            //微信生成订单
            if( $this->body['pay_type'])  $params[ 'pay_type' ] = $this->body[ 'pay_type' ];
            if( $this->body[ 'payment' ]) $params[ 'payment' ] = $this->body[ 'payment' ];

            $params['is_sms'] = intval($this->body['is_sms']); //是否发送短信发码，1是，0否
            !isset($this->body['is_sms']) && $params['is_sms'] = 1;

            $OrderModel->begin();
            $order = $OrderModel->addOrder($params);
            if($order){
                $OrderModel->commit();
                Log_Order::model()->add(array('type'=>Log_Order::$type['CREATE'],'num'=>1,'order_ids'=>$order['id'],'content'=>Lang_Msg::getLang('INFO_ORDER_1'),'distributor_id'=>$params['distributor_id']));
                Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'), $order);
            }
            else{
                $OrderModel->rollback();
                Lang_Msg::error("ERROR_OPERATE_1");
            }
        } catch(Exception $e) {
            Log_Base::save('Order', 'error:'.$e->getMessage());
            Log_Base::save('Order', var_export($this->body,true));
            $OrderModel->rollback();
            Lang_Msg::error( 'ERROR_GLOBAL_3' );
        }
    }

    /**
     * 添加订单，批量添加
     */
    public function addbatchAction() {
        $OrderModel = new OrderModel();
        try{
            $params = $this->getOperator(); //获取操作者
            $params['distributor_id'] = intval($this->body['distributor_id']); //分销商ID
            !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
            //$type = intval($this->body['type']) ? 1:0; //订单类型：0电子票订单1任务票订单

            $params['remark'] = trim(Tools::safeOutput($this->body['remark']));
            $params['ota_type'] = trim(Tools::safeOutput($this->body['ota_type']));
            $params['ota_account'] = intval($this->body['ota_account']);
            $params['ota_name'] = trim(Tools::safeOutput($this->body['ota_name']));
            $params['ota_type']=='ota' && (!$params['ota_account'] || !$params['ota_name']) && Lang_Msg::error('ERROR_OTA_1');

            $params['cartTicketList'] = array();
            $params['productIds'] = $this->body['product_ids'];
            !$params['productIds'] &&  $params['productIds'] = $this->body['ticket_template_ids'];
            $params['cartIds'] = $this->body['cart_ids'];
            (!is_array($params['productIds']) && preg_match("/^[\d,]+$/",$params['productIds'])) && $params['productIds'] = explode(',',$params['productIds']);
            (!is_array($params['cartIds']) && preg_match("/^[\d,]+$/",$params['cartIds'])) && $params['cartIds'] = explode(',',$params['cartIds']);
            if($params['productIds']) {
                $params['cartTicketList'] = CartModel::model()->search(array('ticket_id|IN'=>$params['productIds']));
            }
            else if($params['cartIds']) {
                $params['cartTicketList'] = CartModel::model()->search(array('id|IN'=>$params['cartIds']));
            }

            !$params['cartTicketList'] && $params['cartTicketList'] = $this->body['cartTicketList'];
            empty($params['cartTicketList']) && Lang_Msg::error('ERROR_TKT_3'); //请选择要订购的票

            $OrderModel = new OrderModel();
            $OrderModel->begin();
            $orders = $OrderModel->addBatchOrder($params);
            if($orders){
                $OrderModel->commit();
                Log_Order::model()->add(array('type'=>Log_Order::$type['CREATE'],'num'=>count($orders),'order_ids'=>implode(',',array_keys($orders)),'content'=>Lang_Msg::getLang('INFO_ORDER_1'),'distributor_id'=>$params['distributor_id']));
                Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'), $orders);
            }
            else {
                $OrderModel->rollback();
                Lang_Msg::error("ERROR_OPERATE_1");
            }
        } catch(Exception $e) {
            Log_Base::save('Order', 'error:'.$e->getMessage());
            Log_Base::save('Order', var_export($this->body,true));
            $OrderModel->rollback();
            Lang_Msg::error( 'ERROR_GLOBAL_3' );
        }
    }

    public function updateAction(){
        $OrderModel = new OrderModel();
        try{
            $operator = $this->getOperator(); //获取操作者
            $where = array('deleted_at'=>0);
            $id = trim(Tools::safeOutput($this->body['id']));
            $id && $where['id'] = $id;

            $source_id = trim(Tools::safeOutput(($this->body['source_id'])));
            $source_id && $where['source_id'] = $source_id; //按source_id查找

            !$id && !$source_id && Lang_Msg::error("ERROR_ORDER_INFO_1");

            $distributor_id = intval($this->body['distributor_id']);
            $distributor_id && $where['distributor_id'] = $distributor_id; //按分销商查找

            $supplier_id = intval($this->body['supplier_id']);
            $supplier_id && $where['supplier_id'] = $supplier_id; //按供应商查找

            $landscape_id = intval($this->body['landscape_id']);
            $landscape_id && $where['landscape_ids|EXP'] = "REGEXP '^{$landscape_id}$|^{$landscape_id},|,{$landscape_id},|,{$landscape_id}$'"; //按景区查找

            $detail = $OrderModel->setTable($id)->search($where);
            !$detail && Lang_Msg::error("ERROR_ORDER_INFO_3");
            $data = reset($detail);
            $id = $data['id'];

            $use_day = trim(Tools::safeOutput($this->body['use_day']));
            $payment =  trim(Tools::safeOutput($this->body['payment'])); //支付渠道:cash,offline,credit,pos,alipay,advance,union,kuaiqian,taobao
            $payment_id = trim(Tools::safeOutput($this->body['payment_id'])); //支付单号
            $payed = trim(Tools::safeOutput($this->body['payed']));
            $pay_at = trim(Tools::safeOutput($this->body['pay_at']));
            $refunded = trim(Tools::safeOutput($this->body['refunded']));
            $changed_useday_times = intval($this->body['changed_useday_times']);
            $send_sms_nums = intval($this->body['send_sms_nums']);
            $remark = trim(Tools::safeOutput($this->body['remark']));
            $status = trim(Tools::safeOutput($this->body['status'])); //订单状态：未支付、未确认|已取消|已支付、已确认|已结束|已结款
            $deleted = intval($this->body['deleted']);
            $used_nums = intval($this->body['used_nums']); //任务单可改
            $nums = intval($this->body['nums']); //OTA 可修改
            $owner_name = trim(Tools::safeOutput($this->body['owner_name'])); //取票人
            $owner_mobile = trim(Tools::safeOutput($this->body['owner_mobile'])); //取票人手机
            $owner_card = trim(Tools::safeOutput($this->body['owner_card'])); //取票人身份证

            $ota_type = trim(Tools::safeOutput($this->body['ota_type']));
            $ota_account = intval($this->body['ota_account']);
            $ota_name = trim(Tools::safeOutput($this->body['ota_name']));
            $ota_type=='ota' && (!$ota_account || !$ota_name) && Lang_Msg::error('ERROR_OTA_1');

            ($use_day && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$use_day)) && Lang_Msg::error('ERROR_USEDAY_2'); //游玩日期不能为空，且格式为xxxx-xx-xx
            ($status && !in_array($status,array('unpaid','cancel','paid','finish','billed'))) && Lang_Msg::error("ERROR_UPDATE_2"); //状态参数有错

            if($remark && $data['remark']!=$remark) {
                if (in_array($data['status'], array('unaudited','reject', 'unpaid'))) {
                    $data['status'] = 'unaudited'; //未支付订单如更改备注，则订单状态为待确认
                    $data['audit_status'] = 0;
                } else { //其他情况不能更改备注
                    Lang_Msg::error('ERROR_ORDER_16');//订单备注只能在未支付状态时才能更改
                }
            }

            if(isset($_POST['payment']) && $payment){
                if(in_array($payment,explode(',',$this->config['pay_type']['online'])))
                    $data['pay_type'] = 'online';
                else
                    $data['pay_type'] = $payment;
            }

            $use_day && $data['use_day'] = $use_day;
            isset($_POST['payment']) && $data['payment'] = $payment;
            $payment_id && $data['payment_id'] = $payment_id;
            isset($_POST['payed']) && $data['payed'] = $payed;
            isset($_POST['pay_at']) && $data['pay_at'] = $pay_at;
            isset($_POST['refunded']) && $data['refunded'] = $refunded;
            isset($_POST['changed_useday_times']) && $data['changed_useday_times'] += $changed_useday_times;
            isset($_POST['send_sms_nums']) && $data['send_sms_nums'] += $send_sms_nums;

            isset($_POST['owner_name']) && $data['owner_name'] = $owner_name;
            isset($_POST['owner_mobile']) && $data['owner_mobile'] = $owner_mobile;
            isset($_POST['owner_card']) && $data['owner_card'] = $owner_card;

            isset($_POST['remark']) && $data['remark'] = $remark;
            isset($_POST['nums']) && $ota_type == 'ota' && $data['nums'] = $nums; //ota可改
            $data['type']==1 && isset($_POST['used_nums']) && $data['used_nums'] = $used_nums;

            if (isset($_POST['status'])) {
                if($status=='cancel') {
                    if($data['pay_status']==2 || !in_array($data['status'],array('unaudited','reject','unpaid','cancel')))
                        Lang_Msg::error('该订单已支付，无法取消');
                    $data['cancel_status'] = 1;
                    $data['pay_status'] = 0;
                }
                else if ($status=='paid'){
                    $data['pay_status'] = 2;
                }
                $data['status'] = $status;
            }  //更改状态

            $now = time();
            if($deleted){
                $data['deleted_at']= $now;
                $operation_lang_id = 'INFO_ORDER_3';
            }
            else{
                $data['updated_at']= $now;
                $operation_lang_id = 'INFO_ORDER_2';
                //合并支付成功前某订单取消后，该支付单ID的订单恢复原状
                $payment_id = $data['payment_id'];
                if(isset($_POST['status']) && $status=='cancel' && $payment_id && !$data['pay_at']){
                    $data['pay_type'] = $data['payment'] = $data['payment_id'] = '';
                }
                if($owner_mobile || $owner_card){
                    $OrderModel->setPhoneCardMap($owner_mobile, $owner_card ,$id);
                }
            }
            if($use_day){ //更改游玩日期，重新计算游玩有效期
                $productInfo = TicketTemplateModel::model()->getInfo($data['product_id'],$data['price_type'],$data['distributor_id'],$use_day,0);
                !$productInfo && Lang_Msg::error('ERROR_TKT_2');
                isset($productInfo['code']) && $productInfo['code']=='fail' && Lang_Msg::error($productInfo['message']);
                if(strtotime($use_day.' 23:59:59')<$now) Lang_Msg::error('ERROR_USEDAY_3');

                $data['expire_start'] = strtotime($use_day);
                if(!$productInfo['valid_flag']){
                    $validTime = strtotime($use_day." 23:59:59") + intval($productInfo['valid'])*86400;
                    $data['expire_end'] = $validTime<$productInfo['expire_end']?$validTime:$productInfo['expire_end'];
                }
            }

            $OrderModel->begin();
            $r = $OrderModel->updateById($id,$data);
            if($r){
                if($use_day || isset($_POST['nums']) || ($data['type']==1 && isset($_POST['used_nums']))){
                    $r =  OrderItemModel::model()->setTable($id)->updateByAttr(array('use_day'=>$data['use_day'],'nums'=>$data['nums'],'used_nums'=>$data['used_nums']),array('order_id'=>$id));
                    if(!$r){
                        $OrderModel->rollback();
                        Lang_Msg::error('ERROR_OPERATE_1');
                    }
                }
                //合并支付成功前某订单取消后，该支付单ID的订单恢复原状
                if(isset($_POST['status']) && $status=='cancel' && $payment_id && !$data['pay_at']){
                    $r = PaymentOrderModel::model()->setTable($payment_id)->delete(array('payment_id'=>$payment_id,'order_id'=>$id));
                    if(!$r){
                        $OrderModel->rollback();
                        Lang_Msg::error('ERROR_OPERATE_1');
                    }

                    $paymentInfo = PaymentModel::model()->getById($payment_id);
                    $payOrderIds = implode(',',array_diff(explode(',',$paymentInfo['order_ids']),array($id)));
                    $r = PaymentModel::model()->updateById($payment_id,array('order_ids'=>$payOrderIds));
                    if(!$r) {
                        $OrderModel->rollback();
                        Lang_Msg::error('ERROR_OPERATE_1');
                    }
                }
                if(isset($_POST['owner_mobile'])){
                    $res = $this->regenerateCodeAction($data,$owner_mobile);
                    if(!$res){
                        throw new Lang_Exception('ERROR_OPERATE_1');
                    }
                    $data['code'] = $res;
                }
                $OrderModel->commit();
                Log_Order::model()->add(array('type'=>Log_Order::$type[($deleted?'DEL':'UPDATE')],'num'=>1,'order_ids'=>$id,'content'=>Lang_Msg::getLang($operation_lang_id),'distributor_id'=>$distributor_id));
                //$data['order_items'] = OrderItemModel::model()->search(array('order_id'=>$data['id']));
                Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'),$data);
            }
            $OrderModel->rollback();
            Lang_Msg::error('ERROR_OPERATE_1');
        } catch(Exception $e) {
            Log_Base::save('Order', 'error:'.$e->getMessage());
            Log_Base::save('Order', var_export($this->body,true));
            $OrderModel->rollback();
            Lang_Msg::error( 'ERROR_GLOBAL_3' );
        }
    }

    /**
     * 审核状态为unaudited待确认的订单
     * @author ：zhaqinfeng
     * 2015-01-29
     */
    public function checkOrderAction() {
        $where = array('deleted_at'=>0);
        $id = trim(Tools::safeOutput($this->body['id']));
        $allow = intval($this->body['allow']); //是否同意，1是0否
        !$id && Lang_Msg::error("ERROR_ORDER_INFO_1");
        $where['id'] = $id;

        $supplier_id = intval($this->body['supplier_id']);

        $OrderModel = new OrderModel();
        $detail = $OrderModel->setTable($id)->search($where);
        !$detail && Lang_Msg::error("ERROR_ORDER_INFO_3");
        $detail = $detail[$id];
        if($detail['supplier_id']!= $supplier_id) Lang_Msg::error( 'ERROR_ORDER_INFO_3' ); //该订单记录不存在
        else if(!in_array($detail['status'],array('unaudited','reject')) || $detail['audit_status']==1)
            Lang_Msg::error( 'ERROR_ORDER_17' ); //该订单已确认
        else if($detail['status']=='reject' || $detail['audit_status']==2)
            Lang_Msg::error( 'ERROR_ORDER_18' ); //该订单已驳回

        $reason = trim(Tools::safeOutput($this->body['reason']));
        if(!$allow && !$reason) Lang_Msg::error( 'ERROR_ORDER_19' ); //缺少驳回理由
        $r = $OrderModel->updateById($id,
            array('status'=>$allow?'unpaid':'reject','reason'=>$reason,'audit_status'=>$allow?1:2)
        );
        if($r){
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else Lang_Msg::error('ERROR_OPERATE_1');
    }

    /**
     * 批量获得订单ID的状态
     *
     */
    public function OrdersStatusAction()
    {
        !($this->body['order_ids']) && Lang_Msg::error('ERROR_ORDER_1');
        $order_ids = explode( ',', $this->body['order_ids'] );
        $return = array();
        $orders = OrderModel::model()->getByIds($order_ids);
        foreach( $orders as $order ){
            $return[ $order['id'] ] = $order[ 'status' ];
        }
        Lang_Msg::output( $return );
    }

    /**
     * 支付成功后，调用修改订单状态  - 微信调用
     *
     */
    public function UpdateStatusAction() {
        $order_id = null == $this->body[ 'order_id' ] ? Lang_Msg::error( '没有订单ID'): intval( $this->body[ 'order_id' ] );
        $org_id = null == $this->body[ 'organization_id' ] ? Lang_Msg::error('没有机构ID') : intval( $this->body[ 'organization_id' ] );
        $detail = OrderModel::model()->setTable( $order_id )->getById( $order_id );
        if( $detail[ 'supplier_id' ] != $org_id ) Lang_Msg::error( 'ERROR_ORDER_INFO_3' ); //该订单记录不存在
        if( $detail[ 'status' ] != 'unpaid' ) Lang_Msg::error( 'ERROR_ORDER_INFO_3' ); //该订单记录不存在
        $return = OrderModel::model()->setTable( $order_id )->updateById( $order_id , array( 'status' => 'paid','pay_status'=>2) );
        if( $return ) {
            $url = "http://www.piaotai.com/qr/".$order_id;
            $content = "您已成功预订".$detail['name']."门票".$detail['nums']."张，订单号：".$order_id."，您可在使用有效期内游玩，通过以下二维码链接进行手机二维码：".$url." 入园。";
            Sms::sendSMS( $detail[ 'owner_mobile' ], urlencode('【景旅通票台】'.$content),1,$order_id);
            Lang_Msg::output( $url );
        }
        else {
            Lang_Msg::error( 'ERROR_OPERATE_1' );
        }
    }

    //供应商按日统计票数最多的前5产品 @TODO
    public function supplierstatAction(){
        $top = intval($this->body['top']);
        !$top && $top = 5;
        $date = trim(Tools::safeOutput($this->body['date']));
        !$date && $date=date("Y-m-d");
        $supplier_id = intval($this->body['supplier_id']); //统计已打款、已结束、已结款

        !Validate::isDateFormat($date) && Lang_Msg::error('日期格式不正确');
        !$supplier_id && Lang_Msg::error('ERROR_SALER_1'); //缺少供应商ID参数

        $day2Time = $start_time = strtotime($date);
        $end_time = strtotime($date." 23:59:59");

        $OrderModel = new OrderModel();

        $fields = "id,name,product_id,supplier_id,SUM(nums-refunded_nums) AS ticket_nums,SUM(price*(nums-refunded_nums)) AS money_amount";
        $where = array(
            'supplier_id'=>$supplier_id,'created_at|>='=>$start_time,'created_at|<='=>$end_time,
            'status|in'=> array('paid','finish','billed'),'nums|exp'=>'>refunded_nums','product_id'>0
        );
        $groupby = 'product_id';
        $orderby = "ticket_nums DESC";

        $key = json_encode($where).$fields.$groupby.$orderby.$top;
        $cacheKey = md5($key);
        $result = Cache_Memcache::factory()->get($cacheKey);
        if(!$result){
            $result = $OrderModel->setGroupBy($groupby)->search($where,$fields,$orderby,$top);
            Cache_Memcache::factory()->set($cacheKey,$result,600);
        }
        Lang_Msg::output( $result );
    }

    //分销商按年、月统计票数 @TODO
    public function agencystatAction(){
        $top = intval($this->body['top']);
        $ym = trim(Tools::safeOutput($this->body['ym']));
        !$ym && $ym=date("Y-m");
        $year = trim(Tools::safeOutput($this->body['year']));
        !$year && $year=date("Y");
        $distributor_id = intval($this->body['distributor_id']); //统计已结款

        $ym && !preg_match("/^([0-9]{4})-((0?[0-9])|(1[0-2]))$/",$ym) && Lang_Msg::error('年月格式不正确');
        $year && !preg_match("/^([0-9]{4})$/",$year) && Lang_Msg::error('年份格式不正确');
        !$distributor_id && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数

        if(isset($_POST['ym'])){
            $ym2Time = $start_time = strtotime($ym);
            $days = date("t", mktime(0, 0, 0, date("m",$ym2Time), 1, date("Y",$ym2Time)));
            $end_time = strtotime($ym."-{$days} 23:59:59");

            $OrderModel = new OrderModel();

            $fields = "id,name,product_id,supplier_id,SUM(nums-refunded_nums) AS ticket_nums,SUM(price*(nums-refunded_nums)) AS money_amount,UNIX_TIMESTAMP(FROM_UNIXTIME(`created_at`,'%Y-%m-%d')) AS date";
            $where = array(
                'distributor_id'=>$distributor_id,'created_at|>='=>$start_time,'created_at|<='=>$end_time,
                'status'=> 'billed','nums|exp'=>'>refunded_nums','product_id|>'=>0
            );
            $groupby = 'date';
            $orderby = "date ASC";

            $key = json_encode($where).$fields.$groupby.$orderby;
            $cacheKey = md5($key);
            $result = Cache_Memcache::factory()->get($cacheKey);
            if(!$result){
                $tmp = $OrderModel->setGroupBy($groupby)->search($where,$fields,$orderby);
                $result = array();
                foreach($tmp as $v){
                    $result[date("j",$v['date'])] = $v;
                }
                for($d=1;$d<=$days;$d++){
                    $t = strtotime($ym."-".($d>9?$d:"0".$d));
                    !isset($result[$d]) && $result[$d] = array('distributor_id'=>$distributor_id,'ticket_nums'=>0,'money_amount'=>0,'date'=>$t);
                }
                Cache_Memcache::factory()->set($cacheKey,$result,600);
            }
            Lang_Msg::output( $result );
        }
        else if(isset($_POST['year'])){
            $year2Time = $start_time = strtotime($year."-01-01");
            $days = date("t", mktime(0, 0, 0, 12, 1, date("Y",$year2Time)));
            $end_time = strtotime($year."-12-{$days} 23:59:59");
            $AgencyTkStatModel = new AgencyTkStatModel();

            $tmp = $AgencyTkStatModel->select(array('distributor_id'=>$distributor_id,'created_at|>='=>$start_time,'created_at|<='=>$end_time),"*","created_at ASC");
            $result = array();
            foreach($tmp as $v){
                $result[date("n",$v['created_at'])] = $v;
            }
            for($n=1;$n<=12;$n++){
                $d = date("t", mktime(0, 0, 0, $n, 1, date("Y",$year2Time)));
                $t = strtotime($year."-".($n>9?$n:"0".$n)."-".($d>9?$d:"0".$d));
                !isset($result[$n]) && $result[$n] = array('distributor_id'=>$distributor_id,'created_at'=>$t,'ticket_nums'=>0,'money_amount'=>0);
            }

            Lang_Msg::output( $result );
        }
    }

    //生成订单并支付，支持支付方式union,credit,advance
    public function addPayAction(){
        $OrderModel = new OrderModel();
        $params = $this->getOperator(); //获取操作者

        $params['product_name'] = trim(Tools::safeOutput($this->body['product_name'])); //自定义产品名称，默认空
        $params['product_id'] = intval($this->body['product_id']); //票种id
        !$params['product_id'] &&  $params['product_id'] = intval($this->body['ticket_template_id']);
        $params['price_type'] = intval($this->body['price_type']) ? 1 : 0; //0散客1,团客
        $params['distributor_id'] = intval($this->body['distributor_id']); //分销商ID
        $params['use_day'] = trim(Tools::safeOutput($this->body['use_day'])); //游玩日期
        $params['nums'] = intval($this->body['nums']); //订购票数
        $params['owner_name'] = trim(Tools::safeOutput($this->body['owner_name'])); //取票人
        $params['owner_mobile'] = trim(Tools::safeOutput($this->body['owner_mobile'])); //取票人手机
        $params['owner_card'] = trim(Tools::safeOutput($this->body['owner_card'])); //取票人身份证
        $params['remark'] = trim(Tools::safeOutput($this->body['remark']));
        $params['payment'] = trim(Tools::safeOutput($this->body[ 'payment' ]));
        $params['ota_type'] = trim(Tools::safeOutput($this->body['ota_type']));
        $params['ota_account'] = intval($this->body['ota_account']);
        $params['ota_name'] = trim(Tools::safeOutput($this->body['ota_name']));
        $params['activity_paid'] = doubleval($this->body['activity_paid']); //抵用券金额

        $params['source'] = intval($this->body['source']);
        $params['local_source'] = trim(Tools::safeOutput($this->body['local_source']));
        $params['source_id'] = trim(Tools::safeOutput($this->body['source_id']));
        $params['source_token'] = trim(Tools::safeOutput($this->body['source_token']));

        isset($this->body['price']) && $params['price'] = doubleval($this->body['price']);

        !$params['product_id'] && Lang_Msg::error('ERROR_TKT_1'); //缺少票种ID参数
        !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
        (!$params['use_day'] || !preg_match("/^\d{4}-\d{2}-\d{2}$/",$params['use_day'])) && Lang_Msg::error('ERROR_USEDAY_1'); //游玩日期不能为空，且格式为xxxx-xx-xx
        !$params['nums'] && Lang_Msg::error('ERROR_TK_NUMS_1');
        !$params['owner_name'] && Lang_Msg::error('ERROR_OWNER_1');
        !$params['owner_mobile'] && Lang_Msg::error('ERROR_OWNER_2');
        $params['ota_type']=='ota' && (!$params['ota_account'] || !$params['ota_name']) && Lang_Msg::error('ERROR_OTA_1');

        $params['visitors']= trim($this->body['visitors']);

        $params['is_sms'] = intval($this->body['is_sms']); //是否发送短信发码，1是，0否
        !isset($this->body['is_sms']) && $params['is_sms'] = 1;

        if(!in_array($params['payment'],array('union','credit','advance','offline'))) {
            Lang_Msg::error('ERROR_PAYMENT_3',array('payment'=>$params['payment'])); //不支持该支付方式
        }

        $params['is_checked'] = intval($this->body['is_checked']);

        $OrderModel->begin();
        try{
            $order = $OrderModel->addOrder($params,true);
            if($order){
                $OtaAccountModel = new OtaAccountModel();
                if(isset($OtaAccountModel->config[$params['source']]) && $OtaAccountModel->config[$params['source']]['addAndPay']===0) { //淘宝、却哪儿等订单
                    $OrderModel->commit(); //先生成订单
                    $OrderModel->begin();  //再开始支付
                }
                $PaymentModel = new PaymentModel();
                if($params[ 'payment' ]=='offline') { //线下支付不生成支付单，直接改订单状态
                    if(!$PaymentModel->chgOrderStatusOnSucc($order['id'],array('status'=>'paid','pay_type'=>'offline','payment'=>'offline'))){
                        throw new Lang_Exception('ERROR_OPERATE_1');
                    }
                    //异步存入消息队列
                    $r = TicketQueueModel::model()->sendOrderIds(array($order['id']));
                    if ($r==false) {
                        throw new Lang_Exception('增加订单内容失败');
                    }
                } else {
                    $payInfo = $PaymentModel->addPayment(array(
                        'distributor_id'=>$order['distributor_id'],
                        'order_ids'=>$order['id'],
                        'payment'=>$params['payment'],
                        'status'=>'succ',
                        'remark'=>$order['remark'],
                        'user_id'=>$this->body['user_id']?$this->body['user_id']:$order['user_id'],
                        'activity_paid'=>$params['activity_paid'],//抵用券金额
                    ),1,$order['productInfo'],$params['source']);
                    if(!$payInfo['order_ids']){
                        throw new Lang_Exception('ERROR_PAYMENT_8');
                    }

                    $pay_money = $params['activity_paid']>=$order['amount']?0:($order['amount']-$params['activity_paid']);
                    if($params[ 'payment' ]=='union') {
                        $unionParams = array(
                            'org_id'=> $order['distributor_id'],
                            'user_id'=> $this->body['user_id']?$this->body['user_id']:$order['user_id'],
                            'user_account'=> $this->body['user_account']?$this->body['user_account']:$order['user_account'],
                            'user_name'=> $this->body['user_name']?$this->body['user_name']:$order['user_name'],
                            'money'=> $pay_money,
                            'in_out'=> 0,
                            'trade_type'=> 1,
                            'pay_type'=> 0,
                            'remark'=> $order['id'],
                        );
                        $dopay = ApiUnionMoneyModel::model()->unionInout($unionParams);
                    }
                    else {
                        $dopay = OrganizationModel::model()->creditPay(array(
                            'distributor_id'=>$order['distributor_id'],
                            'supplier_id' => $order['supplier_id'],
                            'money'=>$pay_money,
                            'type'=>$params[ 'payment' ]=='advance'?1:0,
                            'serial_id'=>$order['id'],
                        ));
                    }
                    if(!$dopay || $dopay['code']=='fail'){
                        $msg = Lang_Msg::getLang(!empty($dopay['message'])?$dopay['message']:'ERR_DOPAY_1');

                        if(isset($OtaAccountModel->config[$params['source']])) {
                            if($OtaAccountModel->config[$params['source']]['sendPayErrMessage']===1){ //给分销商发站内信
                                $content = $OtaAccountModel->getPayErrMessage($params['source'],array(
                                    'orderId'=>$order['id'], 'errMsg'=>$msg, 'paymentName'=>$PaymentModel->payments[$params['payment']],
                                ));
                                MessageModel::model()->addBase(array(
                                    'content'=>$content,
                                    'sms_type'=>1,
                                    'sys_type'=>3,
                                    'send_source'=>1,
                                    'send_status'=>1,
                                    'send_user'=>$this->body['user_id']?$this->body['user_id']:$order['user_id'],
                                    'send_organization'=>$order['supplier_id'],
                                    'receiver_organization'=>$order['distributor_id'],
                                    'organization_name'=>$order['distributor_name'],
                                    'receiver_organization_type'=>0,
                                ));
                            }
                            if($OtaAccountModel->config[$params['source']]['sendPayErrSmsMsg']===1){ //给分销商发短信
                                $content = $OtaAccountModel->getPayErrSmsMsg($params['source'],array(
                                    'orderId'=>$order['id'], 'errMsg'=>$msg, 'paymentName'=>$PaymentModel->payments[$params['payment']],
                                ));
                                $agency = OrganizationModel::model()->getInfo($order['distributor_id']);
                                if(isset($agency['mobile']) && $agency['mobile']){
                                    Sms::sendSMS($agency['mobile'],urlencode('【景旅通票台】'.$content));
                                }
                            }
                            throw new Lang_Exception('生成了订单 '.$order['id'].' 但'.$PaymentModel->payments[$params[ 'payment' ]].'支付失败（'.$msg.'）');
                        }
                        else{
                            throw new Lang_Exception($msg);
                        }
                    }
                }
                $OrderModel->commit();
                $order = $OrderModel->getById($order['id']);

                if($params['is_sms']==1 && !in_array($order['source'],[1,2]) && $order['local_source']!=2 && $order['message_open']==1 && empty($order['partner_type'])) {
                    //自由行不发短信 , 淘宝、大漠等码商合作伙伴此处不发短信，在异步通知下单成功后发短信
                    $str = Sms::_getCreateOrderContent($order);
                    Sms::sendSMS($order['owner_mobile'],urlencode($str),1,$order['id']);
                }
                Log_Order::model()->add(array('type'=>Log_Order::$type['CREATE'],'num'=>1,'order_ids'=>$order['id'],'content'=>Lang_Msg::getLang('INFO_ORDER_1'),'distributor_id'=>$params['distributor_id']));
                Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'), $order);
            }
            else{
                throw new Lang_Exception('ERROR_OPERATE_1');
            }
        } catch (Lang_Exception $e) {
            $msg = $e->getMessage();
            Lang_Msg::error($msg);
        } catch(Exception $e) {
            Log_Base::save('Order', '['.date('Y-m-d H:i:s').'] error:'.$e->getMessage());
            Log_Base::save('Order', var_export($this->body,true));
            $OrderModel->rollback();
            Lang_Msg::error( 'ERROR_OPERATE_1' );
        }
    }

    //检查票是否可购买,返回票信息 zqf
    public function checkTicketAction(){
        $product_id = intval($this->body['product_id']);
        !$product_id && $product_id = intval($this->body['ticket_template_id']);
        $distributor_id = intval($this->body['distributor_id']);
        $use_day = trim(Tools::safeOutput($this->body['use_day'])); //游玩日期
        $nums = intval($this->body['nums']); //订购票数
        $price_type = intval($this->body['price_type'])?1:0;

        !$product_id && Lang_Msg::error('ERROR_TKT_1'); //缺少票种ID参数
        !$distributor_id && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
        (!$use_day || !preg_match("/^\d{4}-\d{2}-\d{2}$/",$use_day)) && Lang_Msg::error('ERROR_USEDAY_1'); //游玩日期不能为空，且格式为xxxx-xx-xx
        !$nums && Lang_Msg::error('ERROR_TK_NUMS_1');
        $productInfo = TicketTemplateModel::model()->getInfo($product_id,$price_type,$distributor_id,$use_day,$nums);

        !$productInfo && Lang_Msg::error('ERROR_TKT_2');
        isset($productInfo['code']) && $productInfo['code']=='fail' && Lang_Msg::error($productInfo['message']);

        $nowtime = time();
        // 团体票限定人数 预定时间大于游玩时间
        if(strtotime($use_day.' 23:59:59')<$nowtime)
            Lang_Msg::error('ERROR_USEDAY_3');
        if($price_type==1 && ($nums<$productInfo['mini_buy']|| $nums >$productInfo['max_buy']))
            Lang_Msg::error('ERROR_ORDER_14');

        Lang_Msg::output($productInfo);
    }
    /**
     * 获取数据相同的订单
     * （门票名称， 游玩日期， 选择张数， 取票人名称）， （手机号，or 身份证）
     * @Author:Joe
     */
    public function getDuplicateAction()
    {
        $where = [];
        $product_id      = intval($this->body['product_id']); //商品ID
        $distributor_id  = intval($this->body['distributor_id']); //商品ID
        $extra           = json_decode(trim($this->body['extra']),true); //参数包

        if (empty($product_id) || empty($distributor_id) || !is_array($extra)) {
            return  Tools::lsJson(0, '请求参数不完整', []);
        }
        $Order = OrderModel::model();
        $whereCondition = '';
        foreach ($extra as $value) {
            $whereCondition .= "(1=1";
            foreach ($value as $k=>$v) {
                if ($k=='use_day') {
                    $use_day = $value['use_day'];
                    continue;
                }
                $whereCondition .= ' and ' . $k .'=\''. $v.'\'';
            }
            $whereCondition .= ") or ";
        }
        $whereCondition = substr($whereCondition, 0, -4);
        if (empty($use_day)) {
            return Tools::lsJson(0, '请求参数不完整', []);
        }
        $sql = "select id,owner_mobile,owner_name,nums,use_day,created_at,product_id,name from ".$Order->getTable()." where ".
        "product_id=".$product_id." and distributor_id=".$distributor_id." and use_day='".$use_day."'".
        ' and ('.$whereCondition .") order by created_at desc";
        $orders = $Order->db->selectBySql($sql);
        Lang_Msg::output($orders);
    }

    /**
     * 获取数据相同的订单
     * （门票名称， 游玩日期， 选择张数， 取票人名称）， （手机号，or 身份证）
     * @Author:Joe
     */
    public function getDuplicateBackAction() {
        $product_id      = intval($this->body['product_id']); //商品ID
        $distributor_id  = intval($this->body['distributor_id']); //商品ID
		$extra           = json_decode(trim($this->body['extra']),true); //参数包

		if (empty($product_id) || empty($distributor_id) || !is_array($extra)) {
			return  Tools::lsJson(0, '请求参数不完整', []);
		}

		$Order = OrderModel::model();
		$whereCondition = '';
		foreach ($extra as $value) {
			$whereCondition .= "(1=1";
			foreach ($value as $k=>$v) {
				if ($k=='use_day') {
					$use_day = $value['use_day'];
					continue;
				}
				$whereCondition .= ' and ' . $k .'=\''. $v.'\'';
			}
			$whereCondition .= ") or ";
		}
		$whereCondition = substr($whereCondition, 0, -4);
		if (empty($use_day)) {
			return Tools::lsJson(0, '请求参数不完整', []);
		}

		$sql = "select id,owner_mobile,owner_name,nums,use_day,created_at,product_id,name from ".$Order->getTable()." where ".
		"status<>'cancel' and product_id=".$product_id." and distributor_id=".$distributor_id." and use_day='".$use_day."'".
		' and ('.$whereCondition .") order by created_at desc";
		$orders = $Order->db->selectBySql($sql);
		Lang_Msg::output($orders);
    }

    //手动短信发码 支持openApi,需返回剩余可核销数
    public function smsAction(){
        $where = array('deleted_at'=>0);
        $id = trim(Tools::safeOutput($this->body['id']));
        $id && $where['id'] = $id;

        $source_id = trim(Tools::safeOutput($this->body['source_id']));
        $source_id && $where['source_id'] = $source_id;

        $source_token = trim(Tools::safeOutput($this->body['source_token']));
        $source_token && $where['source_token'] = $source_token;


        !$id && !$source_id && !$source_token && Lang_Msg::error("ERROR_ORDER_INFO_1");

        $detail = OrderModel::model()->setTable($id)->search($where,$this->getFields());
        !$detail && Lang_Msg::error("ERROR_ORDER_INFO_3");
        $order = reset($detail);

        $url = SmsModel::model()->getCodeUrl($order['code']);
        $str = Sms::_getCreateOrderContent($order);
        Sms::sendSMS($order['owner_mobile'],urlencode($str),1,$order['id']);

        $unuse_num = $order['nums']-$order['used_nums']-$order['refunded_nums']-$order['refunding_nums'];
        $unuse_num<0 && $unuse_num=0;
        Lang_Msg::output( array('order_id'=>$order['id'],'unuse_num'=>$unuse_num,'url'=>$url) );
    }

    /**
     * 修改手机号
     * author : yinjian
     */
    private function regenerateCodeAction($data,$owner_mobile)
    {
        $code = Util_Common::uniqid(5);
        $res = OrderModel::model()->updateById(
            array('id'=>$data['id']),
            array(
                'code' => $code,
            )
        );
        if(!$res){
            return false;
        };
        OrderModel::model()->delPhoneCardMap( $data['owner_mobile'], $data['owner_card'], $data['id'] );
        OrderModel::model()->setPhoneCardMap( $owner_mobile, $data['owner_card'], $code );
        return $code;
    }

}