<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-23
 * Time: 下午3:25
 */

class OrderController extends Base_Controller_Api {

    public function listsAction(){
        $fields = trim(Tools::safeOutput($this->body['fields']));
        $fields = $fields ? $fields :"*"; //要获取的字段
        $fieldArr = array();
        if($fields!="*"){
            $fieldArr = explode(',',$fields);
            !in_array('id',$fieldArr) && array_unshift($fieldArr,'id');
            $fields = implode(',',$fieldArr);
        }
        $order = $this->getSortRule();
        $where = array('deleted_at'=>0);

        $distributor_id = intval($this->body['distributor_id']);
        $distributor_id && $where['distributor_id'] = $distributor_id; //按分销商查找

        $supplier_id = intval($this->body['supplier_id']);
        $supplier_id && $where['supplier_id'] = $supplier_id; //按供应商查找

        $landscape_id = intval($this->body['landscape_id']);
        $landscape_id && $where['landscape_ids|EXP'] = "REGEXP '^{$landscape_id}$|^{$landscape_id},|,{$landscape_id},|,{$landscape_id}$'"; //按景区查找

        $kind = intval($this->body['kind']); //种类:1单票2联票3套票
        $kind && $where['kind'] = $kind;

        $start_date = trim(Tools::safeOutput($this->body['start_date']));
        ($start_date && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$start_date)) && Lang_Msg::error('ERROR_START_DAY_1');
        $start_date && $where['use_day|>='] = $start_date;
        $tableYm = $start_date ? strtotime($start_date):'' ;

