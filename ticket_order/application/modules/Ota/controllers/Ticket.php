<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-27
 * Time: 上午11:41
 */

class TicketController extends Base_Controller_Ota
{
    public function indexAction() {
        //
        $data = array("result"=>1);
        Lang_Msg::output($data);
    }

    public function tokenAction() {
        $token = strtoupper(md5(Util_Common::uniqid('token')));

        try{
            $this->sess->destroy($this->userinfo['token']);
            $this->userinfo['token'] = $token;
            OtaAccountModel::model()->updateById($this->userinfo['id'], array('token'=>$token));

            session_id($token);
            $this->sess->start();
            $this->sess->userinfo = $this->userinfo;

        } catch(Exception $e) {
            Lang_Msg::error("ERROR_TOKEN_1");
        }

        $data = array();
        $data["token"] = $token;
        $data["expire"] = $this->sess->getMaxLifeTime();
        Lang_Msg::output($data);
    }

    public function checkorderAction() {
        $ticket_codes = trim(Tools::safeOutput($this->body['code']));
        $use_day = trim(Tools::safeOutput($this->body['use_day']));

        if(!preg_match("/^\d{4}-\d{2}-\d{2}$/",$use_day)) Lang_Msg::error('ERROR_USEDAY_1'); //游玩日期不能为空，且格式为xxxx-xx-xx
        else if($use_day<date("Y-m-d")) Lang_Msg::error('ERROR_USEDAY_3');

        $ticket_codes = explode(',', $ticket_codes);
        if(!$ticket_codes) Lang_Msg::error('ERROR_CHECKSTATUS_2');

        $return = array();
        $ticketCodeInfo = TicketCodeModel::model()->listByIds($ticket_codes);
        if(!$ticketCodeInfo) Lang_Msg::error('ERROR_CHECKSTATUS_2');
        foreach($ticket_codes as $ticket_code){
            $distributor_id = $ticketCodeInfo[$ticket_code]['distributor_id'];
            $ticketTemplateIds = $ticketCodeInfo[$ticket_code]['ticket_template_ids'];
            !$ticketTemplateIds && Lang_Msg::error('ERROR_CHECKSTATUS_2');
            $ticketTemplateIds = explode(',',$ticketTemplateIds);

            $tmp = array('code'=>$ticket_code,'status'=>1,'reserve'=>-1);
            $reserves = array();
            foreach($ticketTemplateIds as $ticket_template_id){
                $ticketTemplateInfo = TicketTemplateModel::model()->getInfo($ticket_template_id,0,$distributor_id,$use_day,0);
                if(!$ticketTemplateInfo){
                    $tmp['status']=0;
                    $tmp['reserve']=0;
                }
                else if (isset($ticketTemplateInfo['code']) && $ticketTemplateInfo=='fail') {
                    $tmp['status']=0;
                    $tmp['reserve']=0;
                }
                else {
                    if(!empty($ticketTemplateInfo['day_reserve']) && 0<$ticketTemplateInfo['remain_reserve']) {
                        $reserves[] = $ticketTemplateInfo['remain_reserve'];
                    }
                }
            }
            if($reserves) {
                $tmp['reserve'] = min($reserves);
                if(!$tmp['reserve']) $tmp['status']=0;
            }
            $return[] = $tmp;
        }
        Lang_Msg::output($return);
    }

