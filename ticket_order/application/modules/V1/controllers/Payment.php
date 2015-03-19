<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-25
 * Time: 下午2:37
 */

class PaymentController extends Base_Controller_Api {

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

        $id = $this->body['id'];
        $ids = $this->body['ids'];
        (!is_array($ids) && preg_match("/^[\d,]+$/",$ids)) && $ids = explode(',',$ids);
        $id && $ids[] = $id;

        $order_id = $this->body['order_id'];
        $order_ids = $this->body['order_ids'];
        (!is_array($order_ids) && preg_match("/^[\d,]+$/",$order_ids)) && $order_ids = explode(',',$order_ids);
        $order_id && $order_ids[] = $order_id;
        if($order_ids){
            $payment_ids = OrderModel::model()->setTable($order_ids[0])->search(array('id|IN'=>$order_ids),"payment_id as id");
            $ids = array_keys($payment_ids);
        }

        $ids && $where['id|IN'] = $ids; //按支付单的ID查找

        $start_date = trim(Tools::safeOutput($this->body['start_date']));
        ($start_date && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$start_date)) && Lang_Msg::error('ERROR_START_DAY_1');
        $start_date && $where['created_at|>='] = strtotime($start_date);

        $tableYm = $start_date ? strtotime($start_date):'' ;

        $end_date = trim(Tools::safeOutput($this->body['end_date']));
        ($end_date && !preg_match("/^\d{4}-\d{2}-\d{2}$/",$end_date)) && Lang_Msg::error('ERROR_END_DAY_1');
        $end_date && $where['created_at|<='] = strtotime($end_date." 23:59:59");

        $status = trim(Tools::safeOutput($this->body['status']));
        ($status && !in_array($status,array('succ','fail','cancel','error','invalid','progress','timeout','ready'))) && Lang_Msg::error("ERROR_UPDATE_2"); //状态参数有错
        $status && $where['status']= $status;

        $distributor_id = intval($this->body['distributor_id']); //分销商ID
        $distributor_id && $where['distributor_id'] = $distributor_id;

        $PaymentModel = new PaymentModel();
        $count = $PaymentModel->share($tableYm)->countResult($where);
        $pagination = Tools::getPagination($this->getParams(),$count);
        $data = $count>0  ? $PaymentModel->share($tableYm)->search($where,$fields,$order,$pagination['limit']) : array();

        $result = array(
            'data'=>array_values($data),
            'pagination'=>array(
                'count'=>$count,
                'current'=>$pagination['current'],
                'items'=>$pagination['items'],
                'total'=>$pagination['total'],
            )
        );
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
        !$id && Lang_Msg::error("ERROR_PAYMENT_1");
        $where['id'] = $id;

        $order_id = trim(Tools::safeOutput($this->body['order_id'])); //订单号
        $order_id && $where['order_ids|LIKE'] = array("%{$order_id}%");

        $detail = PaymentModel::model()->setTable($id)->search($where,$fields);
        !$detail && Lang_Msg::error("ERROR_PAYMENT_2");
        $detail = $detail[$id];
        Lang_Msg::output($detail);
    }

    public function addAction(){
        $params = $this->getOperator(); //获取操作者
        $params['distributor_id'] = intval($this->body['distributor_id']); //分销商ID
        $params['order_ids'] = $this->body['order_ids'];
        $params['payment'] = trim(Tools::safeOutput($this->body['payment']));
        $params['status'] = trim(Tools::safeOutput($this->body['status']));
        $params['account'] = trim(Tools::safeOutput($this->body['account']));
        $params['bank'] = trim(Tools::safeOutput($this->body['bank']));
        $params['pay_account'] = trim(Tools::safeOutput($this->body['pay_account']));
        $params['remark'] = trim(Tools::safeOutput($this->body['remark']));
        $params['payment_bn'] = trim(Tools::safeOutput($this->body['payment_bn']));

        $PaymentModel = new PaymentModel();
        $PaymentModel->begin();
        $info = $PaymentModel->addPayment($params);
        if($info['order_ids']){
            $PaymentModel->commit();
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'), $info);
        }
        else{
            $PaymentModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }
    }

    public function updateAction(){
        $operator = $this->getOperator(); //获取操作者
        $where = array('deleted_at'=>0);
        $id = trim(Tools::safeOutput($this->body['id']));
        !$id && Lang_Msg::error("ERROR_PAYMENT_1");
        $where['id'] = $id;

        $distributor_id = intval($this->body['distributor_id']); //分销商ID
        !$distributor_id && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
        $distributor_id && $where['distributor_id'] = $distributor_id;

        $PaymentModel = new PaymentModel();
        $detail = $PaymentModel->setTable($id)->search($where);
        !$detail && Lang_Msg::error("ERROR_PAYMENT_2");
        $data = $detail[$id];

        $deleted = intval($this->body['deleted']);
        if(!$deleted){
            $payment = trim(Tools::safeOutput($this->body['payment']));
            if(in_array($payment,explode(',',$this->config['pay_type']['online'])))
                $pay_type = 'online';
            else if(in_array($payment,array_keys($this->config['pay_type'])))
                $pay_type = $payment;
            else
                Lang_Msg::error('ERROR_PAYMENT_3',array('payment'=>$payment)); //不支持该支付方式

            $status = trim(Tools::safeOutput($this->body['status'])); //支付单状态ready,succ,fail$account=trim(Tools::safeOutput($this->body['account']));
            $account=trim(Tools::safeOutput($this->body['account']));
            $bank=trim(Tools::safeOutput($this->body['bank']));
            $pay_account=trim(Tools::safeOutput($this->body['pay_account']));
            $remark=trim(Tools::safeOutput($this->body['remark']));
            $payment_bn=trim(Tools::safeOutput($this->body['payment_bn']));

            ($status && !in_array($status,array('succ','fail','cancel','error','invalid','progress','timeout','ready'))) && Lang_Msg::error("ERROR_UPDATE_2"); //状态参数有错

            $data['status']=='succ'&& Lang_Msg::error('ERROR_PAYMENT_6',array('payment_id'=>$id));

            isset($_POST['status']) && $data['status'] = $status;  //更改状态
            isset($_POST['pay_type']) && $data['pay_type'] = $pay_type;
            isset($_POST['payment']) && $data['payment'] = $payment;
            isset($_POST['account']) && $data['account'] = $account;
            isset($_POST['bank']) && $data['bank'] = $bank;
            isset($_POST['pay_account']) && $data['pay_account'] = $pay_account;
            isset($_POST['remark']) && $data['remark'] = $remark;
            isset($_POST['payment_bn']) && $data['payment_bn'] = $payment_bn;
        }
        $nowTime = time();
        if($deleted){
            $data['deleted_at']= $nowTime;
            $operation_lang_id = 'INFO_PAYMENT_3';
        }
        else{
            $data['updated_at']= $nowTime;
            $operation_lang_id = 'INFO_PAYMENT_2';
        }
        $data['op_id'] = $operator['user_id'];
        try {
            $PaymentModel->begin();
            $r = $PaymentModel->updateById($id, $data);
            if ($r) {
                if (!$deleted) {
                    $OrderModel = new OrderModel();
                    $orderPayInfos = PaymentOrderModel::model()->setTable($id)->search(array('payment_id' => $id), "id,order_id,money");
                    $upData = array('pay_type' => $pay_type, 'payment' => $payment);
                    if ($status == 'succ') {
                        $order_ids = array();
                        $ip = Tools::getIp();
                        foreach ($orderPayInfos as $v) {
                            $upData['status'] = 'paid';
                            $upData['payed'] = $v['money'];
                            $upData['pay_at'] = $data['updated_at'];
                            if (!$OrderModel->updateById($v['order_id'], $upData)) {
                                $PaymentModel->rollback();
                                Lang_Msg::error('ERROR_OPERATE_1');
                            }
                            $order_ids[] = $v['order_id'];
                            $orderInfo = $OrderModel->getById($v['order_id']);
                            $transflowParam = array(
                                'id' => Util_Common::payid(), 'mode' => $payment, 'type' => 1, 'amount' => $v['money'],
                                'supplier_id' => $orderInfo['supplier_id'], 'agency_id' => $data['distributor_id'],
                                'ip' => $ip,
                                'op_id' => $operator['user_id']?$operator['user_id']:$orderInfo['user_id'],
                                'user_name' => $operator['user_name']?$operator['user_name']:$orderInfo['user_name'],
                                'created_at' => $nowTime,
                                'order_id' => $v['order_id'],
                            );
                            if (!TransactionFlowModel::model()->add($transflowParam)) {
                                $PaymentModel->rollback();
                                Lang_Msg::error('ERROR_OPERATE_1');
                            }
                            if(in_array($payment,array_keys($PaymentModel->pay_types))) {
                                $unionParams = array(
                                    'org_id'=> $data['distributor_id'],
                                    'user_id'=> $this->body['user_id']?$this->body['user_id']:1,
                                    'user_account'=> $this->body['user_account']?$this->body['user_account']:'system',
                                    'user_name'=> $this->body['user_name']?$this->body['user_name']:'system',
                                    'money'=> $v['money'],
                                    'in_out'=> 0,
                                    'trade_type'=> 1,
                                    'pay_type'=> $PaymentModel->pay_types[$payment],
                                    'remark'=> $v['order_id'],
                                );
                                $unionRes = ApiUnionMoneyModel::model()->unionInout($unionParams);
                                if(!$unionRes || $unionRes['code']=='fail'){
                                    $PaymentModel->rollBack();
                                    !empty($r['message']) && Lang_Msg::error($unionRes['message']);
                                }
                            }
                        }
                        if(!TicketTemplateModel::model()->batUpTktDayUsedReserve($order_ids)) {
                            $PaymentModel->rollback();
                            Lang_Msg::error('ERROR_OPERATE_1');
                        }
                    } else if ($status == 'fail') {
                        PaymentModel::model()->updateById($id, array('status' => 'fail'));
                        foreach ($orderPayInfos as $v) {
                            $OrderModel->updateById($v['order_id'], array('payment_id' => ''));
                        }
                    }
                } else {
                    PaymentOrderModel::model()->setTable($id)->updateByAttr(array('deleted_at' => $data['deleted_at']), array('payment_id' => $id));
                }
                $PaymentModel->commit();
                Log_Payment::model()->add(array('type' => ($deleted ? 3 : 2), 'num' => 1, 'payment_id' => $id, 'order_ids' => $data['order_ids'], 'content' => Lang_Msg::getLang($operation_lang_id) . '[ID:' . $id . ']'));
                Tools::lsJson(true, Lang_Msg::getLang('ERROR_OPERATE_0'), $data);
            } else {
                $PaymentModel->rollback();
                Lang_Msg::error('ERROR_OPERATE_1');
            }
        } catch ( Exception  $e) {
            Log_Base::save('payment', 'error:'.$e->getMessage());
            Log_Base::save('payment', var_export($this->body,true));
            OrderItemModel::model()->rollback();
            Lang_Msg::error( 'ERROR_GLOBAL_3' );
        }
    }








}