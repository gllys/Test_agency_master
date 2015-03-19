<?php

class NpController extends Base_Controller_Ota
{
    public function indexAction() {
        //
        $data = array("result"=>1);
        Lang_Msg::output($data);
    } 

    public function tokenAction() {
        Log_Base::save('np','tokenAction:'.var_export($this->body, true));
        $token = strtoupper(md5(Util_Common::uniqid('token')));
        
        try{
        	$this->sess->destroy($this->userinfo['token']);
        	$this->userinfo['token'] = $token;
        	OtaAccountModel::model()->updateById($this->userinfo['id'], array('token'=>$token));

        	session_id($token);
	        $this->sess->start();
	        $this->sess->userinfo = $this->userinfo;

        } catch(Exception $e) {
            Log_Base::save('np_error','body:'.var_export($this->body, true));
            Log_Base::save('np_error','message:'.$e->getMessage());
        	Lang_Msg::error("ERROR_TOKEN_1");
        }
        
        $data = array();
        $data["token"] = $token;
        $data["expire"] = $this->sess->getMaxLifeTime();
        Log_Base::save('np','output:'.var_export($data, true));
        Lang_Msg::output($data);
    } 

    /**
     * 发码接口
     * @return [type] [description]
     */
    public function gencodeAction() {
        Log_Base::save('np','gencodeAction:'.var_export($this->body, true));
    	$nums = intval($this->body['nums']);
    	$user_name = trim($this->body['user_name']);
    	$user_mobile = trim($this->body['user_mobile']);
    	$user_card = trim($this->body['user_card']);
        $use_day = trim($this->body['use_day']);
    	$price_type = 0;//散客
        $nowtime = time();
        $items = ConfigModel::model()->getConfig(array('activity_ticket_id', 'activity_distributor_id','activity_use_day','activity_payment'));
        $ticket_template_id = $items['activity_ticket_id']; //一元票
        $distributor_id = $items['activity_distributor_id'];//分销商
        $use_day = $use_day ? $use_day : date("Y-m-d"); //使用日期
        $payment =  $items['activity_payment'] ? $items['activity_payment'] : 'credit';

    	if ($nums <=0) Lang_Msg::error("ERROR_GENCODE_1");
    	if (!$user_name) Lang_Msg::error("ERROR_GENCODE_2");
    	if (!Validate::isMobilePhone($user_mobile)) Lang_Msg::error("ERROR_GENCODE_3");
    	if (!Validate::isCard($user_card)) Lang_Msg::error("ERROR_GENCODE_4");
        if(strtotime($use_day.' 23:59:59')<$nowtime) Lang_Msg::error('ERROR_USEDAY_3');
        
        // 异步处理
        $order_id = Util_Common::uniqid(1);
        Process_Async::send(array('OrderModel','gencodeByAsync'),array(array(
            'nums' => $nums,
            'user_name' => $user_name,
            'user_mobile' => $user_mobile,
            'user_card' => $user_card,
            'use_day' => $use_day,
            'price_type'=>$price_type,
            'ticket_template_id' => $ticket_template_id,
            'distributor_id' => $distributor_id,
            'payment' => $payment,
            'userinfo' => $this->userinfo,
            'order_id' => $order_id,
            )));

        $data = array();
        $data["code"] = $order_id;
        Log_Base::save('np','output:'.var_export($data, true));
        Lang_Msg::output($data);
        // return true;

        // try{
        // 	//下单
        //     $orderParams = array(
        //         'ticket_template_id'=>$ticket_template_id,
        //         'price_type'=>$price_type,
        //         'distributor_id'=>$distributor_id,
        //         'use_day'=>$use_day,
        //         'nums'=>$nums,
        //         'owner_name'=>$user_name,
        //         'owner_mobile'=>$user_mobile,
        //         'owner_card'=>$user_card,
        //         'remark'=>'OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']',
        //         'user_id'=>1,
        //         'user_name'=>'system',
        //     );
        //     TicketTemplateModel::model()->setExpireTime(86400);
        //     OrganizationModel::model()->setExpireTime(86400);
        //     $OrderModel = new OrderModel();
        //     $OrderModel->begin();
        //     $orderInfo = $OrderModel->addOrder($orderParams);
        //     if(!$orderInfo) {
        //         $OrderModel->rollback();
        //         Lang_Msg::error("ERROR_OPERATE_1");
        //     }
        //     //支付（信用）
        //     $paymentParams = array(
        //         'distributor_id'=> $distributor_id,
        //         'order_ids'=> $orderInfo['id'],
        //         'status'=> 'succ',
        //         'payment'=> $payment,
        //         'remark'=>'OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']',
        //         'user_id'=>1,
        //         'user_name'=>'system',
        //     );
        //     $paymentInfo = PaymentModel::model()->addPayment($paymentParams, 0);
        //     if(!$paymentInfo){
        //         $OrderModel->rollback();
        //         Lang_Msg::error("ERROR_OPERATE_1");
        //     }

        //     if(empty($paymentInfo['has_paid']) && in_array($payment,array('credit','advance','union'))) {   //扣款
        //         if($payment=='union'){ //平台支付

        //         }
        //         else { //信用、储值支付
        //             $res = OrganizationModel::model()->creditPay(array(
        //                 'distributor_id' => $distributor_id,
        //                 'supplier_id' => $orderInfo['supplier_id'],
        //                 'money' => $paymentInfo['amount'],
        //                 'type' => $payment == 'credit' ? 0 : 1,
        //                 'serial_id' => $paymentInfo['id']
        //             ));
        //             if ($res['code'] == 'fail') {
        //                 $OrderModel->rollback();
        //                 Lang_Msg::error($res['message']);
        //             }
        //         }
        //     }

        //     $OrderModel->commit();
        //     $data = array();
        //     $data["code"] = $orderInfo['id'];
        //     Log_Base::save('np','output:'.var_export($data, true));
        //     Lang_Msg::output($data);
        // } catch(Exception $e){
        //     $OrderModel->rollback();
        //     Log_Base::save('np_error','body:'.var_export($this->body, true));
        //     Log_Base::save('np_error','message:'.$e->getMessage());
        //     Lang_Msg::error("ERROR_OPERATE_1");
        // }
    }