    //取码
    public function gencodeAction() {
        $ticket_code = trim(Tools::safeOutput($this->body['code']));
        $nums = intval($this->body['nums']);
        $owner_name = trim(Tools::safeOutput($this->body['user_name']));
        $owner_mobile = trim(Tools::safeOutput($this->body['user_mobile']));
        $owner_card = trim(Tools::safeOutput($this->body['user_card']));
        $use_day = trim(Tools::safeOutput($this->body['use_day']));
        $price_type = 0;//散客
        $nowtime = time();

        $items = TicketCodeModel::model()->decodeTicketCode($ticket_code);
        if(!$items || !$items['ticket_template_ids'] || !$items['distributor_id'])
            Lang_Msg::error("ERROR_CHECKSTATUS_2"); //无效的码
        $ticket_template_ids = explode(',',$items['ticket_template_ids']);
        $distributor_id = $items['distributor_id'];//分销商

        $use_day = $use_day ? $use_day : date("Y-m-d"); //使用日期
        $user_id = $this->userinfo['id'];
        $user_name = $this->userinfo['name'];

        if ($nums <=0) Lang_Msg::error("ERROR_GENCODE_1");
        if (!$owner_name) Lang_Msg::error("ERROR_GENCODE_2");
        if (!Validate::isMobilePhone($owner_mobile)) Lang_Msg::error("ERROR_GENCODE_3");
        if (!Validate::isCard($owner_card)) Lang_Msg::error("ERROR_GENCODE_4");
        if(!preg_match("/^\d{4}-\d{2}-\d{2}$/",$use_day)) Lang_Msg::error('ERROR_USEDAY_1'); //游玩日期不能为空，且格式为xxxx-xx-xx
        if(strtotime($use_day.' 23:59:59')<$nowtime) Lang_Msg::error('ERROR_USEDAY_3');

        try{ //下单
            $OrderModel = new OrderModel();
            $OrderModel->begin();

            if(1<count($ticket_template_ids)) { //组合票下单
                $orderParams = array(
                    'cartTicketList'=>array(),           'user_id'=>$user_id,
                    'distributor_id'=>$distributor_id,   'user_name'=>$user_name,
                );
                foreach($ticket_template_ids as $tid){
                    $orderParams['cartTicketList'][] = array(
                        'ticket_template_id'=>$tid,         'price_type'=>$price_type,
                        'use_day'=>$use_day,                'nums'=>$nums,
                        'owner_name'=>$owner_name,          'owner_mobile'=>$owner_mobile,
                        'owner_card'=>$owner_card,          'remark'=>'OTA客户：'.$user_name.'[ID:'.$user_id.']',
                        'ota_account'=>$user_id,            'ota_name'=>$user_name,
                    );
                }
                $orders = $OrderModel->addBatchOrder($orderParams); //批量生成订单
                if(!$orders){
                    $OrderModel->rollback();
                    Lang_Msg::error("ERROR_OPERATE_1");
                }
                $orderGpParams = array(
                    'distributor_id'=>$distributor_id,
                    'ticket_code_id'=>$items['id'],
                    'ticket_template_ids'=>implode(',',$ticket_template_ids),
                    'order_ids'=>implode(',',array_keys($orders)),
                    'ota_account'=>$user_id,
                    'ota_name'=>$user_name,
                    'user_id'=>$user_id,
                    'user_name'=>$user_name,
                );
                $orderInfo = OrderGroupModel::model()->addNew($orderGpParams); //生成组合单记录
                if(!$orderInfo) {
                    $OrderModel->rollback();
                    Lang_Msg::error("ERROR_OPERATE_1");
                }
            }
            else{ //非组合票下单
                $ticket_template_id = reset($ticket_template_ids);
                $orderParams = array(
                    'ticket_template_id'=>$ticket_template_id,
                    'price_type'=>$price_type,
                    'distributor_id'=>$distributor_id,
                    'use_day'=>$use_day,
                    'nums'=>$nums,
                    'owner_name'=>$owner_name,
                    'owner_mobile'=>$owner_mobile,
                    'owner_card'=>$owner_card,
                    'remark'=>'OTA客户：'.$user_name.'[ID:'.$user_id.']',
                    'ota_account'=>$user_id,
                    'ota_name'=>$user_name,
                    'user_id'=>$user_id,
                    'user_name'=>$user_name,
                );
                $orderInfo = $OrderModel->addOrder($orderParams);
                if(!$orderInfo) {
                    $OrderModel->rollback();
                    Lang_Msg::error("ERROR_OPERATE_1");
                }
            }
            $OrderModel->commit();
            $data = array();
            $data["code"] = $orderInfo['id'];
            Lang_Msg::output($data);
        } catch(Exception $e){
            $OrderModel->rollback();
            Log_Base::save('OTA', 'Gencode: error:'.$e->getMessage());
            Log_Base::save('OTA', 'Gencode: '.var_export($this->body,true));
            Lang_Msg::error("ERROR_OPERATE_1");
        }
    }