        $end_date = trim(Tools::safeOutput($this->body['end_date']));
        ($end_date && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$end_date)) && Lang_Msg::error('ERROR_END_DAY_1');
        $end_date && $where['use_day|<='] = $end_date;

        $ticket_name = trim(Tools::safeOutput($this->body['ticket_name'])); //按门票名称查询
        if($ticket_name){
            $itemWhere = $where;
            $ticket_type = intval($this->body['type']) ? 1:0; //订单类型：0电子票订单1任务票订单
            isset($_POST['type']) && $itemWhere['ticket_type'] = $ticket_type;

            $itemWhere['name|LIKE'] = array("%{$ticket_name}%");
            $orderItems = OrderItemModel::model()->share($tableYm)->search($itemWhere,"order_id as id,name");
            if($orderItems)
                $where['id|IN'] = array_keys($orderItems);
            else{
                $this->pagenation();
                $result = array(
                    'data'=>array(),
                    'pagination'=>array(  'count'=>$this->count,   'current'=>$this->current,  'items'=>$this->items,  'total'=>$this->total,)
                );
                Lang_Msg::output($result);
            }
        }
        else{
            $id = trim(Tools::safeOutput($this->body['id']));
            $id && $where['id'] = $id; //按订单号查找

            $ids = $this->body['ids'];
            (!is_array($ids) && preg_match("/^[\d,]+$/",$ids)) && $ids = explode(',',$ids);
            $ids && $where['id|IN'] = $ids; //按订单ID查找

            $type = intval($this->body['type']) ? 1:0; //订单类型：0电子票订单1任务票订单
            isset($_POST['type']) && $where['type'] = $type;
        }

        $owner_name = trim(Tools::safeOutput($this->body['owner_name']));
        $owner_name && $where['owner_name'] = $owner_name; //按取票人

        $owner_mobile = trim(Tools::safeOutput($this->body['owner_mobile']));
        $owner_mobile && $where['owner_mobile'] = $owner_mobile; //按取票人手机号

        $owner_card = trim(Tools::safeOutput($this->body['owner_card']));
        $owner_card && $where['owner_card'] = $owner_card; //按取票人身份证

        $status = trim(Tools::safeOutput($this->body['status']));
        in_array($status,array('unpaid','cancel','paid','finish','billed')) &&  $where['status'] = $status; //按状态查找


        $OrderModel = new OrderModel();
        $this->count = $OrderModel->share($tableYm)->countResult($where);
        $this->pagenation();
        $pagination = Tools::getPagination($this->getParams(),$this->count);
        $data = $this->count>0  ? $OrderModel->share($tableYm)->search($where,$fields,$order,$this->limit) : array();
        if($data){
            $order_ids = array_keys($data);
            $items  = OrderItemModel::model()->share($tableYm)->search(array('order_id|IN'=>$order_ids),"order_id as id,ticket_template_id,price_type,distributor_id,name,use_day,nums");
            foreach($data as $k=>$v){
                $data[$k]['name'] = empty($items[$k]['name']) ? '':$items[$k]['name'];
                $data[$k]['ticket_template_id'] = empty($items[$k]['ticket_template_id']) ? '':$items[$k]['ticket_template_id'];
            }

            $ticketNums = array();
            if($this->body['with_store']){
                foreach ($items as $iv) {
                    $key = $iv['ticket_template_id']."_".$iv['use_day'];
                    if(!isset($ticketNums[$key])) {
                        $ticketNums[$key] = array(
                            //'ticket_template_id'=>$iv['ticket_template_id'],'use_day'=>$iv['use_day'],'nums'=>0,'name'=>$iv['name']
                        );
                        $ticketTemplateInfo = TicketTemplateModel::model()->getInfo($iv['ticket_template_id'],$iv['price_type'],$iv['distributor_id'],$iv['use_day'],0);
                        if($ticketTemplateInfo && !isset($ticketTemplateInfo['code'])) {
                            //$ticketNums[$key]['day_reserve']= $ticketTemplateInfo['day_reserve'];
                            //$ticketNums[$key]['used_reserve']= $ticketTemplateInfo['used_reserve'];
                            $ticketNums[$key]['remain_reserve']= 1==$ticketTemplateInfo['state'] ? $ticketTemplateInfo['remain_reserve']:0;
                        }
                        else{
                            $ticketNums[$key]['remain_reserve']= 0;
                        }
                    }
                    //$ticketNums[$key]['nums'] += $iv['nums'];
                }
            }
        }

        $result = array(
            'data'=>array_values($data),
            'pagination'=>array(  'count'=>$this->count,  'current'=>$this->current, 'items'=>$this->items, 'total'=>$this->total, )
        );
        if($this->body['with_store'] && $ticketNums){
            $result['with_store'] = $ticketNums;
        }
        Lang_Msg::output($result);
    }

    public function detailAction(){
        $fields = trim(Tools::safeOutput($this->body['fields']));
        $fields = $fields ? $fields :"*"; //要获取的字段
        $fieldArr = array();
        if($fields!="*"){
            $fieldArr = explode(',',$fields);
            !in_array('id',$fieldArr) && array_unshift($fieldArr,'id');
            $fields = implode(',',$fieldArr);
        }

        $where = array('deleted_at'=>0);
        $id = trim(Tools::safeOutput($this->body['id']));
        !$id && Lang_Msg::error("ERROR_ORDER_INFO_1");
        $where['id'] = $id;

        $distributor_id = intval($this->body['distributor_id']);
        $distributor_id && $where['distributor_id'] = $distributor_id; //按分销商查找

        $supplier_id = intval($this->body['supplier_id']);
        $supplier_id && $where['supplier_id'] = $supplier_id; //按供应商查找

        $landscape_id = intval($this->body['landscape_id']);
        $landscape_id && $where['landscape_ids|EXP'] = "REGEXP '^{$landscape_id}$|^{$landscape_id},|,{$landscape_id},|,{$landscape_id}$'"; //按景区查找

        // (!$distributor_id && !$supplier_id && !$landscape_id) && Lang_Msg::error('ERROR_ORDER_INFO_2');
        $detail = OrderModel::model()->setTable($id)->search($where,$fields);
        !$detail && Lang_Msg::error("ERROR_ORDER_INFO_3");
        $detail = $detail[$id];

        $items = OrderItemModel::model()->setTable($id)->search(array('order_id'=>$id));
        $detail['order_items'] = array_values($items);
        Lang_Msg::output($detail);
    }


    /**
    * 添加订单，单个添加，如单个票上预订
    */
    public function addAction(){
        $params = $this->getOperator(); //获取操作者


        $params['ticket_template_id'] = intval($this->body['ticket_template_id']); //票种id
        $params['price_type'] = intval($this->body['price_type']) ? 1 : 0; //0散客1,团客
        $params['distributor_id'] = intval($this->body['distributor_id']); //分销商ID
        $params['use_day'] = trim(Tools::safeOutput($this->body['use_day'])); //游玩日期
        $params['nums'] = intval($this->body['nums']); //订购票数
        $params['owner_name'] = trim(Tools::safeOutput($this->body['owner_name'])); //取票人
        $params['owner_mobile'] = trim(Tools::safeOutput($this->body['owner_mobile'])); //取票人手机
        $params['owner_card'] = trim(Tools::safeOutput($this->body['owner_card'])); //取票人身份证
        $params['remark'] = trim(Tools::safeOutput($this->body['remark']));

        !$params['ticket_template_id'] && Lang_Msg::error('ERROR_TKT_1'); //缺少票种ID参数
        !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
        (!$params['use_day'] || !preg_match("/^\d{4}-\d{2}-\d{2}$/",$params['use_day'])) && Lang_Msg::error('ERROR_USEDAY_1'); //游玩日期不能为空，且格式为xxxx-xx-xx
        !$params['nums'] && Lang_Msg::error('ERROR_TK_NUMS_1');
        !$params['nums'] && Lang_Msg::error('ERROR_TK_NUMS_1');
        !$params['owner_name'] && Lang_Msg::error('ERROR_OWNER_1');
        !$params['owner_mobile'] && Lang_Msg::error('ERROR_OWNER_2');
        //!$order['owner_card'] && Lang_Msg::error('ERROR_OWNER_3');

        //微信生成订单
        if( $this->body['pay_type'])  $params[ 'pay_type' ] = $this->body[ 'pay_type' ];
        if( $this->body[ 'payment' ]) $params[ 'payment' ] = $this->body[ 'payment' ];

        $OrderModel = new OrderModel();
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
    }

    /**
     * 添加订单，批量添加
     */
    public function addbatchAction() {
        $params = $this->getOperator(); //获取操作者
        $params['distributor_id'] = intval($this->body['distributor_id']); //分销商ID
        !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
        //$type = intval($this->body['type']) ? 1:0; //订单类型：0电子票订单1任务票订单

        $params['cartTicketList'] = array();
        $params['ticketTemplateIds'] = $this->body['ticket_template_ids'];
        (!is_array($params['ticketTemplateIds']) && preg_match("/^[\d,]+$/",$params['ticketTemplateIds'])) && $params['ticketTemplateIds'] = explode(',',$params['ticketTemplateIds']);
        if($params['ticketTemplateIds']){
            $params['cartTicketList'] = CartModel::model()->search(array('ticket_id|IN'=>$params['ticketTemplateIds']));
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
        else{
            $OrderModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }
    }

    public function updateAction(){
        $operator = $this->getOperator(); //获取操作者
        $where = array('deleted_at'=>0);
        $id = trim(Tools::safeOutput($this->body['id']));
        !$id && Lang_Msg::error("ERROR_ORDER_INFO_1");
        $where['id'] = $id;

        $distributor_id = intval($this->body['distributor_id']);
        $distributor_id && $where['distributor_id'] = $distributor_id; //按分销商查找

        $supplier_id = intval($this->body['supplier_id']);
        $supplier_id && $where['supplier_id'] = $supplier_id; //按供应商查找

        $landscape_id = intval($this->body['landscape_id']);
        $landscape_id && $where['landscape_ids|EXP'] = "REGEXP '^{$landscape_id}$|^{$landscape_id},|,{$landscape_id},|,{$landscape_id}$'"; //按景区查找

        //(!$distributor_id && !$supplier_id && !$landscape_id) && Lang_Msg::error('ERROR_ORDER_INFO_2');
        $OrderModel = new OrderModel();
        $detail = $OrderModel->setTable($id)->search($where);
        !$detail && Lang_Msg::error("ERROR_ORDER_INFO_3");
        $data = $detail[$id];

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

        ($use_day && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$use_day)) && Lang_Msg::error('ERROR_USEDAY_2'); //游玩日期不能为空，且格式为xxxx-xx-xx
        ($status && !in_array($status,array('unpaid','cancel','paid','finish','billed'))) && Lang_Msg::error("ERROR_UPDATE_2"); //状态参数有错

        $use_day && $data['use_day'] = $use_day;
        isset($_POST['payment']) && $data['payment'] = $payment;
        $payment_id && $data['payment_id'] = $payment_id;
        isset($_POST['payed']) && $data['payed'] = $payed;
        isset($_POST['pay_at']) && $data['pay_at'] = $pay_at;
        isset($_POST['refunded']) && $data['refunded'] = $refunded;
        isset($_POST['changed_useday_times']) && $data['changed_useday_times'] += $changed_useday_times;
        isset($_POST['send_sms_nums']) && $data['send_sms_nums'] += $send_sms_nums;
        isset($_POST['remark']) && $data['remark'] = $remark;
        isset($_POST['status']) && $data['status'] = $status;  //更改状态
        $data['type']==1 && isset($_POST['used_nums']) && $data['used_nums'] = $used_nums;

        if($deleted){
            $data['deleted_at']= time();
            $operation_lang_id = 'INFO_ORDER_3';
        }
        else{
            $data['updated_at']= time();
            $operation_lang_id = 'INFO_ORDER_2';
        }

        $OrderModel->begin();
        $r = $OrderModel->updateById($id,$data);
        if($r){
            if($use_day || ($data['type']==1 && isset($_POST['used_nums']))){
                $r =  OrderItemModel::model()->setTable($id)->updateByAttr(array('use_day'=>$use_day,'used_nums'=>$data['used_nums']),array('order_id'=>$id));
                if(!$r){
                    $OrderModel->rollback();
                    Lang_Msg::error('ERROR_OPERATE_1');
                }
            }
            $OrderModel->commit();
            Log_Order::model()->add(array('type'=>Log_Order::$type[($deleted?'DEL':'UPDATE')],'num'=>1,'order_ids'=>$id,'content'=>Lang_Msg::getLang($operation_lang_id),'distributor_id'=>$distributor_id));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'),$data);
        }
        $OrderModel->rollback();
        Lang_Msg::error('ERROR_OPERATE_1');
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
    public function UpdateStatusAction()
    {		  
    	  $order_id = null == $this->body[ 'order_id' ] ? Lang_Msg::error( '没有订单ID'): intval( $this->body[ 'order_id' ] );
    	  $org_id = null == $this->body[ 'organization_id' ] ? Lang_Msg::error('没有机构ID') : intval( $this->body[ 'organization_id' ] );
    	  $tmp = OrderModel::model()->setTable( $order_id )->getById( $order_id );
    	  if( $tmp[ 'supplier_id' ] != $org_id ) Lang_Msg::error( 'ERROR_BILL_2' );
    	  if( $tmp[ 'status' ] != 'unpaid' ) Lang_Msg::error( 'ERROR_BILL_2' );
    	  $return = OrderModel::model()->setTable( $order_id )->updateById( $order_id , array( 'status' => 'paid') );
    	  if( $return )
    	  {  
    	  	$item = reset( OrderItemModel::model()->setTable( $order_id )->search(  array( 'order_id' => $order_id ) ) );
    	  	$url = "http://www.piaotai.com/qr/".$order_id;
    	  	$content = "您已成功预订".$item['name']."门票".$item['nums']."张，订单号：".$order_id."，您可在使用有效期内游玩，通过以下二维码链接进行手机二维码：".$url." 入园。"; 
    	  	Sms::sendSMS( $tmp[ 'owner_mobile' ], urlencode('【景旅通票台】'.$content));
    	  	Lang_Msg::output( $url );
    	  }
    	  else
    	  {
    	  	Lang_Msg::error( 'ERROR_OPERATE_1' );
    	  }
    	  
    }

    //供应商按日统计票数最多的前5产品
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

        $OrderItemModel = new OrderItemModel();
        $OrderModel = new OrderModel();

        $fields = "i.name,i.ticket_template_id,i.supplier_id,SUM(i.`nums`-i.`refunded_nums`) AS ticket_nums,SUM(i.`price`*(i.`nums`-i.`refunded_nums`)) AS money_amount";
        $from = $OrderItemModel->share($day2Time)->getTable()."` `i` JOIN `".$OrderModel->share($day2Time)->getTable()."` `o";
        $where = " i.supplier_id={$supplier_id} AND i.order_id=o.id AND i.created_at >= ".$start_time." AND i.created_at<=".$end_time." ";
        $where.=" AND o.status IN ('paid','finish','billed') AND 0<i.`nums`-i.`refunded_nums`";
        $where.=" GROUP BY i.ticket_template_id ";
        $orderby = "ticket_nums DESC";
        $limit = $top;
        $cacheKey = md5($fields.$from.$where.$orderby.$limit);
        $result = Cache_Memcache::factory()->get($cacheKey);
        if(!$result){
            $result = $OrderItemModel->getDb()->select($from,$where,$fields,$orderby,$limit);
            Cache_Memcache::factory()->set($cacheKey,$result,600);
        }
        Lang_Msg::output( $result );
    }

    //分销商按年、月统计票数
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

            $OrderItemModel = new OrderItemModel();
            $OrderModel = new OrderModel();

            $fields = "i.distributor_id,SUM(i.`nums`-i.`refunded_nums`) AS ticket_nums,SUM(i.`price`*(i.`nums`-i.`refunded_nums`)) AS money_amount,UNIX_TIMESTAMP(FROM_UNIXTIME(i.`created_at`,'%Y-%m-%d')) AS date";
            $from = $OrderItemModel->share($ym2Time)->getTable()."` `i` JOIN `".$OrderModel->share($ym2Time)->getTable()."` `o";
            $where = " i.distributor_id={$distributor_id} AND i.order_id=o.id AND i.created_at >= ".$start_time." AND i.created_at<=".$end_time." ";
            $where.=" AND o.status='billed' AND 0<i.`nums`-i.`refunded_nums`";
            $where.=" GROUP BY date";
            $orderby = "date ASC";
            $cacheKey = md5($fields.$from.$where.$orderby);
            $result = Cache_Memcache::factory()->get($cacheKey);
            if(!$result){
                $y = date("Y",$start_time);
                $tmp = $OrderItemModel->getDb()->select($from,$where,$fields,$orderby);
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

}