    public function checkstatusAction() {
    	if (!$this->body['code']) Lang_Msg::error("ERROR_CHECKSTATUS_1");
    	$code = explode(',', $this->body['code']);
    	$ids = array();
    	foreach($code as $item) {
    		if (!$item || strlen($item)<15) Lang_Msg::error("ERROR_CHECKSTATUS_2");
            $date = Util_Common::uniqid2date($item);
            if ($date < 201411) Lang_Msg::error("ERROR_CHECKSTATUS_2");
            $ids[$date][] = $item;
        }
        try {
            $OrderModel = new OrderModel();
            $items = array();
            $OrderModel->delCacheList($code);
            foreach($ids as $key => $id) {
            	$rows = $OrderModel->getByIds($id);
            	if ($rows) $items += $rows;
            }
        } catch(Exception $e) {
            Lang_Msg::error("ERROR_CHECKSTATUS_2");
        }
    	$data = array();
        $data["list"] = array();
        foreach($code as $value) {
        	$order = isset($items[$value]) ? $items[$value] : array();
        	$tmp = array();
        	$tmp['code'] = $value;
        	$tmp['status'] = $order['used_nums']>0 ? 1 : 0;
            $tmp['nums'] = $order['nums'];
            $tmp['used_nums'] = $order['used_nums'];
            $tmp['refunded_nums'] = $order['refunded_nums'];
        	$data["list"][] = $tmp;
        }
        Lang_Msg::output($data);
    }

    public function cancelcodeAction() {
        $id = $this->body['code'];
        $refund_nums = abs(intval($this->body['refund_nums']));
    	if (!$id) Lang_Msg::error("ERROR_CANCELCODE_1");
        try {
            $order = OrderModel::model()->getById($id);
            if (!$order) Lang_Msg::error("ERROR_CANCELCODE_1");
            //if ($order['used_nums']>0) Lang_Msg::error("ERROR_CANCELCODE_2");
            else if ($order['status']=='cancel') Lang_Msg::error("ERROR_CANCELCODE_3");
            else if (in_array($order['status'], array('finish','billed'))) Lang_Msg::error("ERROR_CANCELCODE_4");

            $unuse_nums = $order['nums']-$order['used_nums']-$order['refunding_nums']-$order['refunded_nums'];
            if($unuse_nums<1) Lang_Msg::error('ERROR_CANCELCODE_6');
            else if($refund_nums> $unuse_nums) Lang_Msg::error('ERROR_CANCELCODE_7');

            $refundParams = array(
                'nums' => $refund_nums ? $refund_nums : $unuse_nums,
                'order_id' => $id,
                'user_id'=>1,
                'remark'=>'OTA申请退票，OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']',
            );
            !$refundParams['nums'] && Lang_Msg::error("ERROR_CANCELCODE_3");
            $r = RefundApplyModel::model()->applyRefund($refundParams);
            if(!$r){
                Lang_Msg::error("ERROR_OPERATE_1");
            }
            $refundChkParams = array(
                'id'=> $r,
                'user_id'=>1,
                'allow_status'=>1,
            );
            $r = RefundApplyModel::model()->checkApply($refundChkParams);
            if(!$r){
                Lang_Msg::error("ERROR_OPERATE_1");
            }
        } catch(Exception $e) {
            Lang_Msg::error("ERROR_CANCELCODE_5");
        }
        
    	$data = array();
        $data["result"] = 1;
        Lang_Msg::output($data);
    }

}