    //订单支付
    public function dopayAction(){
        $code = trim(Tools::safeOutput($this->body['code'])) ;
        if (!$code) Lang_Msg::error("ERROR_GLOBAL_1");
        $isGroup = 4==substr($code,0,1) ? 1:0;

        $payment =  'credit';
        if($isGroup){ //组合订单
            $orderInfo = OrderGroupModel::model()->getById($code);
        } else {
            $orderInfo = OrderModel::model()->getById($code);
        }
        if(!$orderInfo) Lang_Msg::error("ERROR_CANCELCODE_5");

        $PaymentModel = new PaymentModel();
        $PaymentModel->begin();
        try {
            $order_ids = explode(',', $isGroup?$orderInfo['order_ids']:$orderInfo['id']);
            $orderInfos = OrderModel::model()->getByIds($order_ids);
            $remark = 'OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']';
            foreach ($order_ids as $order_id) {
                $paymentParams = array(
                    'distributor_id'=> $orderInfos[$order_id]['distributor_id'],
                    'order_ids'=> $order_id,
                    'status'=> 'succ',
                    'payment'=> $payment,
                    'remark'=>$remark,
                    'user_id'=>$this->userinfo['id'],
                    'user_name'=>'OTA:'.$this->userinfo['name'],
                );
                $paymentInfo = $PaymentModel->addPayment($paymentParams);
                if(!$paymentInfo){
                    $PaymentModel->rollback();
                    Lang_Msg::error("ERROR_OPERATE_1");
                }
                if(empty($paymentInfo['has_paid']) && in_array($payment,array('credit','advance','union'))){   //扣款
                    if($payment=='union'){ //平台支付

                    }
                    else { //信用、储值支付
                        $res = OrganizationModel::model()->creditPay(array(
                            'distributor_id' => $orderInfos[$order_id]['distributor_id'],
                            'supplier_id' => $orderInfos[$order_id]['supplier_id'],
                            'money' => $paymentInfo['amount'],
                            'type' => $payment == 'credit' ? 0 : 1,
                            'serial_id' => $paymentInfo['id']
                        ));
                        if ($res['code'] == 'fail') {
                            $PaymentModel->rollback();
                            Lang_Msg::error($res['message']);
                        }
                    }
                }
            }
            if($isGroup) {
                $r = OrderGroupModel::model()->updateById($code,array('status'=>1,'updated_at'=>time()));
                if(!$r) {
                    $PaymentModel->rollback();
                    Lang_Msg::error("ERROR_OPERATE_1");
                }
            }
        } catch(Exception $e) {
            $PaymentModel->rollback();
            Log_Base::save('OTA', 'Dopay: error:'.$e->getMessage());
            Log_Base::save('OTA', 'Dopay: '.var_export($this->body,true));
            Lang_Msg::error("ERROR_OPERATE_1");
        }

