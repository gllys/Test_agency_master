<?php
/**
 * OpenApi 订单控制器
 * @author 崔林 <cuilin@ihuilian.com>
 * @package OpenApi
 * @version 1.1
 */

class OrderController extends Base_Controller_Ota {
    public function checkAction() {
        $required_params = array('product_id','date','count');
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => '参数不完整',
                'result'  => array(),
            ));
        }

        $product = ApiOtaModel::model()->productDetail(array(
            'id' => $this->body['product_id'], 
        ));
        
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action'  => 'check_product',
            'body'    => $this->body,
            'product' => $product,
        ));
        if($product['code'] == 'fail') {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => $product['message'],
                'result'  => array(),
            ));
        }
        if(!isset($product['body']) || !isset($product['body']['product_id'])) {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => '获取相关产品数据失败',
                'result'  => array(),
            ));
        }
        if(!Validate::isDateFormat($this->body['date'])) {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => Lang_Msg::getLang('ERROR_DATE_2'),
                'result'  => array(),
            ));
        }

        $count = intval($this->body['count']);
        if(!$count) {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => Lang_Msg::getLang('ERROR_TK_NUMS_1'),
                'result'  => array(),
            ));
        }

        //0 散客，1 团体 
        $price_type = isset($this->body['price_type']) && intval($this->body['price_type']) == 1 ? 1 : 0;
        $check_params = array(
            'use_day'        => $this->body['date'],
            'distributor_id' => $this->userinfo['distributor_id'],
            'price_type'     => $price_type,
            'nums'           => $count,
            'product_id'     => $product['body']['product_id'],
        );
        $r = ApiOrderModel::model()->check($check_params);
        
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'check_product',
            'params' => $check_params,
            'result' => $r,
        ));
        
        if($r['code'] == 'fail') {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => $r['message'],
                'result'  => array(),
            ));
        }
        $ticketInfo = $r['body'];
        if(isset($ticketInfo['day_reserve']) && isset($ticketInfo['remain_reserve'])) {
            if($ticketInfo['remain_reserve'] < $count){
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => '当前可购买票数为'.$ticketInfo['remain_reserve'].'张，您的请求无法受理',
                    'result'  => array(),
                ));
            }
        }

        $result = array(
            'product_id' => $check_params['product_id'],
            'date'       => $check_params['use_day'],
            'count'      => $check_params['nums'],
        );
        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => $result,
        ));
    }

    /* 创建订单
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
        $required_params = array(
            'product_id','date','count','price','payment','traveler_name','traveler_mobile','identify_card'
        );
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '参数不完整',
                'result' => array(),
            ));
        }
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
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $product['message'],
                    'result' => array(),
                ));
            }
            //0 散客，1 团体，目前1.1只支持散客
            $price_type = isset($this->body['price_type']) && intval($this->body['price_type']) == 1 ? 1 : 0;

            //创建订单前，还是要重新检测下是否可创建订单
            $check_params = array(
                'product_id'     => $product['body']['product_id'],
                'use_day'        => $this->body['date'],
                'nums'           => $this->body['count'],
                'distributor_id' => $this->userinfo['distributor_id'],
                'price_type'     => $price_type,
            );
            $check_result = ApiOrderModel::model()->check($check_params);
            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'check_before_create_order',
                'params' => $check_params,
                'result' => $check_result,
            ));
            if(!isset($check_result['code']) || $check_result['code'] == 'fail') {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => isset($check_result['message']) ? $check_result['message'] : '检测失败，请稍后再试',
                    'result' => array(),
                )); 
            }

            if(!Validate::isDateFormat($this->body['date'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => Lang_Msg::getLang('ERROR_DATE_2'),
                    'result'  => array(),
                ));
            }
            $count = intval($this->body['count']);
            if(!$count) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => Lang_Msg::getLang('ERROR_TK_NUMS_1'),
                    'result'  => array(),
                )); 
            }

            if(!Validate::isFloat($this->body['price'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => '价格格式有误',
                    'result'  => array(),
                )); 
            }
            if(!Validate::isName($this->body['traveler_name'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => Lang_Msg::getLang('ERROR_OWNER_1'),
                    'result'  => array(),
                )); 
            }
            if(!Validate::isMobilePhone($this->body['traveler_mobile'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => Lang_Msg::getLang('ERROR_OWNER_2'),
                    'result'  => array(),
                )); 
            }
            if(!Validate::isCard($this->body['identify_card'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => Lang_Msg::getLang('ERROR_GENCODE_4'),
                    'result'  => array(),
                )); 
            }

            $source = 0;
            //根据token获取source
            $token = $this->body['token'];
            if(!$token) {
                Lang_Msg::output(array(
                    'code' => 400, 
                    'message' => 'token已过期[ERROR_002]',
                    'result' => array(),
                ));
            }
            $user_model = OtaAccountModel::model()->getUserInfoByAttributes(array('token' => $token));
            if(!$user_model || !$user_model['source']) {
                Lang_Msg::output(array(
                    'code' => 400, 
                    'message' => '无法获取该用户信息',
                    'result' => array(),
                ));
            }
            $source = $user_model['source'];

            $payment = $this->body['payment'] == 0 ? 'credit' : 'offline';
            $params = array(
                'product_id'     => $product['body']['product_id'],
                'source'         => $source,
                'local_source'   => 1, //1 OpenApi
                'source_token'   => $token,
                'price_type'     => $price_type,
                'distributor_id' => $this->userinfo['distributor_id'],
                'use_day'        => $this->body['date'],
                'nums'           => $count,
                'owner_name'     => $this->body['traveler_name'],
                'owner_mobile'   => $this->body['traveler_mobile'],
                'owner_card'     => $this->body['identify_card'],
                'remark'         => 'OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']',
                'user_id'        => $this->userinfo['id'],
                'user_account'   => $this->userinfo['id'],
                'user_name'      => $this->userinfo['name'],
                'payment'        => $payment,
                'price'          => $this->body['price'],
                'ota_type'       => 'ota',
                'ota_account'    => $this->userinfo['id'],
                'ota_name'       => $this->userinfo['name'],
            );

            $r = ApiOrderModel::model()->create($params);
            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'create_order',
                'body'   => $this->body,
                'params' => $params,
                'result' => $r,
            ));
            if(!isset($r['code'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => '订单创建失败',
                    'result'  => array(),
                )); 
            }
            if($r['code'] == 'fail') {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => $r['message'],
                    'result'  => array(),
                )); 
            }

            $orderInfo = $r['body'];
            Lang_Msg::output(array(
                'code' => 200,
                'message' => '',
                'result' => array(
                    'id'=>$orderInfo['id'] ,
                    'created_at'=>date("Y-m-d H:i:s",$orderInfo['created_at'])
                ),
            ));
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
        $required_params = array(
            'id'
        );
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '参数不完整',
                'result' => array(),
            ));
        }
        try {
            $params = array();
            $params['id'] = $this->body['id'];
            $params['user_id'] = $this->userinfo['id'];
            $params['user_account'] = $this->userinfo['id'];
            $params['user_name'] = $this->userinfo['name'];
            isset($this->body['traveler_name']) && $params['owner_name'] = trim(Tools::safeOutput($this->body['traveler_name']));
            isset($this->body['traveler_mobile']) && $params['owner_mobile'] = trim(Tools::safeOutput($this->body['traveler_mobile']));
            isset($this->body['identify_card']) && $params['owner_card'] = trim(Tools::safeOutput($this->body['identify_card']));

             //not used for now
            /**(isset($this->body['date']) || isset($this->body['count'])) && (!Validate::isFloat($params['price']) || $params['price']<=0) && Lang_Msg::error('缺少价格信息');
            isset($this->body['date']) && !Validate::isDateFormat($params['use_day']) && Lang_Msg::error('ERROR_DATE_2');//游玩日期不能为空，且格式为xxxx-xx-xx
            isset($this->body['count']) && $params['nums']<1 && Lang_Msg::error('ERROR_TK_NUMS_1');**/
            if(isset($this->body['traveler_name']) && !Validate::isName($params['owner_name'])) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => Lang_Msg::getLang('ERROR_OWNER_1'),
                    'result' => array(),
                ));
            }
            if(isset($this->body['traveler_mobile']) && !Validate::isMobilePhone($params['owner_mobile'])) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => Lang_Msg::getLang('ERROR_OWNER_2'),
                    'result' => array(),
                ));
            }
            if(isset($this->body['identify_card']) && !Validate::isCard($params['owner_card'])) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => Lang_Msg::getLang('ERROR_GENCODE_4'),
                    'result' => array(),
                ));
            }
            $r = ApiOrderModel::model()->detail(array('id'=>$params['id']));
            if(!isset($r['code'])) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => '订单更新失败_1',
                    'result' => array(),
                )); 
            }
            if($r['code'] == 'fail') {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $r['message'],
                    'result' => array(),
                )); 
            }
            $detail = $r['body'];
            if($detail['status'] != 'paid') {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => '订单已使用或过期',
                    'result' => array(),
                ));
            }

            $r = ApiOrderModel::model()->update($params);
            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'update_order',
                'body' => $this->body,
                'params' => $params,
                'result' => $r,
            ));
            if(!isset($r['code'])) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => '订单更新失败_2',
                    'result' => array(),
                )); 
            }
            if($r['code'] == 'fail') {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $r['message'],
                    'result' => array(),
                )); 
            }
           
            Lang_Msg::output(array( //ticket_order/v1/order/update需同步改orderIterm
                'code' => 200,
                'message' => '',
                'result' => array(
                    'id' => $r['body']['id'],
                ),
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
        $required_params = array(
            'id'
        );
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '参数不完整',
                'result' => array(),
            ));
        }
        $id = $this->body['id'];
        $r = ApiOrderModel::model()->detail(array('id'=>$id));
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'order_detail',
            'body' => $this->body,
            'result' => $r,
        ));
        if(!isset($r['code'])) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '订单查询失败',
                'result' => array(),
            )); 
        }
        if($r['code'] == 'fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $r['message'],
                'result' => array(),
            )); 
        }

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
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'order_detail',
            'product' => $product,
        ));
        
        $result = array( 
            'id'              => $orderInfo['id'] ,
            'payment'         => $orderInfo['payment']=='offline'? 0 : 1 ,
            'product_id'      => $product['body']['id'],
            'scenic_id'       => $order_item['landscape_ids'],
            'count'           => $orderInfo['nums'] ,
            'used_count'      => $orderInfo['used_nums'],
            'refunding_count' => $orderInfo['refunding_nums'],
            'refunded_count'  => $orderInfo['refunded_nums'],
            'price'           => $order_item['price'] ,
            'date'            => $order_item['use_day'] ,
            'end_date'        => $endDate,
            'traveler_name'   => $orderInfo['owner_name'] ,
            'traveler_mobile' => $orderInfo['owner_mobile'] ,
            'identify_card'   => $orderInfo['owner_card'] ,
            'created_at'      => date("Y-m-d H:i:s",$orderInfo['created_at']),
            'status'          => $orderInfo['status']=='paid'?1:0, //订单状态,0表示该订单不可用。1表示该订单可用。
            'refund'          => $orderInfo['refund'],
        );
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'order_detail',
            'result' => $result,
        ));
        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => $result,
        ));
    }

    /**
     * 检测是否可退款
     * @param int $id 订单ID
     * @param int $nums 退票张数
     * @return 
     */
    public function checkRefundAction() {
        $required_params = array(
            'id'
        );
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '参数不完整',
                'result' => array(),
            ));
        }
        $id = intval($this->body['id']); 
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'check_refund',
            'body' => $this->body,
        ));

        $check_result = $this->_check_refund($id,'check_refund');
        if($check_result['status']) {
            $output = array(
                'code' => 200,
                'message' => '',
                'result' => array(
                    'count' => $check_result['data']['refundable_num'],
                ),
            );
        } else {
            $output = array(
                'code' => 400,
                'message' => $check_result['message'],
                'result' => array(),
            );
        }
        Lang_Msg::output($output);
    }

    /**
     * [refundAction description]
     * id 是 int 订单ID。
     * @return [type] [description]
     */
    public function refundApplyAction() {
        $required_params = array(
            'id'
        );
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '参数不完整',
                'result' => array(),
            ));
        }
        try {
            $id = intval($this->body['id']);
            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'refund_apply',
                'body' => $this->body,
            ));

            $check_result = $this->_check_refund($id,'refund_apply');
            if(!$check_result['status']) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $check_result['message'],
                    'result' => array(),
                ));
            }
            $refundable_num = $check_result['data']['refundable_num']; //可退款的票数
            $count = 0;
            if(!isset($this->body['count'])) {
                $count = $refundable_num;
            } else {
                $count = intval($this->body['count']);
                if(!$count) {
                    $count = $refundable_num;
                }
            }
            if($count > $refundable_num) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => '退款票数超过了可退票数',
                    'result' => array(),
                ));
            }

            $params['order_id'] = $this->body['id'];
            $params['user_id'] = $this->userinfo['id'];
            $params['nums'] = $count;
            $params['remark'] = 'OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']';

            try {
                $r = ApiOrderModel::model()->refundApply($params);
                Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                    'action' => 'refund_apply',
                    'body' => $this->body,
                    'params' => $params,
                    'result' => $r,
                ));
                if(!$r || !$r['code'] || $r['code'] == 'fail') {
                    Lang_Msg::output(array(
                        'code' => 400,
                        'message' => $r['message'],
                        'result' => array(),
                    ));
                }
            } catch (Exception $ex) {
                Lang_Msg::error($ex->getMessage());
            }

            Lang_Msg::output(array(
                'code' => 200,
                'message' => '',
                'result' => array(
                    'id' => $r['body']['id'],
                ),
            ));
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
        $required_params = array(
            'id'
        );
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '参数不完整',
                'result' => array(),
            ));
        }
        $id = $this->body['id'];
        $r = ApiOrderModel::model()->detail(array('id'=>$id));
        if(isset($r['code']) && $r['code']=='fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $r['message'],
                'result' => array(),
            ));
        }

        if($r['status'] != 'unpaid') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '该订单状态不是未支付，不能取消',
                'result' => array(),
            ));
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
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $r['message'],
                    'result' => array(),
                ));
            }
        } catch (Exception $ex) {
            Lang_Msg::error($ex->getMessage());
        }
        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => array(
                'id' => $id,
            ),
        ));
    }

    /**
     * （重）发入园凭证
     */
    public function sendOrderTicketAction() {
        $required_params = array(
            'id'
        );
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '参数不完整',
                'result' => array(),
            ));
        }
        $orderId = $this->body['id'];
        
        $r = ApiOrderModel::model()->detail(array('id'=> $orderId));
        if (isset($r['code']) && $r['code']=='fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $r['message'],
                'result' => array(),
            ));
        }
        $order = $r['body'];
        
        $api_arr = array(
            'id' => $order['id'],
            //'phoneNumber' => $phoneNumber, //目前没有考虑让内部api添加该参数
        );
        $send = ApiOrderModel::model()->sendTicket($api_arr);

        self::echoLog('body', var_export($send, true), 'sendOrderEticket.log');

        if ($send['code'] != 'succ') {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => '重发凭证失败，原因为订单未支付或订单状态不正确',
                'result'  => array(),
            ));
        } else {
            Lang_Msg::output(array(
                'code' => 200,
                'message' => '',
                'result' => array(),
            ));
        }
    }

    /**
     * 检测是否可退款
     * @param int $order_id 
     * @param string $action 操作名，用于记录日志。
     * @return array 
     */
    private function _check_refund($order_id,$action = 'check_refund') {
        $r = ApiOrderModel::model()->detail(array('id' => $order_id));
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => $action,
            'order_detail' => $r,
        ));

        if(!$r || !$r['code'] || $r['code'] == 'fail') {
            return array('status' => false, 'message' => $r['message']);
        }
        $r = $r['body'];
        if(!$r['refund']) {
            return array('status' => false, 'message' => '该订单不允许退款');
        }
        $refundable_num = $r['nums'] - $r['used_nums'] - $r['refunding_nums'] - $r['refunded_nums']; 
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => $action,
            'refunded_nums' => $refundable_num,
        ));
        if($refundable_num <= 0) {
            return array('status' => false, 'message' => '该订单没有可退的票');
        }
    
        $data = array(
            'refundable_num' => $refundable_num,
        );
        return array('status' => true, 'data' => $data);
    }
}
