<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-1-9
 * Time: 下午2:15
 */

class OrderController extends Base_Controller_Ota
{
    /**检查产品是否可定
     * product_id 是 int 产品ID。
     * date 是 date 想要查询的日期。
     * count 是 int 产品的数量。 //可预定性,0为不可预定,1为可以预定。
     * */
    public function checkAction()
    {
        $params['use_day'] = trim($this->body['date']);
        $params['distributor_id'] = $this->userinfo['distributor_id'];
        $params['price_type'] = 0;
        $params['nums'] = intval($this->body['count']);
        
        $product = ApiOtaModel::model()->productDetail(array(
            'id' => $this->body['product_id']
        ));
        
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'check_product',
            'body' => $this->body,
            'product' => $product,
        ));
        if ($product['code'] == 'fail') {
            Lang_Msg::error($product['message']);
        }
        
        $params['ticket_template_id'] = $product['body']['product_id'];
        
        !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
        (!$params['use_day'] || !Validate::isDateFormat($params['use_day'])) && Lang_Msg::error('ERROR_DATE_2');//游玩日期不能为空，且格式为xxxx-xx-xx
        $params['nums']<1 && Lang_Msg::error('ERROR_TK_NUMS_1');
        $r = ApiOrderModel::model()->check($params);
        
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'check_product',
            'params' => $params,
            'result' => $r,
        ));
        $return = array(
            'product_id'=>$params['ticket_template_id'],
            'date'=>$params['use_day'],
            'count'=>$params['nums'],
            'is_avaliable'=>1,
        );
        
        if ($r['code'] == 'fail') {
            Lang_Msg::error($r['message']);
        }
        
        $ticketInfo = $r['body'];
        if(isset($ticketInfo['day_reserve']) && isset($ticketInfo['remain_reserve'])) {
            if($ticketInfo['remain_reserve']<$params['nums']){
                $return['is_avaliable'] = 0;
            }
        }

        Lang_Msg::output($return);
    }

    /*同步订单
     * product_id 是 int 产品ID。
     * date 是 date 想要查询的日期。
     * count 是 int 数量。
     * price 是 float 单价。
     * payment 是 int 支付方式,0为景点到付,1为商户收款。
     * traveler_name 是 string 游客的姓名。
     * traveler_mobile 是 string 游客的联系电话。
     * identify_card 是 string 游客的身份证号码。
     * */

    public function createAction(){
        try {
            $product = ApiOtaModel::model()->productDetail(array(
                'id' => $this->body['product_id']
            ));

            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'check_product',
                'body' => $this->body,
                'product' => $product,
            ));
            if ($product['code'] == 'fail') {
                Lang_Msg::error($product['message']);
            }

            $params = array();//870AED813F27B305BF81839A9ED4698F
            $params['ticket_template_id'] = $product['body']['product_id']; //732
            $params['source'] = 'ota';
            $params['price_type'] = 0;
            $params['distributor_id'] = $this->userinfo['distributor_id'];
            $params['use_day'] = trim($this->body['date']); //2015-01-11
            $params['nums'] = intval($this->body['count']); //1
            $params['owner_name'] = trim($this->body['traveler_name']);
            $params['owner_mobile'] = trim($this->body['traveler_mobile']);
            $params['owner_card'] = trim($this->body['identify_card']);
            $params['remark'] = 'OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']';
            $params['user_id'] = $this->userinfo['id'];
            $params['user_account'] = $this->userinfo['id'];
            $params['user_name'] = $this->userinfo['name'];
            $params['payment']  = intval($this->body['payment'])?'credit':'offline'; //支付方式,0为景点到付(offline),1为商户收款(credit信用)
            $params['price'] = $this->body['price'];
            $params['ota_type'] = 'ota';
            $params['ota_account'] = $this->userinfo['id'];
            $params['ota_name'] = $this->userinfo['name'];

            !$params['ticket_template_id'] && Lang_Msg::error('ERROR_TKT_1'); //缺少票种ID参数
            !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
            (!$params['use_day'] || !Validate::isDateFormat($params['use_day'])) && Lang_Msg::error('ERROR_DATE_2');//游玩日期不能为空，且格式为xxxx-xx-xx
            $params['nums']<1 && Lang_Msg::error('ERROR_TK_NUMS_1');
            !Validate::isFloat($params['price']) && Lang_Msg::error('缺少价格信息');
            !Validate::isName($params['owner_name']) && Lang_Msg::error('ERROR_OWNER_1');
            !Validate::isMobilePhone($params['owner_mobile']) && Lang_Msg::error('ERROR_OWNER_2');