        $PaymentModel->commit();
        Lang_Msg::output(array('result'=>1));
    }

    //查询订单核销状态
    public function checkstatusAction() {
        $code = trim(Tools::safeOutput($this->body['code']));
        if (!$code) Lang_Msg::error("ERROR_CHECKSTATUS_1");
        $code = explode(',', $code);
        $tmp_codes = array();
        foreach ($code as $id) {
            $isGroup = 4==substr($id,0,1) ? 1:0;
            if($isGroup){
                $orderGroup = OrderGroupModel::model()->getById($id);
                if(!$orderGroup) Lang_Msg::error("ERROR_CHECKSTATUS_1");
                $tmp_codes += explode(',',$orderGroup['order_ids']);
            }
            else
                $tmp_codes[] =$id;
        }
        $code = $tmp_codes;

        $ids = array();
        foreach($code as $item) {
            if (!$item || strlen($item)<15) Lang_Msg::error("ERROR_CHECKSTATUS_2");
            $date = Util_Common::uniqid2date($item);
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
            $tmp['pay_at'] = $order['pay_at'];
            $tmp['status'] = $order['used_nums']>0 ? 1 : 0;
            $tmp['nums'] = $order['nums'];
            $tmp['used_nums'] = $order['used_nums'];
            $tmp['refunded_nums'] = $order['refunded_nums'];
            $data["list"][] = $tmp;
        }
        Lang_Msg::output($data);
    }

    //退码
    public function cancelcodeAction() {
        $id = $this->body['code'];
        $refund_nums = abs(intval($this->body['refund_nums']));
        if (!$id) Lang_Msg::error("ERROR_CANCELCODE_1");
        $OrderModel = new OrderModel();
        $OrderModel->begin();
        try {
            $isGroup = 4==substr($id,0,1) ? 1:0;
            if($isGroup) {
                $orderGroup = OrderGroupModel::model()->getById($id);
                if (!$orderGroup) Lang_Msg::error("ERROR_CANCELCODE_1");
                foreach(explode(',',$orderGroup['order_ids']) as $order_id){
                    $order = $OrderModel->getById($order_id);
                    if (!$order) Lang_Msg::error("ERROR_CANCELCODE_1");
                    else if ($order['status']=='cancel') Lang_Msg::error("ERROR_CANCELCODE_3");
                    else if (in_array($order['status'], array('finish','billed'))) Lang_Msg::error("ERROR_CANCELCODE_4");

                    $unuse_nums = $order['nums']-$order['used_nums']-$order['refunding_nums']-$order['refunded_nums'];
                    if($unuse_nums<1) Lang_Msg::error('ERROR_CANCELCODE_6');
                    else if($refund_nums> $unuse_nums) Lang_Msg::error('ERROR_CANCELCODE_7');

                    $refundParams = array(
                        'nums' => $refund_nums ? $refund_nums : $unuse_nums,
                        'order_id' => $order_id,
                        'user_id'=>$this->userinfo['id'],
                        'remark'=>'OTA申请退票，OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']',
                    );
                    !$refundParams['nums'] && Lang_Msg::error("ERROR_CANCELCODE_3");
                    $r = RefundApplyModel::model()->applyRefund($refundParams);
                    if(!$r){
                        $OrderModel->rollback();
                        Lang_Msg::error("ERROR_OPERATE_1");
                    }
                    $refundChkParams = array(
                        'id'=> $r,
                        'user_id'=>$this->userinfo['id'],
                        'allow_status'=>1,
                    );
                    $r = RefundApplyModel::model()->checkApply($refundChkParams);
                    if(!$r){
                        $OrderModel->rollback();
                        Lang_Msg::error("ERROR_OPERATE_1");
                    }
                }
            }
            else {
                $order = $OrderModel->getById($id);
                if (!$order) Lang_Msg::error("ERROR_CANCELCODE_1");
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
                    $OrderModel->rollback();
                    Lang_Msg::error("ERROR_OPERATE_1");
                }
                $refundChkParams = array(
                    'id'=> $r,
                    'user_id'=>1,
                    'allow_status'=>1,
                );
                $r = RefundApplyModel::model()->checkApply($refundChkParams);
                if(!$r){
                    $OrderModel->rollback();
                    Lang_Msg::error("ERROR_OPERATE_1");
                }
            }
        } catch(Exception $e) {
            $OrderModel->rollback();
            Log_Base::save('OTA', 'CannelCode: error:'.$e->getMessage());
            Log_Base::save('OTA', 'CannelCode: '.var_export($this->body,true));
            Lang_Msg::error("ERROR_CANCELCODE_5");
        }
        $OrderModel->commit();

        $data = array();
        $data["result"] = 1;
        Lang_Msg::output($data);
    }

}
