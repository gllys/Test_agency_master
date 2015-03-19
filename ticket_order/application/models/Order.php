<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-23
 * Time: 下午5:05
 */

class OrderModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'orders';
    protected $basename = 'orders';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|OrderModel|';
    public $tradeNoKey = 'OrderModel|trade2order|';
    protected $phonePreCacheKey = 'OrderModel|phone2order|';
    protected $cardPreCacheKey = 'OrderModel|card2order|';
    protected $autoShare = 1;

    public function getTable() {
        return $this->tblname;
    }

    public function setTable($id = 0) {
    	if (!$id) $this->tblname = $this->basename . date('Ym');
    	else  $this->tblname = $this->basename . Util_Common::uniqid2date($id);
    	return $this;
    }

    public function share($ts = 0) {
        if (!$ts) $ts = time();
        $this->tblname = $this->basename . date('Ym', $ts);
        return $this;
    }

    //添加单条订单
    public function addOrder($params){
        //获取票种含价格库存判断
        $params['ticketTemplateInfo'] = TicketTemplateModel::model()->getInfo($params['ticket_template_id'],$params['price_type'],$params['distributor_id'],$params['use_day'],$params['nums']);
        !$params['ticketTemplateInfo'] && Lang_Msg::error('ERROR_TKT_2');
        isset($params['ticketTemplateInfo']['code']) && $params['ticketTemplateInfo']['code']=='fail' && Lang_Msg::error($params['ticketTemplateInfo']['message']);

        $nowtime = time();

        // 团体票限定人数 预定时间大于游玩时间
        if(strtotime($params['use_day'].' 23:59:59')<$nowtime) Lang_Msg::error('ERROR_USEDAY_3');
        if( $params['price_type']==1 && ($params['nums']<$params['ticketTemplateInfo']['mini_buy']|| $params['nums'] >$params['ticketTemplateInfo']['max_buy'])) Lang_Msg::error('ERROR_ORDER_14');
        if (!$params['order_id']) $params['order_id'] = Util_Common::uniqid(1);
        $order = array(
            'id'=>$params['order_id'], //参数1，订单
            'type'=>$params['ticketTemplateInfo']['type'],
            'kind'=>$params['ticketTemplateInfo']['is_union']==0?1:2, //种类:1单票2联票3套票
            'status'=>'unpaid',
            'created_by'=>$params['user_id'],
            'created_at'=>$nowtime,
            'updated_at'=>$nowtime
        );
        $order['distributor_id'] = $params['distributor_id']; //分销商ID
        $order['supplier_id'] = $params['ticketTemplateInfo']['organization_id']; //供应商ID
        $order['landscape_ids'] = $params['ticketTemplateInfo']['scenic_id']; //景区id

        $orgs = OrganizationModel::model()->getList(array($order['supplier_id'],$order['distributor_id']));
        $order['supplier_name'] = empty($orgs['data'][$order['supplier_id']])?' ':$orgs['data'][$order['supplier_id']]['name'];
        $order['distributor_name'] = empty($orgs['data'][$order['distributor_id']])?' ':$orgs['data'][$order['distributor_id']]['name'];

        $order['price_type'] = $params['price_type']?$params['price_type']:0;
        $order['use_day'] = $params['use_day']?$params['use_day']:date('Y-m-d'); //游玩日期
        $order['nums'] = $params['nums']; //订购票数
        $order['amount'] = $params['nums'] * $params['ticketTemplateInfo']['price']; //订单结算金额
        $order['owner_name'] = $params['owner_name']; //取票人
        $order['owner_mobile'] = $params['owner_mobile']; //取票人手机
        $order['owner_card'] = $params['owner_card']; //取票人身份证
        $order['remark'] = $params['remark']?$params['remark']:'';
        $order['ota_type'] = $params['ticketTemplateInfo'][ 'ota_type' ];
        $order['ota_account'] = !empty($params['ota_account'])?$params['ota_account']:0;
        $order['ota_name'] = !empty($params['ota_name'])?$params['ota_name']:'';
        isset($params['pay_type']) && $params['pay_type'] && $order['pay_type'] = $params['pay_type'];
        isset($params['payment']) && $params['payment'] && $order['payment'] = $params['payment'];

        $order['user_id'] = $params['user_id'];
        $order['user_account'] = $params['user_account'];
        $order['user_name'] = $params['user_name'];
        $r = $this->add($order);
        //根据phone, card 添加到redis
        $this->setPhoneCardMap( $order['owner_mobile'], $order['owner_card'] ,$order[ 'id' ]);
        if($r){
            //添加订单明细
            $r = OrderItemModel::model()->addNew($order,$params['ticketTemplateInfo']);
            if($r){
                //添加票
                $r = TicketModel::model()->addNew($order,$params['ticketTemplateInfo']);
                if($r){
                    return $order;
                }
            }
        }
        return false;
    }

    /**
     * 订单，订单明细，支付单，支付单明细，订单的票跟子景点关联，生成票号，生成流水
     * 新建拉手订单并完成支付
     * author : yinjian
     */
    public function addLashouOrder($buy_info,$ticketTemplateInfo)
    {
        try {
            $this->begin();
            // 初始基础信息
            $nowtime = time();
            $order_id = Util_Common::uniqid(1);
            $payment_id = Util_Common::payid();
            $transaction_flow_id = Util_Common::payid();
            // 信用储值扣款 @TODO
            $pay_type_map = array('credit'=>1,'advance'=>0);
            if(in_array($buy_info['pay_type'],array('credit','advance'))){
                $info['distributor_id'] = $buy_info['distributor_id'];
                $info['supplier_id'] = $ticketTemplateInfo['organization_id'];
                $info['type'] = $pay_type_map[$buy_info['pay_type']];
                $info['money'] = '-'.$buy_info['amount'];
                $info['serial_id'] = $transaction_flow_id;
                // @TODO 获取操作者
                $info['op_id'] = 0;
                $res = OrganizationModel::model()->pay_credit($info);
                if(!$res){
                    return false;
                }
            }
            // orders
            $orderInfo = array(
                'id' => $order_id,
                'type' => $ticketTemplateInfo['type'],
                'kind' => $ticketTemplateInfo['is_union']==0?1:2,
                'status' => 'paid',
                'nums' => $buy_info['nums'],
                'amount' => $buy_info['amount'],
                'use_day' => $buy_info['use_day'],
                'pay_type' => $buy_info['pay_type'],
                'payment' => $buy_info['payment'],
                'payment_id' => $payment_id,
                'payed' => $buy_info['amount'],
                'pay_at' => $buy_info['pay_at'],
                'owner_name' => $buy_info['owner_name'],
                'owner_mobile' => $buy_info['owner_mobile'],
                'remark' => $buy_info['trade_no'],
                'distributor_id' => $buy_info['distributor_id'],
                'distributor_name'=> $buy_info['distributor_name'],
                'supplier_id' => $ticketTemplateInfo['organization_id'],
                'supplier_name' => $buy_info['supplier_name'],
                'landscape_ids' => $ticketTemplateInfo['scenic_id'],
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'ota_name' => $buy_info['owner_name'],
            );
            $this->add($orderInfo);
            // 调整原有门票有效期
            $d1 = strtotime($buy_info['use_day'].' 00:00:00');
            $ticketTemplateInfo['valid'] = round(($ticketTemplateInfo['expire_end']-$d1)/3600/24);
            // order_items
            OrderItemModel::model()->addNew($orderInfo,$ticketTemplateInfo);
            // payments
            $paymentInfo = array(
                'id'=> $payment_id,
                'distributor_id'=>$buy_info['distributor_id'],
                'order_ids'=> $order_id,
                'status'=> 'succ',
                'pay_type'=> $buy_info['pay_type'], //支付方式类型：线上、线下、信用支付、储值支付
                'payment'=> $buy_info['payment'], //支付渠道:cash,pos,offline,credit,advance,union,alipay,kuaiqian,taobao
                'amount'=> $buy_info['amount'],
                'account'=>$buy_info['owner_name'],
                'pay_account'=>$buy_info['owner_name'],
                'remark'=>$buy_info['trade_no'],
                'ip'=> Tools::getIp(),
//                'op_id'=> $params['user_id'],
                'created_at'=> $nowtime,
                'updated_at'=> $nowtime);
            PaymentModel::model()->add($paymentInfo);
            // payment_orders
            PaymentOrderModel::model()->add(array(
                'id' => Util_Common::payid(),
                'payment_id' => $paymentInfo['id'],
                'order_id' => $order_id,
                'money' => $buy_info['amount'],
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
            ));
            // tickets
            $ticket_ids =TicketModel::model()->addNew($orderInfo,$ticketTemplateInfo);
            // ticket_relations
            TicketRelationModel::model()->addNew($orderInfo,$ticketTemplateInfo,$ticket_ids);
            // transaction_flow
            $transflowParam = array(
                'id' => $transaction_flow_id,
                'mode' => $buy_info['payment'],
                'type' => 1,
                'amount' => $buy_info['amount'],
                'supplier_id' => $ticketTemplateInfo['organization_id'],
                'agency_id' => $buy_info['distributor_id'],
                'ip' => $paymentInfo['ip'],
//                'op_id' => $operator['user_id'],
                'created_at' => $nowtime,
                'order_id' => $order_id,
            );
            TransactionFlowModel::model()->add($transflowParam);
            // log_order
            Log_Payment::model()->add(array(
                    'type' => 1,
                    'num' => 1,
                    'payment_id' => $paymentInfo['id'],
                    'order_ids' => $order_id,
                    'content' => Lang_Msg::getLang('INFO_PAYMENT_1',array(
                        'id' => $paymentInfo['id'],
                        'order_ids' => $order_id
                    ))
                ));
            $this->commit();
            return $order_id;
        } catch (PDOException $e) {
            // 回滚事务
            $this->rollBack();
//            Tools::dump($e->errorInfo);
            return false;
        }
    }

    //批量添加订单
    public function addBatchOrder($params){
        $orders = $fields = $ticketTemplateInfos= $orgIds = array();
        $nowtime = time();
        foreach($params['cartTicketList'] as $v){
            isset($v['ticket_id']) && $v['ticket_template_id'] = $v['ticket_id'];
            if($v['ticket_template_id']){
                isset($v['date']) && $v['use_day'] = $v['date'];
                isset($v['num']) && $v['nums'] = $v['num'];
                isset($v['name']) && $v['owner_name'] = $v['name'];
                isset($v['phone']) && $v['owner_mobile'] = $v['phone'];
                isset($v['card']) && $v['owner_card'] = $v['card'];
                $v['price_type'] = isset($v['price_type'])?$v['price_type']:0;
                ($v['use_day'] && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$v['use_day'])) &&  $v['use_day'] = date("Y-m-d",$v['use_day']);

                (!$v['use_day'] || !preg_match("/^\d{4}-\d{2}-\d{2}$/",$v['use_day'])) && Lang_Msg::error('ERROR_USEDAY_1'); //游玩日期不能为空，且格式为xxxx-xx-xx
                !$v['nums'] && Lang_Msg::error('ERROR_TK_NUMS_1');
                !$v['owner_name'] && Lang_Msg::error('ERROR_OWNER_1');
                !$v['owner_mobile'] && Lang_Msg::error('ERROR_OWNER_2');
                //!$v['owner_card'] && Lang_Msg::error('ERROR_OWNER_3');

                $order = array(
                    'id'=> Util_Common::uniqid(1), //参数1，订单
                    'distributor_id'=> $params['distributor_id'],
                    'distributor_name'=> '',
                    'supplier_name'=> '',
                    'use_day'=> $v['use_day'],
                    'nums'=> $v['nums'],
                    'owner_name'=> $v['owner_name'],
                    'owner_mobile'=> $v['owner_mobile'],
                    'owner_card'=> isset($v['owner_card'])?$v['owner_card']:'',
                    'remark'=> isset($v['remark'])?$v['remark']:'',
                    'status'=> 'unpaid',
                    'ota_account'=> !empty($v['ota_account']) ? $v['ota_account'] : 0,
                    'ota_name'=> !empty($v['ota_name']) ? $v['ota_name'] : '',
                    'created_by'=> $params['user_id'],
                    'created_at'=> $nowtime,
                    'updated_at'=> $nowtime,
                    'user_id' => $params['user_id'],
                    'user_account' => $params['user_account'],
                    'user_name' => $params['user_name'],
                );

                //获取票种含价格库存判断
                $ticketTemplateInfos[$order['id']] = TicketTemplateModel::model()->getInfo($v['ticket_template_id'],$v['price_type'],$params['distributor_id'],$v['use_day'],$v['nums']);
                !$ticketTemplateInfos[$order['id']] && Lang_Msg::error('ERROR_TKT_4',array('ticket_name'=>empty($v['ticket_name'])?'票ID:'.$v['ticket_template_id']:$v['ticket_name']));
                isset($ticketTemplateInfos[$order['id']]['code']) && $ticketTemplateInfos[$order['id']]['code']=='fail' && Lang_Msg::error($ticketTemplateInfos[$order['id']]['message']);

                $order['type'] = $ticketTemplateInfos[$order['id']]['type'];
                $order['kind'] = $ticketTemplateInfos[$order['id']]['is_union']==0?1:2; //种类:1单票2联票3套票
                $order['supplier_id'] = $ticketTemplateInfos[$order['id']]['organization_id'];
                $order['landscape_ids'] = $ticketTemplateInfos[$order['id']]['scenic_id'];
                $order['amount'] = $v['nums'] * $ticketTemplateInfos[$order['id']]['price']; //订单结算金额
                $order['ota_type'] = $ticketTemplateInfos[$order['id']][ 'ota_type' ];

                $ticketTemplateInfos[$order['id']]['order_id'] = $order['id'];

                array_push($orgIds,$order['supplier_id'],$order['distributor_id']);

                $orders[$order['id']] = $order;
                if(!$fields) {
                    $fields = array_keys($order);
                }
                //根据phone, card 添加到redis
                OrderModel::model()->setPhoneCardMap( $order['owner_mobile'], $order['owner_card'] ,$order[ 'id' ]);
            }
        }
        if($orgIds){
            $orgIds = array_unique($orgIds);
            $orgs = OrganizationModel::model()->getList($orgIds);
            foreach($orders as $order_id=>$order){
                $orders[$order_id]['supplier_name'] =  empty($orgs['data'][$order['supplier_id']])?' ':$orgs['data'][$order['supplier_id']]['name'];
                $orders[$order_id]['distributor_name'] = empty($orgs['data'][$order['distributor_id']])?' ':$orgs['data'][$order['distributor_id']]['name'];
            }
        }

        !$orders && Lang_Msg::error('ERROR_TKT_1');
        $ordersAdd = array_values($orders);
        array_unshift($ordersAdd,$fields);

        $r = $this->add($ordersAdd);
        if($r){
            $r = OrderItemModel::model()->addBatch($orders,$ticketTemplateInfos);
            if($r){
                $r = TicketModel::model()->addBatch($orders,$ticketTemplateInfos);
                if($r){
                    return $orders;
                }
            }
        }
        return false;
    }


    //添加一条记录
    public function setPhoneCardMap( $phone, $card, $order_id )
    {	
        $now = time();
        if($phone){
            $cacheKey = $this->phonePreCacheKey.$phone;
            $this->redis->push('hset', array($cacheKey , $order_id,  $order_id));
        }
        if($card){
            $cacheKey = $this->cardPreCacheKey.$card;
            $this->redis->push('hset', array($cacheKey , $order_id,  $order_id));
            $cacheKey = $this->cardPreCacheKey.substr($card, -6);
            $this->redis->push('hset', array($cacheKey , $order_id,  $order_id));
        }
    }
    

	//删除一个ORDER_IDA
    public  function delPhoneCardMap( $phone, $card, $order_id )
    {
        if($phone){
            $cacheKey = $this->phonePreCacheKey.$phone;
            $this->redis->push('hdel', array($cacheKey , $order_id));
        }
        if($card){
            $cacheKey = $this->cardPreCacheKey.$card;
            $this->redis->push('hdel', array($cacheKey , $order_id));
            $cacheKey = $this->cardPreCacheKey.substr($card, -6);
            $this->redis->push('hdel', array($cacheKey , $order_id));
        }
    }

    public function getOrderByPhone($phone) {
        $cacheKey = $this->phonePreCacheKey.$phone;
        return $this->redis->hgetall($cacheKey);
    }

    public function getOrderByCard($card) {
        $cacheKey = $this->cardPreCacheKey.$card;
        return $this->redis->hgetall($cacheKey);
    }

    private function groupById($ids) {
        $tmp = array();
        foreach($ids as $id) {
            $key = Util_Common::uniqid2date($id);
            $tmp[$key][] = $id;
        }
        return $tmp;
    }

    public function checkUnuseNum($order_item, $poi_id=0, $ticket_id=0) {
        $now = strtotime(date('Y-m-d'));
        $week = date( 'w' );
        // 有效期
        if ($order_item['date_available']) {
            list($st, $et)  = explode(',', $order_item['date_available']);
            if ($now < $st || $now > $et) return 0;
        }
        // 游玩时间
        $st = strtotime($order_item['use_day']);
        $et = strtotime('+'.$order_item['valid'].' day', $st);
        if ($now < $st || $now > $et) return 0;
        // 星期
        if (strpos($order_item['week_time'], $week) === false) return 0;
        // 剩余次数
        if ($poi_id>0) {
            $left_nums = TicketModel::model()->getUnusedNum($order_item['order_id'], $poi_id, $ticket_id);
        } else {
            $left_nums = $order_item['nums'] - $order_item['used_nums'] - $order_item['refunding_nums'] - $order_item['refunded_nums'];
        }
        return $left_nums;
    }


	public function getOrderList( $ids ,$poiId=0, $landscape_id = 0,$supplier_id=0)
    {
        $return = array();
    	$tmp = array();
    	if (!is_array($ids)) $ids = explode(',', $ids);
        $ids = $this->groupById($ids);
        foreach($ids as $key=>$items) {
            // 付款检测
            $where = array('id|in'=>$items, 'status|in'=>array('paid','finish','billed'));
            $supplier_id && $where['supplier_id'] = $supplier_id;
            if ($landscape_id > 0) $where['find_in_set|exp'] = '('.$landscape_id.',landscape_ids)';
            $orders = $this->setTable(reset($items))->search($where);
            if (!$orders) continue;
            $paidIds = array_keys($orders);
            $list = OrderItemModel::model()->setTable(reset($paidIds))->search(array('order_id|in'=>$paidIds));
            foreach($list as $item) {
                if(!$poiId && $landscape_id>0) {
                    $poiList = PoiModel::model()->getPoiList($landscape_id);
                    $poiIds = array_keys($poiList);
                    if (!$poiIds) continue;
                    $nums = array();
                    foreach($poiIds as $value) {
                        $nums[] = $this->checkUnuseNum($item, $value);
                    }
                    $num = max($nums);
                }else {
                    $num = $this->checkUnuseNum($item, $poiId);
                }
                if ($num<=0) continue;
                $order = $orders[$item['order_id']];
                $tmp = array();
                $tmp['order_id'] = $item['order_id'];
                $tmp['nums'] = intval($num);
                $tmp['name'] = $item['name'];
                $tmp['owner_name'] = $order['owner_name'];
                $tmp['owner_mobile'] = $order['owner_mobile'];
                $tmp['owner_card'] = $order['owner_card'];
                $tmp['ticket_template_id'] = $item['ticket_template_id'];
                $tmp['distributor_id'] = $item['distributor_id'];
                $tmp['supplier_id'] = $item['supplier_id'];
                $tmp['refunding_nums'] = $item['refunding_nums'];
                $tmp['refunded_nums'] = $item['refunded_nums'];
                $tmp['used_nums'] = $item['used_nums'];
                $tmp['price'] = $item['price'];
                $return[$item['order_id']] = $tmp;
            }
        }
        return $return;
    }

    public static function gencodeByAsync($params) {
        self::model()->gencode($params);
    }

    public function gencode($params) {
        extract($params);
        try{
            //下单
            $orderParams = array(
                'order_id'=>$order_id,
                'ticket_template_id'=>$ticket_template_id,
                'price_type'=>$price_type,
                'distributor_id'=>$distributor_id,
                'use_day'=>$use_day,
                'nums'=>$nums,
                'owner_name'=>$user_name,
                'owner_mobile'=>$user_mobile,
                'owner_card'=>$user_card,
                'remark'=>'OTA客户：'.$userinfo['name'].'[ID:'.$userinfo['id'].']',
                'user_id'=>1,
                'user_name'=>'system',
            );
            // print_r($orderParams);exit();
            TicketTemplateModel::model()->setExpireTime(86400);
            OrganizationModel::model()->setExpireTime(86400);
            $this->begin();
            $orderInfo = $this->addOrder($orderParams);
            if(!$orderInfo) {
                $this->rollback();
                throw new Exception("ERROR_OPERATE_1");
            }
            //支付（信用）
            $paymentParams = array(
                'distributor_id'=> $distributor_id,
                'order_ids'=> $orderInfo['id'],
                'status'=> 'succ',
                'payment'=> $payment,
                'remark'=>'OTA客户：'.$userinfo['name'].'[ID:'.$userinfo['id'].']',
                'user_id'=>1,
                'user_name'=>'system',
            );
            $paymentInfo = PaymentModel::model()->addPayment($paymentParams, 0);
            if(!$paymentInfo){
                $this->rollback();
                throw new Exception("ERROR_OPERATE_1");
            }

            // if(empty($paymentInfo['has_paid']) && in_array($payment,array('credit','advance','union'))) {   //扣款
            //     $res = OrganizationModel::model()->creditPay(array(
            //         'distributor_id' => $distributor_id,
            //         'supplier_id' => $orderInfo['supplier_id'],
            //         'money' => $paymentInfo['amount'],
            //         'type' => $payment == 'credit' ? 0 : 1,
            //         'serial_id' => $paymentInfo['id']
            //     ));
            //     if ($res['code'] == 'fail') {
            //         $this->rollback();
            //         throw new Exception($res['message']);
            //     }
            // }

            $this->commit();
            Log_Base::save('np','finish:'.var_export($params, true));
        } catch(Exception $e){
            $this->rollback();
            Log_Base::save('np_error','params:'.var_export($params, true));
            Log_Base::save('np_error','message:'.$e->getMessage());
        }
    }
}