//            !Validate::isCard($params['owner_card']) && Lang_Msg::error('ERROR_OWNER_3');

            $r = ApiOrderModel::model()->create($params);
            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'create_order',
                'body' => $this->body,
                'params' => $params,
                'result' => $r,
            ));
            isset($r['code']) && $r['code']=='fail' && Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));

            $orderInfo = $r['body'];
            Lang_Msg::output(array(
                'id'=>$orderInfo['id'] ,
                'created_at'=>date("Y-m-d H:i:s",$orderInfo['created_at']))
            );
        } catch(Exception $e) {
            Log_Base::save('OpenApiOrder', 'error:'.$e->getMessage());
            Log_Base::save('OpenApiOrder', var_export($this->body,true));
            Lang_Msg::error( 'ERROR_GLOBAL_3',500 );
        }
    }

    /*修改订单
     *date 是 date 想要查询的日期。
     *count 是 int 产品的数量。
     *traveler_name 是 string 游客的姓名。
     *traveler_mobile 是 string 游客的联系电话。
     *identify_card 是 string 游客的身份证号码。
     * */
    public function updateAction(){
        try {
            $params = array();
            $params['id'] = $this->body['id'];
            $params['user_id'] = $this->userinfo['id'];
            $params['user_account'] = $this->userinfo['id'];
            $params['user_name'] = $this->userinfo['name'];
            //not used for now
           /** isset($this->body['date']) && $params['use_day'] = trim($this->body['date']);
            $params['price_type'] = 0;
            $params['price'] = $this->body['price'];
            $params['ota_type'] = 'ota';
            $params['ota_account'] = $this->userinfo['id'];
            $params['ota_name'] = $this->userinfo['name'];
            isset($this->body['count']) && $params['nums'] = intval($this->body['count']);**/
            isset($this->body['traveler_name']) && $params['owner_name'] = trim(Tools::safeOutput($this->body['traveler_name']));
            isset($this->body['traveler_mobile']) && $params['owner_mobile'] = trim(Tools::safeOutput($this->body['traveler_mobile']));
            isset($this->body['identify_card']) && $params['owner_card'] = trim(Tools::safeOutput($this->body['identify_card']));

            !$params['id'] && Lang_Msg::error('ERROR_ORDER_INFO_1'); //缺少订单ID参数
             //not used for now
            /**(isset($this->body['date']) || isset($this->body['count'])) && (!Validate::isFloat($params['price']) || $params['price']<=0) && Lang_Msg::error('缺少价格信息');
            isset($this->body['date']) && !Validate::isDateFormat($params['use_day']) && Lang_Msg::error('ERROR_DATE_2');//游玩日期不能为空，且格式为xxxx-xx-xx
            isset($this->body['count']) && $params['nums']<1 && Lang_Msg::error('ERROR_TK_NUMS_1');**/
            isset($this->body['traveler_name']) && !Validate::isName($params['owner_name']) && Lang_Msg::error('ERROR_OWNER_1');
            isset($this->body['traveler_mobile']) && !Validate::isMobilePhone($params['owner_mobile']) && Lang_Msg::error('ERROR_OWNER_2');
            isset($this->body['identify_card']) && !Validate::isCard($params['owner_card']) && Lang_Msg::error('ERROR_OWNER_3');
            $r = ApiOrderModel::model()->detail(array('id'=>$params['id']));
            isset($r['code']) && $r['code']=='fail' && Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));
            $detail = $r['body'];
            $detail['status']!='paid' && Lang_Msg::error('订单已使用或过期');

            $r = ApiOrderModel::model()->update($params);
            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'update_order',
                'body' => $this->body,
                'params' => $params,
                'result' => $r,
            ));
            isset($r['code']) && $r['code']=='fail' && Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));
           
            Lang_Msg::output(array( //ticket_order/v1/order/update需同步改orderIterm
                'id'=> $params['id'] ,
                'success' => 'ok'
            ));
        } catch(Exception $e) {
            Log_Base::save('OpenApiOrder', 'error:'.$e->getMessage());
            Log_Base::save('OpenApiOrder', var_export($this->body,true));
            Lang_Msg::error( 'ERROR_GLOBAL_3',500 );
        }
    }

    /**
     * [detail description]
     * id 是 int 想要查询的订单ID。
     * @return [type] [description]
     */
    public function detailAction() {
        $id = $this->body['id'];
        $r = ApiOrderModel::model()->detail(array('id'=>$id));
        isset($r['code']) && $r['code']=='fail' && Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));

        $orderInfo = $r['body'];
        $order_item = isset($orderInfo['order_items']) ? reset($orderInfo['order_items']) : array();
         isset($orderInfo['product_id']) && $order_item['ticket_template_id'] = $orderInfo['product_id'];
         isset($orderInfo['price']) && $order_item['price'] = $orderInfo['price'];
         isset($orderInfo['landscape_ids']) && $order_item['landscape_ids'] = $orderInfo['landscape_ids'];
         isset($orderInfo['use_day']) && $order_item['use_day'] = $orderInfo['use_day'];
         
        $endDate = strtotime("+{$orderInfo['valid']} day", strtotime($order_item['use_day']));
        if($endDate > $orderInfo['expire_end']) $endDate = $orderInfo['expire_end'];
        $endDate = date('Y-m-d', $endDate);
        
        $product = ApiOtaModel::model()->productDetail(array(
            'product_id' => $order_item['ticket_template_id'],
            'agency_id' => $this->userinfo['distributor_id'],
        ));
        Lang_Msg::output($r);
        
        Lang_Msg::output(array( //ticket_order/v1/order/update需同步改orderIterm
            'id'=>$orderInfo['id'] ,
            'payment'=>$orderInfo['payment']=='offline'? 0 : 1 ,
            'product_id'=>$product['body']['id'],
            'scenic_id'=>$order_item['landscape_ids'],
            'count'=>$orderInfo['nums'] ,
            'price' => $order_item['price'] ,
            'date'=>$order_item['use_day'] ,
            'end_date'=>$endDate,
            'traveler_name'=>$orderInfo['owner_name'] ,
            'traveler_mobile'=>$orderInfo['owner_mobile'] ,
            'identify_card'=>$orderInfo['owner_card'] ,
            'created_at'=>date("Y-m-d H:i:s",$orderInfo['created_at']),
            'status'=>$orderInfo['status']=='paid'?1:0, //订单状态,0表示该订单不可用。1表示该订单可用。
        ));
    }

    /**
     * 检测是否可退款
     * @param int $id 订单ID
     * @param int $nums 退票张数
     * @return 
     */
    public function checkRefundAction() {
        $id = intval($this->body['id']); 
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'check_refund',
            'body' => $this->body,
        ));
        if(!$id) {
            Lang_Msg::error('参数错误,错误的订单ID');
        }
        $r = ApiOrderModel::model()->detail(array('id' => $id));
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'check_refund',
            'order_detail' => $r,
        ));

        if(!$r || !$r['code'] || $r['code'] == 'fail') {
            Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));
        }
        $r = $r['body'];
        if(!$r['refund']) {
            Lang_Msg::error('EOOR_API_1', 400, array('error'=>'该订单不允许退款'));
        }
        $refundable_num = $r['nums'] - $r['used_nums'] - $r['refunding_nums'] - $r['refunded_nums']; 
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'check_refund',
            'refunded_nums' => $refundable_num,
        ));
        if($refundable_num <= 0) {
            Lang_Msg::error('EOOR_API_1', 400, array('error'=>'该订单没有可退的票'));
        }
        Lang_Msg::output(array(
            'status' => 'success',
            'id' => $id,
            'refundable_num' => $refundable_num,
        ));
    }

    /**
     * [refundAction description]
     * id 是 int 订单ID。
     * @return [type] [description]
     */
    public function refundApplyAction() {
        try {
            $orderId = $this->body['id'];
            $sign = $this->body['sign'];

            $params['order_id'] = $this->body['id'];
            $params['user_id'] = $this->userinfo['id'];
            $params['nums'] = isset($this->body['nums']) ? intval($this->body['nums']) : $this->body['count'];
            $params['remark'] = 'OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']';
            !$params['order_id'] && Lang_Msg::error('参数错误');
            $r = ApiOrderModel::model()->detail(array('id'=>$params['order_id']));

            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'refund_apply',
                'body' => $this->body,
                'params' => $params,
                'order_detail' => $r,
            ));
            isset($r['code']) && $r['code']=='fail' && Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));
            $orderInfo = $r['body'];

            try {
                $r = ApiOrderModel::model()->refundApply($params);
                Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                    'action' => 'refund_apply',
                    'body' => $this->body,
                    'params' => $params,
                    'result' => $r,
                ));
                if(!$r || !$r['code'] || $r['code'] == 'fail') {
                    Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));
                }
            } catch (Exception $ex) {
                Lang_Msg::error($ex->getMessage());
            }

            $callbackParams = array(
                'id'  => $r['id'],
                'status'     => 'success',
                'refund_num' => $params['nums'],
            );
            Lang_Msg::output($callbackParams);
        } catch(Exception $e) {
            Log_Base::save('OpenApiOrder', 'error:'.$e->getMessage());
            Log_Base::save('OpenApiOrder', var_export($this->body,true));
            Lang_Msg::error( 'ERROR_GLOBAL_3',500 );
        }
    }

    /**
     * 取消订单
     */
    public function cancelAction() {
        $id = $this->body['id'];
        $r = ApiOrderModel::model()->detail(array('id'=>$id));
        isset($r['code']) && $r['code']=='fail' && Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));

        if($r['status'] != 'unpaid') {
            Lang_Msg::error('EOOR_API_1', 400, array('error'=>'该订单状态不是未支付，不能取消'));
        }

        $params = array(
            'id'           => $id,
            'status'       => 'cancel',
            'user_id'      => $this->userinfo['id'],
            'user_account' => $this->userinfo['id'],
            'user_name'    => $this->userinfo['name'],
        );
        try {
            $r = ApiOrderModel::model()->update($params);
            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'cancel_order',
                'body' => $this->body,
                'params' => $params,
                'result' => $r,
            ));
            if(!$r || !$r['code'] || $r['code'] == 'fail') {
                Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));
            }
        } catch (Exception $ex) {
            Lang_Msg::error($ex->getMessage());
        }
        Lang_Msg::output(array(
            'id'     => $id,
            'status' => 'success',
        ));
    }

    public function ticketUsedAction() {
        $params = $this->body;
        if(!array_key_exists('id', $params) || empty($params['id']))  Lang_Msg::error('缺少参数id');

        $r = ApiOrderModel::model()->ticketUsed($params);
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'ticket_used',
            'body' => $this->body,
            'params' => $params,
            'result' => $r,
        ));

        if(isset($r['code']) && $r['code'] == 'succ'){
            Lang_Msg::output($r['body']);
        }else{
            Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));
        }
    }

    /**
     * （重）发入园凭证
     */
    public function sendOrderTicketAction() {
        $orderId = $this->body['id'];
        if (!$orderId) {
            Lang_Msg::error('参数错误');
        }
        
        $r = ApiOrderModel::model()->detail(array('id'=> $orderId));
        if (isset($r['code']) && $r['code']=='fail') {
            Lang_Msg::error('EOOR_API_1', 400, array('error'=>$r['message']));
        }
        $order = $r['body'];
        
        $api_arr = array(
            'id' => $order['id'],
            //'phoneNumber' => $phoneNumber, //目前没有考虑让内部api添加该参数
        );
        $send = ApiOrderModel::model()->sendTicket($api_arr);

        self::echoLog('body', var_export($send, true), 'sendOrderEticket.log');

        if ($send['code'] != 'succ') {
            Lang_Msg::error('重发凭证失败，原因为订单未支付或订单状态不正确');
        } else {
            Lang_Msg::output(array(
                'code' => 200,
                'code_msg' => '发送成功',
            ));
        }
    }
}
