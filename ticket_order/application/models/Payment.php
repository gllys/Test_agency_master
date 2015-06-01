<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-25
 * Time: 下午12:06
 */

class PaymentModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'payments';
    protected $basename = 'payments';
    protected $pkKey = 'id';
   // protected $preCacheKey = 'cache|PaymentModel|';
    protected $preCacheKey='';
    protected $autoShare = 1;
    public $pay_types = array('kuaiqian'=>1,'alipay'=>2);
    public $payments = array(
        'offline'=>'线下', 'cash'=>'现金', 'pos'=>'POS', 'credit'=>'信用', 'advance'=>'储值', 'union'=>'平台',
        'kuaiqian'=>'块钱', 'alipay'=>'支付宝',
    );

    public function getTable() {
        return $this->tblname;
    }

    public function setTable($id = 0) {
        // if (!$id) $this->tblname = $this->basename . date('Ym');
        // else  $this->tblname = $this->basename . Util_Common::payid2date($id);
        return $this;
    }

    public function share($ts = 0) {
        // if (!$ts) $ts = time();
        // $this->tblname = $this->basename . date('Ym', $ts);
        return $this;
    }

    public function finishPayment($req) {
        $id = $req['orderId'];
        $info = $this->getById($id);
        if($info['status'] == 'succ') {
           throw new Exception("ERROR_PAYMENT_1");
        }

        $data = array();
        $data['status'] = 'succ';
        $data['updated_at'] = time();
        $data['payment_bn'] = $req['dealId'];
        $data['remark'] = $req['remark'];
        $data['bank'] = $req['bankId'];
        $data['account'] = $req['merchantAcctId'];
        return $this->updateById($id, $data);
    }

    //支付，$prodInfo参数一般在/order/addPay接口会提供
    public function addPayment($params,$checkDayReserve = 1,$prodInfo=array(),$source=0) {
        !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
        !$params['order_ids'] && Lang_Msg::error('ERROR_ORDER_INFO_1'); //缺少订单号ID参数
        (!is_array($params['order_ids']) && preg_match("/^[\d,]+$/",$params['order_ids'])) && $params['order_ids'] = explode(',',$params['order_ids']);
        if(in_array($params['payment'],explode(',',$this->config['pay_type']['online'])))
            $params['pay_type'] = 'online';
        else if(in_array($params['payment'],array_keys($this->config['pay_type'])))
            $params['pay_type'] = $params['payment'];
        else
            Lang_Msg::error('ERROR_PAYMENT_3',array('payment'=>$params['payment'])); //不支持该支付方式
        ($params['status'] && !in_array($params['status'],array('succ','fail','cancel','error','invalid','progress','timeout','ready'))) && Lang_Msg::error("ERROR_UPDATE_2"); //状态参数有错

        if($params['activity_paid']>0){ //检查抵用券是否充足
            $unionMoneyDetail = ApiUnionMoneyModel::model()->unionMoneyDetail($params['distributor_id']);
            if(!$unionMoneyDetail || $unionMoneyDetail['code']=="fail" || $unionMoneyDetail['body']['activity_money']<$params['activity_paid'])
                Lang_Msg::error('ERR_COUPON_1'); //抵用券金额不足
        }

        $orderCount = count($params['order_ids']);

        //检查订单中的票是否支持支付方式 $params['pay_type']
        $params['orders'] = OrderModel::model()->setTable($params['order_ids'][0])->search(array('id|IN'=>$params['order_ids']));
        foreach($params['orders'] as $v){
            if(!empty($prodInfo) && $orderCount==1) {
                $v['product_payment'] = $prodInfo['payment'];
            }
            if(in_array($v['status'], array('unaudited','reject'))){
                Lang_Msg::error('ERROR_PAYMENT_9',array('order_id'=>$v['id'])); //订单［{order_id}］正在确认中，请在确认通过后再支付
            }
            if($source==0 && isset($this->config['ticket_template']['payment'][$params['pay_type']])
            && !in_array($this->config['ticket_template']['payment'][$params['pay_type']],explode(',',$v['product_payment']))) {
                Lang_Msg::error('ERROR_PAYMENT_4',array('order_id'=>$v['id'],'ticket_name'=>$v['name'],'payment'=>$params['payment']));
            } //不支持该支付方式
        }

        $PaymentModel = new PaymentModel();


        $params['amount'] = 0.00;
        $activity_paid = $params['activity_paid'];
        foreach($params['orders'] as $k=>$v) {
            if($v['payment_id']){
                $paymentInfo = $PaymentModel->get(array('id'=>$v['payment_id']));
                if($paymentInfo){
                    $tmpOrdreIds = explode(',',$paymentInfo['order_ids']);
                    $tmp1 = array_diff($tmpOrdreIds,$params['order_ids']);
                    $tmp2 = array_intersect($tmpOrdreIds,$params['order_ids']);
                    if(!$tmp1 && count($tmp2)==count($params['order_ids'])){
                        $paymentInfo['has_paid'] = 1;
                        return $paymentInfo;
                    }
                    else {
                        Lang_Msg::error('ERROR_PAYMENT_5',array('order_id'=>$v['id'],'payment_id'=>$v['payment_id']));
                    }
                }
            }
            $params['amount'] += $v['amount'];
            if($activity_paid>=$v['amount']){
                $params['orders'][$k]['payed'] = 0; //扣除抵用券支付金额
                $params['orders'][$k]['activity_paid'] = $v['amount']; //抵用券支付金额
                $activity_paid -= $v['amount'];
            }
            else{
                $params['orders'][$k]['payed'] = $v['amount'] - $activity_paid; //扣除抵用券支付金额
                $params['orders'][$k]['activity_paid'] = $activity_paid;
                $activity_paid = 0;
            }
        }
        $params['activity_paid'] = $params['activity_paid']>=$params['amount']?$params['amount']:$params['activity_paid'];

        $nowTime = time();
        $ip =  Tools::getIp();
        $data = array(
            'id'=> Util_Common::payid(),
            'distributor_id'=>$params['distributor_id'],
            'order_ids'=> implode(',',$params['order_ids']),
            'status'=> $params['status']?$params['status']:'ready',
            'pay_type'=> $params['pay_type'], //支付方式类型：线上、线下、信用支付、储值支付
            'payment'=> $params['payment'], //支付渠道:cash,pos,offline,credit,advance,union,alipay,kuaiqian,taobao
            'amount'=> $params['amount'],
            'activity_paid'=> $params['activity_paid'],
            'account'=>$params['account'],
            'bank'=>$params['bank'],
            'pay_account'=>$params['pay_account'],
            'remark'=>$params['remark'],
            'payment_bn'=>$params['payment_bn'],
            'ip'=> $ip,
            'op_id'=> $params['user_id'],
            'created_at'=> $nowTime,
            'updated_at'=> $nowTime
        );
        $r = $this->add($data);
        if($r){
            $r = PaymentOrderModel::model()->addBatch($data,$params['orders']);
            if($r){
                $upData = array(
                    'pay_type'=>$params['pay_type'],'payment'=>$params['payment'],
                    'payment_id'=>$data['id'], 'pay_status' => 0 //未支付
                );
                if($params['status']=='succ'){
                    $pay_rate = PayRateModel::model()->getRate($params['payment']); //获取费率
                    foreach($params['orders'] as $v) {
                        $transflowParam = array(
                            'id'=>Util_Common::payid(),   'mode'=>$params['payment'],           'type'=>1,
                            'amount'=>$v['amount'],     'supplier_id'=>$v['supplier_id'],   'agency_id'=>$params['distributor_id'],
                            'ip'=>$ip,
                            'op_id'=>$params['user_id']?$params['user_id']:$v['user_id'],
                            'user_name' => $params['user_name']?$params['user_name']:$v['user_name'],
                            'created_at'=>$nowTime,
                            'order_id' => $v['id'],
                        );
                        if(!TransactionFlowModel::model()->add($transflowParam)){
                            return false;
                        }
                        if(in_array($params['payment'],array_keys($PaymentModel->pay_types))) {
                            $unionParams = array(
                                'org_id'=> $params['distributor_id'],
                                'user_id'=> $this->body['user_id']?$this->body['user_id']:$v['user_id'],
                                'user_account'=> $this->body['user_account']?$this->body['user_account']:$v['user_account'],
                                'user_name'=> $this->body['user_name']?$this->body['user_name']:$v['user_name'],
                                'money'=> $v['amount'],
                                'in_out'=> 0,
                                'trade_type'=> 1,
                                'pay_type'=> $PaymentModel->pay_types[$params['payment']],
                                'remark'=> $v['id'],
                            );
                            $unionRes = ApiUnionMoneyModel::model()->unionInout($unionParams);
                            if(!$unionRes || $unionRes['code']=='fail'){
                                return false;
                            }
                        }

                        $upData['status'] = 'paid';
                        $upData['pay_status'] = 2; //已支付
                        $upData['payed'] = $v['amount']-$v['activity_paid']; //扣除抵用券支付金额
                        $upData['activity_paid'] = $v['activity_paid']; //抵用券支付金额
                        $upData['pay_at'] = $data['created_at'];
                        $upData['pay_rate'] = $pay_rate; //费率
                        if(!$this->chgOrderStatusOnSucc($v['id'],$upData)){
                            return false;
                        }
                    }

                    if($checkDayReserve && !TicketTemplateModel::model()->batUpTktDayUsedReserve($params['order_ids'],$prodInfo)){
                        return false;
                    }
                    //扣除抵用券操作
                    $order_user_info = reset($params['orders']);
                    if(0<$params['activity_paid']){
                        $unionParams = array(
                            'org_id'=> $params['distributor_id'],
                            'user_id'=> $this->body['user_id']?$this->body['user_id']:$order_user_info['user_id'],
                            'user_account'=> $this->body['user_account']?$this->body['user_account']:$order_user_info['user_account'],
                            'user_name'=> $this->body['user_name']?$this->body['user_name']:$order_user_info['user_name'],
                            'money'=> 0,
                            'activity_money'=>$params['activity_paid'],
                            'in_out'=> 0,
                            'trade_type'=> 1,
                            'pay_type'=> 0,
                            'remark'=> $data['id']."（使用抵用券）",
                        );
                        $unionRes = ApiUnionMoneyModel::model()->unionInout($unionParams);
                        if(!$unionRes || $unionRes['code']=='fail'){
                            return false;
                        }
                    }

                    foreach($params['orders'] as $order){ //将支付后的订单添加至redis列队，以便定时脚本通知大漠
                        if($order['partner_type']>0 && $order['partner_product_code']!='') {
                            OpenApiPartnerModel::model()->orderToRds($order);
                        }
                    }
                }
                else {
                    $rc = TicketTemplateModel::model()->chkIsAblePay($params['order_ids'],$prodInfo);
                    if(!$rc){
                        return false;
                    }
                    $ro = OrderModel::model()->setTable($params['order_ids'][0])->updateByAttr($upData,array('id|IN'=>$params['order_ids']));
                    if(!$ro){
                        return false;
                    }
                }
                Log_Payment::model()->add(
                    array(
                        'type'=>1,'num'=>1,'payment_id'=>$data['id'],'order_ids'=>$data['order_ids'],
                        'content'=>Lang_Msg::getLang('INFO_PAYMENT_1',array('id'=>$data['id'],'order_ids'=>$data['order_ids']))
                    )
                );
                //异步存入消息队列
                $r = TicketQueueModel::model()->sendOrderIds($params['order_ids']);
                if ($r==false) {
                    throw new Lang_Exception('增加订单内容失败');
                }
                return $data;
            }
        }
        return false;
    }

    public function chgOrderStatusOnSucc($orderId,$upData){
        if(!$orderId || !$upData) return false;
        if(!OrderModel::model()->updateById($orderId,$upData)){
            return false;
        }
        if(!OrderItemModel::model()->update(array('payment'=>$upData['payment'],'status'=>1),array('order_id'=>$orderId))){
            return false;
        }
        /*if(!TicketModel::model()->update(array('status'=>1),array('order_id'=>$orderId))){
            return false;
        }
        if(!TicketItemModel::model()->update(array('status'=>1),array('order_id'=>$orderId))){
            return false;
        }*/
        return true;
    }
}