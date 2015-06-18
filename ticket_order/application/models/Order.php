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
    protected $productFields = array(
        'name','fat_price','group_price','sale_price','listed_price',
        'valid','max_buy','mini_buy','week_time','refund',
        'price','valid_flag','checked_open','message_open'
    );
    public $allStatus = array('unaudited','reject','unpaid','cancel','paid','finish','billed');

    public function getTable() {
        return $this->tblname;
    }

    public function setTable($id = 0) {
        // if (!$id) $this->tblname = $this->basename . date('Ym');
        // else  $this->tblname = $this->basename . Util_Common::uniqid2date($id);
        return $this;
    }

    public function share($ts = 0) {
        // if (!$ts) $ts = time();
        // $this->tblname = $this->basename . date('Ym', $ts);
        return $this;
    }

    //检查游玩时间
    private function checkUseDay($productInfo,$use_day,$price_type=0) {
        if(!empty($productInfo) && !empty($use_day)) {
            $scheduled_time = $price_type==1?$productInfo['group_scheduled_time']:$productInfo['fat_scheduled_time'];
            $days = intval($scheduled_time / 86400); //提前天数
            $preSec = $scheduled_time % 86400; //预定提前时分秒时间戳
            $nowTime = time();
            $nowDay = strtotime(date("Y-m-d")); //当天0点时间戳
            $nowSec = $nowTime-$nowDay; //当天时分秒时间戳
            $useDayTime = strtotime($use_day); //游玩日期0点时间戳
            $preHM = date("H:i",$nowDay+$preSec);
            //echo $scheduled_time." | ".$days." | ".$preSec." | ".$nowTime." | ".$nowDay." | ".$nowSec." | ".$useDayTime." | ".$preHM."\n";

            //预定时间大于游玩时间
            if(strtotime($use_day.' 23:59:59')<$nowTime) {
                Lang_Msg::error('ERROR_USEDAY_3');
            } else if($useDayTime<$nowDay+($days+(($preSec>0 && $nowSec>$preSec)?1:0))*86400 ) {
                Tools::lsJson(false,'该产品['.$productInfo['name'].']需在入园前 '.$days.' 天的 '.$preHM.' 以前购买');
            }
        } else {
            Tools::lsJson(false,'缺少产品ID或游玩日期参数');
        }
    }

    //检查预定张数限制
    private function checkNumLimit($productInfo,$nums,$price_type=0) {
        if(!empty($productInfo)) {
            if (!$nums) {
                Lang_Msg::error('ERROR_TK_NUMS_1');
            } else if ($price_type == 1 && $nums < $productInfo['mini_buy']) {
                Tools::lsJson(false, '团客购买下限不能小于' . $productInfo['mini_buy']);
            } else if ($nums > $productInfo['max_buy']) {
                Tools::lsJson(false, '购买上限不能大于' . $productInfo['max_buy']);
            }
        } else {
            Tools::lsJson(false,'缺少产品ID参数');
        }
    }

    //添加单条订单，参数$returnProdInfo：是否一起返回产品信息
    public function addOrder($params,$returnProdInfo=false){
        if($params['is_sms']==1 && !Validate::isMobilePhone($params['owner_mobile'])) {
            Lang_Msg::error('ERROR_SMSSEND_1');
        }

        //获取票种含价格库存判断
        $params['productInfo'] = TicketTemplateModel::model()->getInfo($params['product_id'],$params['price_type'],$params['distributor_id'],$params['use_day'],$params['nums']);
        !$params['productInfo'] && Lang_Msg::error('ERROR_TKT_2');
        isset($params['productInfo']['code']) && $params['productInfo']['code']=='fail' && Lang_Msg::error($params['productInfo']['message']);

        if(empty($params['productInfo']['payment'])) { //如果未获取到产品支付方式，则重新获取
            $params['productInfo'] = TicketTemplateModel::model()->getInfo($params['product_id'],$params['price_type'],$params['distributor_id'],$params['use_day'],$params['nums']);
            !$params['productInfo'] && Lang_Msg::error('ERROR_TKT_2');
            isset($params['productInfo']['code']) && $params['productInfo']['code']=='fail' && Lang_Msg::error($params['productInfo']['message']);

            if(empty($params['productInfo']['payment'])) { //重现获取产品信息仍人没有支付方式的话则用默认值
                $params['productInfo']['payment']="1,2,3,4";
                Log_Base::save('OrderPayment', 'Get payment unsuccessfully when adding order [product_id:'.$params['product_id'].']: '.var_export($this->body,true));
            }
        }

        $nowtime = time();

        $this->checkUseDay($params['productInfo'],$params['use_day'],$params['price_type']); //检查游玩时间
        $this->checkNumLimit($params['productInfo'],$params['nums'],$params['price_type']); //检查预定张数限制

        $custome = true;
        if (!array_key_exists('is_checked', $params) || !$params['is_checked'])
        {
            if(isset($params['price']) && $params['price']!= $params['productInfo']['price']) {
                Lang_Msg::error('ERROR_ORDER_15');
            }
            $custome = false;
        }

        $visitors = array();
        if($params['visitors']){
            $params['visitors'] = json_decode($params['visitors'],true);
            OrderItemModel::model()->checkVisitors($params['visitors'],$params['productInfo']['name'],$params['productInfo']['need_idcard'],$params['nums']);
            $visitors = array_values($params['visitors']);
            $count = count($visitors);
            if($params['nums'] > count($visitors)) {
                for($i = 0; $i < $params['nums']; $i++) {
                    if(array_key_exists($i, $visitors)) {
                        continue;
                    }
                    $index = $i % $count;
                    $visitors[] = $visitors[$index];
                }
            } elseif($params['nums'] < count($visitors)) {
                $visitors = array_slice($visitors, 0, $params['nums']);
            }
        } else if(!empty($params['productInfo']['need_idcard'])) {
            Tools::lsJson(false,'该产品［'.$params['productInfo']['name'].'］旅客姓名和身份证必填');
        }


        if (!$params['order_id']) $params['order_id'] = Util_Common::uniqid(1);
        $order = array(
            'id'=>$params['order_id'], //参数1，订单
            'type'=>$params['productInfo']['type'],
            'kind'=>1, //种类:1单票2联票3套票
            'status'=>'unpaid',
            'created_by'=>$params['user_id'],
            'created_at'=>$nowtime,
            'updated_at'=>$nowtime
        );
        if(count($params['productInfo']['items'])>1) {
            $order['kind'] = 3;
        }

        // 一次验票一次取票
        if($params['price_type']==1){
            // 团客
            $order['is_once_verificate'] = $params['productInfo']['is_group_once_verificate'];
            $order['is_once_taken'] = $params['productInfo']['is_group_once_taken'];
            $order['ticket_template_remark'] = $params['productInfo']['group_description'];
        }elseif($params['price_type']==0){
            // 散客
            $order['is_once_verificate'] = $params['productInfo']['is_fat_once_verificate'];
            $order['is_once_taken'] = $params['productInfo']['is_fat_once_taken'];
            $order['ticket_template_remark'] = $params['productInfo']['fat_description'];
        }

        $order['code'] = $params['order_id'];
        $order['distributor_id'] = $params['distributor_id']; //分销商ID
        $order['supplier_id'] = $params['productInfo']['organization_id']; //供应商ID
        $order['landscape_ids'] = $params['productInfo']['scenic_id']; //景区id
        $order['checked_open'] = $params['productInfo']['checked_open'];
        $order['message_open'] = $params['productInfo']['message_open'];

        $orgs = OrganizationModel::model()->getList(array($order['supplier_id'],$order['distributor_id']),'name,supply_type');
        $order['supplier_name'] = empty($orgs['data'][$order['supplier_id']])?' ':$orgs['data'][$order['supplier_id']]['name'];
        $order['distributor_name'] = empty($orgs['data'][$order['distributor_id']])?' ':$orgs['data'][$order['distributor_id']]['name'];

        $order['price_type'] = $params['price_type']?$params['price_type']:0;
        $order['use_day'] = $params['use_day']?$params['use_day']:date('Y-m-d'); //游玩日期
        $order['nums'] = $params['nums']; //订购票数
        $order['amount'] = $custome ? $params['nums'] * $params['price'] : $params['nums'] * $params['productInfo']['price']; //订单结算金额
        $order['owner_name'] = $params['owner_name']; //取票人
        $order['owner_mobile'] = $params['owner_mobile']; //取票人手机
        $order['owner_card'] = $params['owner_card']; //取票人身份证
        $order['remark'] = $params['remark']?$params['remark']:'';
        $order['status'] = !empty($orgs['data'][$order['supplier_id']]['supply_type'])?($params['status']=='unaudited' ? 'unaudited':'unpaid'):'unpaid'; //如有备注，则订单状态为未审核
        $order['audit_status'] = !empty($orgs['data'][$order['supplier_id']]['supply_type'])?($params['status']=='unaudited' ? 0:1):1; //如有备注，则审核状态为未审核
        $order['ota_type'] = $params['ota_type']?$params['ota_type']:($params['productInfo']['ota_type']?$params['productInfo']['ota_type']:'system');
        $order['ota_account'] = !empty($params['ota_account'])?$params['ota_account']:0;
        $order['ota_name'] = !empty($params['ota_name'])?$params['ota_name']:'';
        isset($params['pay_type']) && $params['pay_type'] && $order['pay_type'] = $params['pay_type'];
        isset($params['payment']) && $params['payment'] && $order['payment'] = $params['payment'];

        $order['user_id'] = $params['user_id'];
        $order['user_account'] = $params['user_account'];
        $order['user_name'] = $params['user_name'];
        $order['product_id'] = $params['product_id'];
        $order['expire_start'] = strtotime($order['use_day']);
        $order['product_payment'] = $params['productInfo']['payment']; //支付方式：1在线支付，2信用支付，3储值支付，4平台储值支付
        $order['ticket_infos']= json_encode($params['productInfo']['items'],JSON_UNESCAPED_UNICODE); //产品门票信息

        $order['source'] = $params['source'];
        $order['local_source'] = $params['local_source'];
        $order['source_id'] = $params['source_id'];
        $order['source_token'] = $params['source_token'];
        $order['partner_type'] = $params['productInfo']['partner_type']; //合作景区类型,0景旅通,1大漠,等
        $order['partner_product_code'] = $params['productInfo']['partner_product_code']; //合作伙伴（如大漠）提供的产品号，多个逗号隔开

        foreach ($params['productInfo'] as $key=>$v) {
            in_array($key,$this->productFields) && $order[$key] = $v;
        }

        $order['product_name'] = !empty($params['product_name']) ? $params['product_name']:$order['name']; //自定义产品名称

        if($params['productInfo']['valid_flag']){ //不限制游玩时间则用产品有效期
            $order['expire_start'] = $params['productInfo']['expire_start'];
            $order['expire_end'] = $params['productInfo']['expire_end'];
        } else {
            $validTime = strtotime($order['use_day']." 23:59:59") + intval($params['productInfo']['valid'])*86400;
            $order['expire_end'] = $validTime<$params['productInfo']['expire_end']?$validTime:$params['productInfo']['expire_end'];
        }

        $params['productInfo']['order_id'] = $order['id'];

        //把产品的短信模版解析后保存redis
        SmsModel::model()->setOrderSmsTemplateMap($params['productInfo'],$order);
        //根据phone, card 添加到redis
        $this->setPhoneCardMap( $order['owner_mobile'], $order['owner_card'] ,$order[ 'id' ]);
        if(!empty($visitors)){
            foreach($visitors as $visitor){
                $this->setPhoneCardMap( $visitor['visitor_mobile'], $visitor['visitor_card'] ,$order[ 'id' ]);
            }
        }
        $r = $this->add($order);
        if($r){
            //添加订单明细
            $r = OrderItemModel::model()->addNew($order,$params['productInfo'],$visitors);
            if($returnProdInfo)
                $order['productInfo'] = $params['productInfo'];
            if($r) return $order;
        }
        return false;
    }

    /** （废弃 zqf 2016-06-15）
     * 订单，订单明细，支付单，支付单明细，订单的票跟子景点关联，生成票号，生成流水
     * 新建拉手订单并完成支付
     * author : yinjian
     */
    public function addLashouOrder($buy_info,$productInfo)
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
                $info['supplier_id'] = $productInfo['organization_id'];
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
                'type' => $productInfo['type'],
                'kind' => $productInfo['is_union']==0?1:2,
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
                'supplier_id' => $productInfo['organization_id'],
                'supplier_name' => $buy_info['supplier_name'],
                'landscape_ids' => $productInfo['scenic_id'],
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'ota_name' => $buy_info['owner_name'],
            );
            $orderInfo['code'] = $orderInfo['id'];
            $this->add($orderInfo);
            // 调整原有门票有效期
            $d1 = strtotime($buy_info['use_day'].' 00:00:00');
            $productInfo['valid'] = round(($productInfo['expire_end']-$d1)/3600/24);
            // order_items
            OrderItemModel::model()->addNew($orderInfo,$productInfo);
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
            $ticket_ids =TicketModel::model()->addNew($orderInfo,$productInfo);
            // ticket_relations
            TicketRelationModel::model()->addNew($orderInfo,$productInfo,$ticket_ids);
            // transaction_flow
            $transflowParam = array(
                'id' => $transaction_flow_id,
                'mode' => $buy_info['payment'],
                'type' => 1,
                'amount' => $buy_info['amount'],
                'supplier_id' => $productInfo['organization_id'],
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
    public function addBatchOrder($params) {
        $orders = $productInfos= $orgIds = $visitorsArr = array();
        $nowtime = time();
        foreach($params['cartTicketList'] as $v) {
            isset($v['ticket_template_id']) && $v['product_id'] = $v['ticket_template_id'];
            isset($v['ticket_id']) && $v['product_id'] = $v['ticket_id'];
            if($v['product_id']){
                isset($v['date']) && $v['use_day'] = $v['date'];
                isset($v['num']) && $v['nums'] = $v['num'];
                isset($v['name']) && $v['owner_name'] = $v['name'];
                isset($v['phone']) && $v['owner_mobile'] = $v['phone'];
                isset($v['card']) && $v['owner_card'] = $v['card'];
                $v['price_type'] = isset($v['price_type'])?$v['price_type']:0;
                ($v['use_day'] && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$v['use_day'])) &&  $v['use_day'] = date("Y-m-d",$v['use_day']);

                (!$v['use_day'] || !preg_match("/^\d{4}-\d{2}-\d{2}$/",$v['use_day'])) && Lang_Msg::error('ERROR_USEDAY_1'); //游玩日期不能为空，且格式为xxxx-xx-xx

                !$v['owner_name'] && Lang_Msg::error('ERROR_OWNER_1');
                !$v['owner_mobile'] && Lang_Msg::error('ERROR_OWNER_2');
                !Validate::isMobilePhone($v['owner_mobile']) && Lang_Msg::error('ERROR_SMSSEND_1');
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
                    'remark'=> isset($v['remark'])?$v['remark']:($params['remark']?$params['remark']:''),
                    'status'=> 'unpaid',
                    'ota_type'=> !empty($v['ota_type'])?$v['ota_type']:'system',
                    'ota_account'=> !empty($v['ota_account'])?$v['ota_account']:0,
                    'ota_name'=> !empty($v['ota_name'])?$v['ota_name']:'',
                    'created_by'=> $params['user_id'],
                    'created_at'=> $nowtime,
                    'updated_at'=> $nowtime,
                    'user_id' => $params['user_id'],
                    'user_account' => $params['user_account'],
                    'user_name' => $params['user_name'],
                    'product_id'=> $v['product_id'],
                    'expire_start'=>strtotime($v['use_day']),
                    'price_type'=>$v['price_type'],
                );
                $order['code'] = $order['id'];
                $order['status'] = $v['remark'] ? 'unaudited':'unpaid'; //如有备注，则订单状态为待确认
                $order['audit_status'] = $v['remark'] ? 0:1;

                //获取票种含价格库存判断
                $productInfos[$order['id']] = TicketTemplateModel::model()->getInfo($v['product_id'],$v['price_type'],$params['distributor_id'],$v['use_day'],$v['nums']);
                !$productInfos[$order['id']] && Lang_Msg::error('ERROR_TKT_4',array('ticket_name'=>empty($v['ticket_name'])?'票ID:'.$v['product_id']:$v['ticket_name']));
                isset($productInfos[$order['id']]['code']) && $productInfos[$order['id']]['code']=='fail' && Lang_Msg::error($productInfos[$order['id']]['message']);

                $this->checkUseDay($productInfos[$order['id']],$v['use_day'],$v['price_type']); //检查游玩时间
                $this->checkNumLimit($productInfos[$order['id']],$v['nums'],$v['price_type']); //检查预定张数限制

                if(empty($productInfos[$order['id']]['payment'])) { //如果未获取到产品支付方式，则重新获取
                    $productInfos[$order['id']] = TicketTemplateModel::model()->getInfo($v['product_id'],$v['price_type'],$params['distributor_id'],$v['use_day'],$v['nums']);
                    !$productInfos[$order['id']] && Lang_Msg::error('ERROR_TKT_4',array('ticket_name'=>empty($v['ticket_name'])?'票ID:'.$v['product_id']:$v['ticket_name']));
                    isset($productInfos[$order['id']]['code']) && $productInfos[$order['id']]['code']=='fail' && Lang_Msg::error($productInfos[$order['id']]['message']);

                    if(empty($productInfos[$order['id']]['payment'])) { //重现获取产品信息仍人没有支付方式的话则用默认值
                        $productInfos[$order['id']]['payment']="1,2,3,4";
                        Log_Base::save('OrderPayment', 'Get payment unsuccessfully when adding order [product_id:'.$v['product_id'].']: '.var_export($this->body,true));
                    }
                }

                //旅客信息
                $visitors = array();
                if($v['visitors']){
                    $v['visitors'] = json_decode($v['visitors'],true);
                    OrderItemModel::model()->checkVisitors($v['visitors'],$productInfos[$order['id']]['name'],$productInfos[$order['id']]['need_idcard'],$v['nums']);
                    $visitors = array_values($v['visitors']);
                    $count = count($visitors);
                    if($v['nums'] > count($visitors)) {
                        for($i = 0; $i < $v['nums']; $i++) {
                            if(array_key_exists($i, $visitors)) {
                                continue;
                            }
                            $index = $i % $count;
                            $visitors[] = $visitors[$index];
                        }
                    } elseif($v['nums'] < count($visitors)) {
                        $visitors = array_slice($visitors, 0, $v['nums']);
                    }
                } else if(!empty($productInfos[$order['id']]['need_idcard'])) {
                    Tools::lsJson(false,'该产品［'.$productInfos[$order['id']]['name'].'］旅客姓名和身份证必填');
                }
                if(!empty($visitors)) {
                    $visitorsArr[$order['id']] = $visitors;
                }

                // @TODO 优化建议：可将订单属性单独出一个函数（方便单独下单和批量下单调用）
                // 团散客一次验票一次取票
                if($v['price_type']==1){
                    // 团客
                    $order['is_once_verificate'] = $productInfos[$order['id']]['is_group_once_verificate'];
                    $order['is_once_taken'] = $productInfos[$order['id']]['is_group_once_taken'];
                    $order['ticket_template_remark'] = $productInfos[$order['id']]['group_description'];
                }elseif($v['price_type'] ==0){
                    // 散客
                    $order['is_once_verificate'] = $productInfos[$order['id']]['is_fat_once_verificate'];
                    $order['is_once_taken'] = $productInfos[$order['id']]['is_fat_once_taken'];
                    $order['ticket_template_remark'] = $productInfos[$order['id']]['fat_description'];
                }
                $order['type'] = $productInfos[$order['id']]['type'];
                $order['kind'] = 1; //种类:1单票2联票3套票
                $order['supplier_id'] = $productInfos[$order['id']]['organization_id'];
                $order['landscape_ids'] = $productInfos[$order['id']]['scenic_id'];
                $order['amount'] = $v['nums'] * $productInfos[$order['id']]['price']; //订单结算金额
                $order['ota_type'] = $productInfos[$order['id']][ 'ota_type' ];

                $order['ota_type']=$params['ota_type']?$params['ota_type']:($order['ota_type']?$order['ota_type']:'system');
                $order['ota_account']= $params['ota_account']?$params['ota_account']:($order['ota_account']?$order['ota_account']:0);
                $order['ota_name']= $params['ota_name']?$params['ota_name']:($order['ota_name']?$order['ota_name']:'');
                $order['product_payment'] = $productInfos[$order['id']]['payment']; //支付方式：1在线支付，2信用支付，3储值支付，4平台储值支付
                $order['ticket_infos']= json_encode($productInfos[$order['id']]['items'],JSON_UNESCAPED_UNICODE); //产品门票信息

                if(count($productInfos[$order['id']]['items'])>1) {
                    $order['kind'] = 3;
                }

                foreach ($productInfos[$order['id']] as $key=>$vv) {
                    in_array($key,$this->productFields) && $order[$key] = $vv;
                }

                $order['product_name'] = !empty($v['product_name']) ? $v['product_name']:$order['name']; //自定义产品名称

                if($productInfos[$order['id']]['valid_flag']){ //不限制游玩时间则用产品有效期
                    $order['expire_start'] = $productInfos[$order['id']]['expire_start'];
                    $order['expire_end'] = $productInfos[$order['id']]['expire_end'];
                } else {
                    $validTime = strtotime($order['use_day']." 23:59:59") + intval($productInfos[$order['id']]['valid'])*86400;
                    $order['expire_end'] = $validTime<$productInfos[$order['id']]['expire_end']?$validTime:$productInfos[$order['id']]['expire_end'];
                }

                $order['partner_type'] = $productInfos[$order['id']]['partner_type']; //合作景区类型,0景旅通,1大漠,等
                $order['partner_product_code'] = $productInfos[$order['id']]['partner_product_code']; //合作伙伴（如大漠）提供的产品号，多个逗号隔开

                $productInfos[$order['id']]['order_id'] = $order['id'];

                array_push($orgIds,$order['supplier_id'],$order['distributor_id']);

                $orders[$order['id']] = $order;

                //把产品的短信模版解析后保存redis
                SmsModel::model()->setOrderSmsTemplateMap($productInfos[$order['id']],$order);
                //根据phone, card 添加到redis
                OrderModel::model()->setPhoneCardMap( $order['owner_mobile'], $order['owner_card'] ,$order[ 'id' ]);
                if(!empty($visitors)) {
                    foreach($visitors as $visitor){
                        $this->setPhoneCardMap( $visitor['visitor_mobile'], $visitor['visitor_card'] ,$order[ 'id' ]);
                    }
                }
            }
        }
        if($orgIds){
            $orgIds = array_unique($orgIds);
            $orgs = OrganizationModel::model()->getList($orgIds,'name,supply_type');
            foreach($orders as $order_id=>$order){
                $orders[$order_id]['supplier_name'] =  empty($orgs['data'][$order['supplier_id']])?' ':$orgs['data'][$order['supplier_id']]['name'];
                $orders[$order_id]['distributor_name'] = empty($orgs['data'][$order['distributor_id']])?' ':$orgs['data'][$order['distributor_id']]['name'];
                $orders[$order_id]['status']=$orgs['data'][$order['supplier_id']]['supply_type']?($orders[$order_id]['status']=='unaudited'?'unaudited':'unpaid'):'unpaid';
                $orders[$order_id]['audit_status']=$orgs['data'][$order['supplier_id']]['supply_type']?($orders[$order_id]['status']=='unaudited'?0:1):1;
            }
        }

        !$orders && Lang_Msg::error('ERROR_TKT_1');
        $orderArr = $orders;
        array_unshift($orders,array_keys(reset($orders)));

        $r = $this->add($orders);
        if($r) {
            $r = OrderItemModel::model()->addBatch($orderArr,$productInfos,$visitorsArr);
            if($r) return $orderArr;
        }
        return false;
    }


    //添加一条记录
    public function setPhoneCardMap( $phone, $card, $order_id ,$code='')
    {
        !$code && $code = $order_id;
        if($phone){
            $cacheKey = $this->phonePreCacheKey.$phone;
            $this->redis->push('hset', array($cacheKey , $code,  $order_id));
        }
        if($card){
            $cacheKey = $this->cardPreCacheKey.$card;
            $this->redis->push('hset', array($cacheKey , $code,  $order_id));
            $cacheKey = $this->cardPreCacheKey.substr($card, -6);
            $this->redis->push('hset', array($cacheKey , $code,  $order_id));
        }
    }


    //删除一个ORDER_IDA
    public  function delPhoneCardMap( $phone, $card, $code )
    {
        if($phone){
            $cacheKey = $this->phonePreCacheKey.$phone;
            $this->redis->push('hdel', array($cacheKey , $code));
        }
        if($card){
            $cacheKey = $this->cardPreCacheKey.$card;
            $this->redis->push('hdel', array($cacheKey , $code));
            $cacheKey = $this->cardPreCacheKey.substr($card, -6);
            $this->redis->push('hdel', array($cacheKey , $code));
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

    /**
     * @param $landscape_id
     * @param $poi_id
     * @param $ids
     * @param $order_id
     * @param $order
     */
    public function async($landscape_id, $poi_id, $ids, $order_id, $order)
    {
// STEP 1 更新验票点
        $this->updateTicketItem($ids, $landscape_id, $poi_id);
        $tickets = array();
        $products = array();
        $ticket_items = TicketItemsModel::model()->search(array('ticket_id|in' => $ids));
        foreach ($ticket_items as $value) {
            $tickets[$value['status']][$value['ticket_id']] = $value['ticket_id'];
            $products[$value['status']][$value['order_item_id']] = $value['order_item_id'];
        }
        // STEP 2 更新票
        $this->updateTicket($tickets);
        // STEP 3 更新ORDERITEM
        $this->updateOrderItem($products);
        // STEP 4 更新ORDER
        $left_nums = $this->updateOrder($order_id, $order);
        // 团客票多余得票申请退票(需产品是可退的)
        if ($left_nums > 0 && $order['source'] == 0 && $order['refund'] == 1) {
            $order_items = OrderItemModel::model()->search(array('order_id' => $order_id, 'status' => 1));
            if (!empty($order_items)) {
                $order_item0 = reset($order_items);
                if ($order_item0['price_type'] == 1) { //团客票多余票申请退票
                    RefundApplyModel::model()->refundOrder($order, $order_items, array(), array(
                        'remark' => '核销后自动退票',
                        'nums' => $left_nums,
                        'u_id' => $this->body['user_id'] ? $this->body['user_id'] : $order['user_id'],
                        'user_id' => $this->body['user_id'] ? $this->body['user_id'] : $order['user_id'],
                        'user_account' => $this->body['user_account'] ? $this->body['user_account'] : $order['user_account'],
                        'user_name' => $this->body['user_name'] ? $this->body['user_name'] : $order['user_name'],
                    ));
                }
            }
        }
    }

    private function groupById($ids) {
        $tmp = array();
        foreach($ids as $id) {
            $key = Util_Common::uniqid2date($id);
            $tmp[$key][] = $id;
        }
        return $tmp;
    }

    /**
     * 获取可核销的订单列表
     * @param  [type]  $ids          订单号
     * @param  integer $poiId        景点ID
     * @param  integer $landscape_id 景区ID
     * @param  integer $supplier_id  供应商ID
     * @return [type]                [description]
     */
    public function getOrderList($codes ,$poi_id=0, $landscape_id = 0,$supplier_id=0)
    {
        $return = array();
        $tmp = array();
        if (!is_array($codes)) $codes = explode(',', $codes);
        //获取产品订单
        $orders = $this->setTable(reset($codes))->search(array('code|in'=>$codes, 'status|in'=>array('cancel')));
        if(is_array($orders) && count($orders)==1){
            foreach($orders as $order) {
                $this->checkEnable($order,1);
            }
        }
        $where = array('code|in'=>$codes, 'status|in'=>array('paid','finish','billed'),'checked_open'=>1);
        // $supplier_id && $where['supplier_id'] = $supplier_id;
        $orders = $this->setTable(reset($codes))->search($where);
        if (!$orders) return $return;
        if(is_array($orders) && count($orders)==1){
            foreach($orders as $order) {
                $this->checkEnable($order,1);
            }
        }
        $paidIds = array_keys($orders);
        //获取可验票的订单列表
        $where = array();
        $where['order_id'] = $paidIds;
        if($landscape_id) $where['landscape_id'] = $landscape_id;
        if($poi_id) $where['poi_id'] = $poi_id;
        list($ticket_codes, $order_codes) = TicketItemsModel::model()->getTicketList($where);
        if (!$ticket_codes[1]) return $return;
        foreach($order_codes as $order_id => $items) {
            $order = $orders[$order_id];
            if(!$this->checkEnable($order)) continue;
            $num = $items[1] ? count($items[1]) : 0;
            if($num==0) continue;
            $used_nums = $items[2] ? count($items[2]) : 0;
            $refunded_nums = $items[0] ? count($items[0]) : 0;
            $tmp = array();
            $tmp['order_id'] = $order_id."";
            $tmp['nums'] = $num;
            $tmp['name'] = $order['name'];
            $tmp['owner_name'] = $order['owner_name'];
            $tmp['owner_mobile'] = $order['owner_mobile'];
            $tmp['owner_card'] = $order['owner_card'];
            $tmp['ticket_template_id'] = $order['product_id'];
            $tmp['distributor_id'] = $order['distributor_id'];
            $tmp['supplier_id'] = $order['supplier_id'];
            $tmp['refunding_nums'] = 0;
            $tmp['refunded_nums'] = $refunded_nums;
            $tmp['used_nums'] = $used_nums;
            $tmp['price'] = $order['price'];
            $tmp['price_type'] = $order['price_type'];
            $tmp['ticket_infos']=json_decode($order['ticket_infos'],true);
            $tmp['per_num'] = 0; //每份产品可过人数
            foreach($tmp['ticket_infos'] as $base) {
                if($landscape_id>0 && $base['scenic_id']==$landscape_id) {
                    $tmp['per_num'] += $base['num'];
                }
            }

            $return[$order_id] = $tmp;
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
                'product_id'=>$product_id,
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

    /**
     * 检查产品核销的有效期、使用时间及使用周
     * @param  [type] $info [description]
     * @return [type]       [description]
     */
    public function checkEnable($info, $thow = 0) {
        if (!$info) {
            if ($thow) {
                throw new Lang_Exception("门票不存在");
            }
            return false;
        }
        else if($info['nums'] <= $info['refunding_nums']+$info['refunded_nums'] ) {
            if ($thow) {
                throw new Lang_Exception('该订单已退款');
            }
            return false;
        }
        else if($info['status'] == 'cancel') {
            if ($thow) {
                throw new Lang_Exception('该订单已取消');
            }
            return false;
        }
        $now = strtotime(date('Y-m-d'));
        $week = date( 'w' );
        // 有效期
        if ($now < $info['expire_start']) {
            if ($thow) {
                throw new Lang_Exception("门票还未到游玩日期");
            }
            return false;
        }
        else if ($info['expire_end'] && $now>$info['expire_end']) {
            if ($thow) {
                throw new Lang_Exception("门票已过有效期");
            }
            return false;
        }
        // 游玩时间
        if (!$info['valid_flag']) {
            $st = strtotime($info['use_day']);
            $et = strtotime('+'.$info['valid'].' day', $st);
            if ($now < $st || $now > $et) {
                if ($thow) {
                    throw new Lang_Exception("门票已过可游玩时间");
                }
                return false;
            }
        }
        // 星期
        if (strpos($info['week_time'], $week) === false) {
            if ($thow) {
                throw new Lang_Exception("今天不能使用该门票");
            }
            return false;
        }
        return true;
    }

    /**
     * 核销
     * @param  [type]  $code         支持按订单码、产品码、门票码核销
     * @param  integer $landscape_id 核销的景区ID
     * @param  integer $poi_id       核销的景点ID
     * @param  integer $nums         核销的数量
     * @return [type]                [description]
     */
    public function useTicket(&$order, $code, $landscape_id = 0, $poi_id = 0, $nums = 1, $type=0) {
        $r = Util_Lock::lock($code); //对相同订单核销时加锁
        if(!$r) Lang_Msg::error('操作正在进行中，请稍等再尝试！');
        $where = array();
        $ticket_id = 0;
        //是否有新产品核销
        //$flag = false;
        if ($landscape_id) $where['landscape_id'] = $landscape_id;
        if ($poi_id) $where['poi_id'] = $poi_id;
        switch ($type) {
            case 2:
                //票号
                $ticket_info = TicketModel::model()->getById($code);
                if (!$ticket_info) {
                    throw new Lang_Exception('没有可核销的门票');
                }
                $order_id = $ticket_info['order_id'];
                $where['ticket_id'] = $ticket_id = $code;
                break;
            case 3:
                //产品号
                $item_info = OrderItemModel::model()->getById($code);
                if (!$item_info) {
                    throw new Lang_Exception('没有可核销的门票');
                }
                $order_id = $item_info['order_id'];
                $where['order_item_id'] = $code;
                break;
            default:
                //订单号
                // $order = $this->getById($code);
                // if (!$order) {
                //     throw new Lang_Exception('没有可核销的门票');
                // }
                $order_id = $code;
                break;
        }
        $where['order_id'] = $order_id;
        // !$order && $order = $this->getById($order_id);
        // 订单产品
        $this->checkEnable($order, 1);

        //获取当前景点未使用的票
        // list($ticket_codes,$order_codes) = TicketItemsModel::model()->getTicketList($where);
        // $ticket_codes = $ticket_codes[1];
        // if (!$ticket_codes) {
        //     throw new Lang_Exception('没有可核销的门票');
        // }
        // if (count($ticket_codes)<$nums){
        //     throw new Lang_Exception('门票不足');//票不足
        // }

        // 顺序使用指定数量的票
        // ksort($ticket_codes);
        // $ids = array_slice(array_keys($ticket_codes), 0, $nums);

        //核销REDIS中得TICKET_ITEMS
        $tickets = array();
        $ticket_items = array();
        $order_items = array();
        $id_map = array();
        $rows = TicketItemModel::model()->getCacheByOrderId($order_id);
        foreach($rows as $id=>$tmp) {
            //设置产品核销状态
            if ($tmp['status']==0) {
                $order_items[$tmp['order_item_id']] = $tmp['status'];
            }elseif($tmp['status']==2) {
                if ($order_items[$tmp['order_item_id']]!==0) $order_items[$tmp['order_item_id']] = $tmp['status'];
            }else {
                if ($order_items[$tmp['order_item_id']]!==0 && $order_items[$tmp['order_item_id']]!==2) $order_items[$tmp['order_item_id']] = $tmp['status'];
            }
            $id_map[$tmp['order_item_id']][$tmp['id']] = $tmp['id'];
            if($tmp && $tmp['status'] == 1
                && ($landscape_id<=0 || $landscape_id == $tmp['landscape_id'])
                && ($poi_id<=0 || $poi_id == $tmp['poi_id'])
                && ($ticket_id <= 0 || $ticket_id == $tmp['ticket_id'])) {
                $tickets[$tmp['ticket_id']] = $tmp['ticket_id'];
                $ticket_items[$id] = $tmp;
            }
        }
        if (!$tickets) {
            throw new Lang_Exception('没有可核销的门票');
        }
        if (count($tickets)<$nums){
            throw new Lang_Exception('门票不足');//票不足
        }

        $used_num = count(array_keys($order_items, 2));

        // 顺序使用指定数量的票
        ksort($tickets);
        $ids = array_slice(array_keys($tickets), 0, $nums);
        $update_ticket_items = array();
        foreach ($ticket_items as $id=>$tmp) {
            if( in_array($tmp['ticket_id'], $ids)
                && ($poi_id<=0 || $poi_id == $tmp['poi_id'])
                && ($ticket_id <= 0 || $ticket_id == $tmp['ticket_id'])) {
                $tmp['status'] = 2;
                $update_ticket_items[$id] = json_encode($tmp);
                $order_items[$tmp['order_item_id']] = 2;
            }
        }

        // 团客票多余得票申请退票(需产品是可退的)
        $left_nums = count(array_keys($order_items, 1));
        if($left_nums>0 && $order['source']==0 && $order['refund']==1
            && $order['partner_type']==0 && $order['is_once_verificate']==1) {
            foreach($order_items as $key=>$value) {
                if($value==1) {
                    //退产品
                    $order_items[$key] = 0;
                    //退产品下的票
                    $ids = $id_map[$key];
                    if($ids) {
                        foreach($ids as $id) {
                            $rows[$id]['status'] = 0;
                            $update_ticket_items[$id] = json_encode($rows[$id]);
                        }
                    }
                }
            }
        }

        //更新REDIS中得TICKET_ITEMS & ORDER_ITEMS
        TicketItemModel::model()->setCacheByOrderId($order_id, $update_ticket_items);
        OrderItemModel::model()->setCacheByOrderId($order_id, $order_items);
        //异步执行RDS核销
        Process_Async::presend(array("OrderModel","useTicketAsync"),array($code, $landscape_id, $poi_id, $nums, $type, $this->body));
        // self::useTicketAsync($code, $landscape_id, $poi_id, $nums, $type, $this->body);
        //计算新核销的产品数
        $new_used_num = count(array_keys($order_items, 2));
        $use_num = $new_used_num - $used_num;
        $order['used_num'] = $new_used_num;
        $order['refunding_nums'] = count(array_keys($order_items, 0));

        return $use_num;

    }

    public static function useTicketAsync($code, $landscape_id = 0, $poi_id = 0, $nums = 1, $type=0, $body = array()) {
        $OrderModel = OrderModel::model();
        $OrderModel->begin();
        try{

            // $r = Util_Lock::lock($code); //对相同订单核销时加锁
            // if(!$r) Lang_Msg::error('操作正在进行中，请稍等再尝试！');
            $where = array();
            //是否有新产品核销
            //$flag = false;
            if ($landscape_id) $where['landscape_id'] = $landscape_id;
            if ($poi_id) $where['poi_id'] = $poi_id;
            switch ($type) {
                case 2:
                    //票号
                    $ticket_info = TicketModel::model()->getById($code);
                    // if (!$ticket_info) {
                    //     throw new Lang_Exception('没有可核销的门票');
                    // }
                    $order_id = $ticket_info['order_id'];
                    $where['ticket_id'] = $code;
                    break;
                case 3:
                    //产品号
                    $item_info = OrderItemModel::model()->getById($code);
                    // if (!$item_info) {
                    //     throw new Lang_Exception('没有可核销的门票');
                    // }
                    $order_id = $item_info['order_id'];
                    $where['order_item_id'] = $code;
                    break;
                default:
                    //订单号
                    $order = $OrderModel->getById($code);
                    // if (!$order) {
                    //     throw new Lang_Exception('没有可核销的门票');
                    // }
                    $order_id = $code;
                    break;
            }
            $where['order_id'] = $order_id;
            !$order && $order = $OrderModel->getById($order_id);
            // 订单产品
            // $OrderModel->checkEnable($order, 1);
            //获取当前景点未使用的票
            list($ticket_codes,$order_codes) = TicketItemsModel::model()->getTicketList($where);
            $ticket_codes = $ticket_codes[1];
            // if (!$ticket_codes) {
            //     throw new Lang_Exception('没有可核销的门票');
            // }
            // if (count($ticket_codes)<$nums){
            //     throw new Lang_Exception('门票不足');//票不足
            // }

            // 顺序使用指定数量的票
            ksort($ticket_codes);
            $ids = array_slice(array_keys($ticket_codes), 0, $nums);

            if($order['is_once_taken'] == 1){
                $arr_order_item_ids = array();
                foreach($ids as $v){
                    $arr_order_item_ids[] = $ticket_codes[$v]['order_item_id'];
                }
                $tmp_ids = TicketModel::model()->search(array('order_item_id|in'=>$arr_order_item_ids));
                $ids = array_keys($tmp_ids);
                // STEP 1 一次取票更新验票点
                $OrderModel->updateTicketItem($ids, null, null);
            }else {
                // STEP 1 更新验票点
                $OrderModel->updateTicketItem($ids, $landscape_id, $poi_id);
            }

            $tickets = array();
            $products = array();
            $ticket_items = TicketItemsModel::model()->search(array('ticket_id|in'=>$ids));
            foreach($ticket_items as $value) {
                $tickets[$value['status']][$value['ticket_id']] = $value['ticket_id'];
                $products[$value['status']][$value['order_item_id']] = $value['order_item_id'];
            }
            // STEP 2 更新票
            $OrderModel->updateTicket($tickets);
            // STEP 3 更新ORDERITEM
            $OrderModel->updateOrderItem($products);
            // STEP 4 更新ORDER
            $left_nums = $OrderModel->updateOrder($order_id, $order);
            // 团客票多余得票申请退票(需产品是可退的)
            if($left_nums>0 && $order['source']==0 && $order['refund']==1 && $order['partner_type']==0) {
                $order_items = OrderItemModel::model()->search(array('order_id'=>$order_id,'status'=>1));
                if (!empty($order_items)) {
                    $order_item0 = reset($order_items);
                    if ($order['is_once_verificate']==1){ //是否一次验票
                        RefundApplyModel::model()->refundOrder($order, $order_items, array(), array(
                            'remark'=>'核销后自动退票',
                            'nums'=>$left_nums,
                            'u_id'=>$body['user_id']?$body['user_id']:$order['user_id'],
                            'user_id'=>$body['user_id']?$body['user_id']:$order['user_id'],
                            'user_account'=>$body['user_account']?$body['user_account']:$order['user_account'],
                            'user_name'=>$body['user_name']?$body['user_name']:$order['user_name'],
                        ));
                    }
                }
            }
            $OrderModel->commit();

        } catch(Exception $e) {
            $OrderModel->rollback();
            $args = func_get_args();
            $logs = json_encode($args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $time = date("Y-m-d H:i:s");
            Log_Base::save("useTicketAsync.log", "[{$time}]{$code}|{$logs}");
            //保存参数并发邮件通知
            $msg = $e->getMessage();
            $content = 'method: '.__METHOD__."\n message: {$msg}\n params: ".var_export($args, true);
            MailModel::sendSrvGroup("核消异步操作失败", $content);
        }
    }

    /**
     * 更新核销景点状态
     * @param  [type] $ids          [description]
     * @param  [type] $landscape_id [description]
     * @param  [type] $poi_id       [description]
     * @return [type]               [description]
     */
    protected function updateTicketItem($ids,$landscape_id,$poi_id, $flag = 0) {
        $where = array();
        $where['ticket_id|in'] = $ids;
        if ($landscape_id) $where['landscape_id'] = $landscape_id;
        if ($poi_id) $where['poi_id'] = $poi_id;
        if ($flag == 0) {
            $attr = array('status' => 2,'updated_at'=>time());
        } else {
            // 撤销
            $attr = array('status' => 1,'updated_at'=>time());
        }
        TicketItemsModel::model()->updateByAttr($attr, $where);
    }

    /**
     * 更新门票码的核销数量、状态及核销时间
     * @param  [type] $ids [description]
     * @return [type]              [description]
     */
    protected function updateTicket(&$tickets, $flag = 0) {
        $now = time();
        if ($flag == 0) {
            if ($tickets[2]) {
                TicketModel::model()->update(array('status'=>2), array('id|in'=>$tickets[2]));
                TicketModel::model()->update(array('use_time'=>$now), array('id|in'=>$tickets[2],'use_time'=>0));
            }
        } else {
            //撤销
            if ($tickets[1]) {
                TicketModel::model()->update(array('status'=>1,'use_time'=>0), array('id|in'=>$tickets[1]));
            }
        }
    }

    /**
     * 更新产品码得核销数量、状态及核销时间
     * @param  [type] $use_products [description]
     * @return [type]               [description]
     */
    protected function updateOrderItem(&$products, $flag = 0) {
        $now = time();
        if ($flag == 0) {
            if ($products[2]) {
                OrderItemModel::model()->update(array('status'=>2), array('id|in'=>$products[2]));
                OrderItemModel::model()->update(array('use_time'=>$now), array('id|in'=>$products[2],'use_time'=>0));
            }
        } else {
            //撤销
            if ($products[1]) {
                OrderItemModel::model()->update(array('status'=>1,'use_time'=>0), array('id|in'=>$products[1]));
            }
        }
    }

    /**
     * 更新订单的核销数量、状态
     * @param  [type] $order_id     [description]
     * @param  [type] $use_products [description]
     * @param  [type] &$order       [description]
     * @return [type]               [description]
     */
    protected function updateOrder($order_id, &$order, $flag = 0) {
        $order_items = OrderItemModel::model()->search(array('order_id'=>$order_id));
        $now = time();
        $products = array();
        $useTimeArr =array();
        $firstUseTime = 0;
        foreach($order_items as $value) {
            $products[$value['status']][$value['id']] = $value;
            if($value['use_time']>0) $useTimeArr[] = $value['use_time'];
            //if($value['use_time']>$lastUseTime) $lastUseTime = $value['use_time'];
        }
        if(!empty($useTimeArr)) {
            $firstUseTime = min($useTimeArr);
        }
        $used_nums = $products[2] ? count($products[2]) : 0;
        $left_nums = $products[1] ? count($products[1]) : 0;
        $save = array('used_nums'=>$used_nums,'updated_at'=>$now, 'use_time'=>$firstUseTime, 'use_status'=>$used_nums>0?1:0);
        if ($flag == 0) { //核销
            if ($left_nums<=0) $save['status'] = 'finish';
        } else { //撤销
            if ($order['status'] == 'finish') {
                $save['status'] = 'paid';
            }
            if ($order['is_once_verificate']==1) { //是否一次验票
                //检查是否有未处理退票申请，有则取消退票
                $refundApplyModel = new RefundApplyModel();
                $refundApplys = $refundApplyModel->search(array('order_id' => $order_id, 'allow_status' => 0, 'status' => 0, 'audited_by' => 0, 'is_del' => 0), "id");
                if ($refundApplys) {
                    $user_id = intval($this->body['user_id']);
                    $user_account = trim(Tools::safeOutput($this->body['user_account']));
                    $user_name = trim(Tools::safeOutput($this->body['user_name']));
                    foreach ($refundApplys as $ra) {
                        $refundParams = array(
                            'id' => $ra['id'],
                            'allow_status' => 3,
                            'reject_reason' => '撤销核销自动驳回退票申请',
                            'user_id' => $user_id ? $user_id : $order['user_id'],
                            'user_account' => $user_account ? $user_account : $order['user_account'],
                            'user_name' => $user_name ? $user_name : $order['user_name'],
                        );
                        $cancelOk = $refundApplyModel->checkApply($refundParams);
                        if ($cancelOk) {
                            $refundApplyModel->deleteById($ra['id']);
                        }
                    }
                }
            }
        }
        $this->updateById($order_id, $save);
        return $left_nums;
    }


    /**
     * 取消核销
     * @param  [type]  $code         [description]
     * @param  integer $landscape_id [description]
     * @param  integer $poi_id       [description]
     * @param  integer $nums         [description]
     * @return [type]                [description]
     */
    public function cancelTicket($code, $landscape_id = 0, $poi_id = 0, $nums = 1) {
        $type = intval(substr("$code", 0, 1));
        $order_id = '1'.substr("$code", 1);

        // 订单产品
        $order = $this->getById($order_id);
        if (!$order) {
            throw new Lang_Exception("门票不存在");
        }
        // $this->checkEnable($order, 1);

        //获取当前景点未使用的票
        $where = array();
        $where['order_id'] = $order_id;
        if ($landscape_id) $where['landscape_id'] = $landscape_id;
        if ($poi_id) $where['poi_id'] = $poi_id;
        if ($type==2) $where['ticket_id'] = $code;
        if ($type==3) $where['order_item_id'] = $code;
        list($ticket_codes,$order_codes) = TicketItemsModel::model()->getTicketList($where);
        $ticket_codes = $ticket_codes[2];
        if (!$ticket_codes) {
            throw new Lang_Exception('没有可撤销的门票');
        }
        $used_nums = count($ticket_codes);
        if ($used_nums < $nums) {
            $nums = $used_nums;
        }

        // 顺序撤销指定数量的票
        ksort($ticket_codes);
        $ids = array_slice(array_keys($ticket_codes), 0, $nums);

        // STEP 1 更新验票点
        $this->updateTicketItem($ids,$landscape_id,$poi_id, 1);
        $tickets = array();
        $products = array();
        $ticket_items = TicketItemsModel::model()->search(array('ticket_id|in'=>$ids));
        foreach($ticket_items as $value) {
            $tickets[$value['status']][$value['ticket_id']] = $value['ticket_id'];
            $products[$value['status']][$value['order_item_id']] = $value['order_item_id'];
        }
        //清除缓存
        OrderItemModel::model()->deleteRedisCache($order_id);
        TicketItemModel::model()->deleteRedisCache($order_id);
        // STEP 2 更新票
        $this->updateTicket($tickets, 1);
        // STEP 3 更新ORDERITEM
        $this->updateOrderItem($products, 1);
        // STEP 4 更新ORDER
        $this->updateOrder($order_id, $order, 1);
        return true;
    }

    public function update($data, $where = null)
    {
        $result = parent::update($data, $where);
        if ($result) {
            OrderEventModel::send($where);
        }
        return $result;
    }
}


