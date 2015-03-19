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

    public function getTable() {
        return $this->tblname;
    }

    public function setTable($id = 0) {
        if (!$id) $this->tblname = $this->basename . date('Ym');
        else  $this->tblname = $this->basename . Util_Common::payid2date($id);
        return $this;
    }

    public function share($ts = 0) {
        if (!$ts) $ts = time();
        $this->tblname = $this->basename . date('Ym', $ts);
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

    public function addPayment($params,$checkDayReserve = 1){
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

        //检查订单中的票是否支持支付方式 $params['pay_type']
        $orderItems = OrderItemModel::model()->setTable($params['order_ids'][0])
            ->search( array('order_id|IN'=>$params['order_ids']),"id,order_id,ticket_template_id,name,payment" );
        foreach($orderItems as $v){
            isset($this->config['ticket_template']['payment'][$params['pay_type']])
            && !in_array($this->config['ticket_template']['payment'][$params['pay_type']],explode(',',$v['payment']))
            && Lang_Msg::error('ERROR_PAYMENT_4',array('order_id'=>$v['order_id'],'ticket_name'=>$v['name'],'payment'=>$params['payment'])); //不支持该支付方式
        }

        $PaymentModel = new PaymentModel();
        $params['orders'] = OrderModel::model()->setTable($params['order_ids'][0])->search(array('id|IN'=>$params['order_ids']));

        $params['amount'] = 0.00;
        foreach($params['orders'] as $v) {
            if($v['payment_id']){
                $paymentInfo = $PaymentModel->getById($v['payment_id']);
                if($paymentInfo){
                    $tmpOrdreIds = explode(',',$paymentInfo['order_ids']);
                    $tmp1 = array_diff($tmpOrdreIds,$params['order_ids']);
                    $tmp2 = array_intersect($tmpOrdreIds,$params['order_ids']);
                    if(!$tmp1 && count($tmp2)==count($params['order_ids'])){
                        $paymentInfo['has_paid'] = 1;
                        return $paymentInfo;
                    }
                }
                Lang_Msg::error('ERROR_PAYMENT_5',array('order_id'=>$v['id'],'payment_id'=>$v['payment_id']));
            }
            $params['amount'] += $v['amount'];
        }

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
                $upData = array('pay_type'=>$params['pay_type'],'payment'=>$params['payment'],'payment_id'=>$data['id']);
                if($params['status']=='succ'){
                    foreach($params['orders'] as $v) {
                        $upData['status'] = 'paid';
                        $upData['payed'] = $v['amount'];
                        $upData['pay_at'] = $data['created_at'];
                        if(!OrderModel::model()->updateById($v['id'],$upData)){
                            return false;
                        }
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
                                'user_id'=> $this->body['user_id']?$this->body['user_id']:1,
                                'user_account'=> $this->body['user_account']?$this->body['user_account']:'system',
                                'user_name'=> $this->body['user_name']?$this->body['user_name']:'system',
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
                    }

                    if($checkDayReserve && !TicketTemplateModel::model()->batUpTktDayUsedReserve($params['order_ids'])){
                        return false;
                    }
                }
                else {
                    $rc = TicketTemplateModel::model()->chkIsAblePay($params['order_ids']);
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
                return $data;
            }
        }
        return false;
    }
}