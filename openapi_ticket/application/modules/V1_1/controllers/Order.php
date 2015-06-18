<?php
/**
 * OpenApi 订单控制器
 * @author 崔林 <cuilin@ihuilian.com>
 * @package OpenApi
 * @version 1.1
 */

class OrderController extends Base_Controller_OtaNew {
    public function checkAction() {
        $required_params = array('product_id','date','count');
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => '参数不完整',
                'result'  => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }

        $product = ApiOtaModel::model()->productDetail(array(
            'id' => $this->body['product_id'], 
        ));
        
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action'  => 'check_product',
            'body'    => $this->body,
            'product' => $product,
        ),'','检测是否可创建订单-获取产品详情',$this->body['product_id']);
        if($product['code'] == 'fail') {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => $product['message'],
                'result'  => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }
        if(!isset($product['body']) || !isset($product['body']['product_id'])) {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => '获取相关产品数据失败',
                'result'  => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }
        if(!Validate::isDateFormat($this->body['date'])) {
            //日期格式必须是xxxx-xx-xx
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => Lang_Msg::getLang('ERROR_DATE_2'),
                'result'  => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }

        $count = intval($this->body['count']);
        if(!$count) {
            //订购票数不能少于1
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => Lang_Msg::getLang('ERROR_TK_NUMS_1'),
                'result'  => array(),
            ),200,JSON_UNESCAPED_UNICODE);
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
        ),'','检测是否可创建订单-检测',$product['body']['product_id']);
        
        if($r['code'] == 'fail') {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => $r['message'],
                'result'  => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }
        $ticketInfo = $r['body'];
        if(isset($ticketInfo['day_reserve']) && isset($ticketInfo['remain_reserve'])) {
            if($ticketInfo['remain_reserve'] < $count){
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => '当前可购买票数为'.$ticketInfo['remain_reserve'].'张，您的请求无法受理',
                    'result'  => array(),
                ),200,JSON_UNESCAPED_UNICODE);
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
        ),200,JSON_UNESCAPED_UNICODE);
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
            ),200,JSON_UNESCAPED_UNICODE);
        }
        try {
            $product = ApiOtaModel::model()->productDetail(array(
                'id' => $this->body['product_id']
            ));

            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'check_product',
                'body' => $this->body,
                'product' => $product,
            ),'','创建订单-获取产品详情',$this->body['product_id']);
            if ($product['code'] == 'fail') {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $product['message'],
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE);
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
            ),'','创建订单-检测是否可创建订单',$product['body']['product_id']);
            if(!isset($check_result['code']) || $check_result['code'] == 'fail') {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => isset($check_result['message']) ? $check_result['message'] : '检测失败，请稍后再试',
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }

            if(!Validate::isFloat($this->body['price'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => '价格格式有误',
                    'result'  => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }
            if($this->body['price'] != $product['body']['price']) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => '所给价格与该产品价格不符，无法购买',
                    'result'  => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }


            if(!Validate::isDateFormat($this->body['date'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => Lang_Msg::getLang('ERROR_DATE_2'),
                    'result'  => array(),
                ),200,JSON_UNESCAPED_UNICODE);
            }
            $count = intval($this->body['count']);
            if(!$count) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => Lang_Msg::getLang('ERROR_TK_NUMS_1'),
                    'result'  => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }
            if(!Validate::isName($this->body['traveler_name'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => Lang_Msg::getLang('ERROR_OWNER_1'),
                    'result'  => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }
            if(!Validate::isMobilePhone($this->body['traveler_mobile'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => Lang_Msg::getLang('ERROR_OWNER_2'),
                    'result'  => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }
            if(!Validate::isCard($this->body['identify_card'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => Lang_Msg::getLang('ERROR_GENCODE_4'),
                    'result'  => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }

            $source = 0;
            //根据token获取source
            $token = $this->body['token'];
            if(!$token) {
                Lang_Msg::output(array(
                    'code' => 400, 
                    'message' => 'token已过期[ERROR_002]',
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE);
            }
            $user_model = OtaAccountModel::model()->getUserInfoByAttributes(array('token' => $token));
            if(!$user_model || !$user_model['source']) {
                Lang_Msg::output(array(
                    'code' => 400, 
                    'message' => '无法获取该用户信息',
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE);
            }
            $source = $user_model['source'];

            $payment_array = array(
                2 => 'credit',
                3 => 'advance',
                4 => 'union',
            );
            $payment = isset($this->body['payment']) ? $this->body['payment'] : 2;
            $payment = isset($payment_array[$payment]) ? $payment_array[$payment] : 'credit';

            $source_id = isset($this->body['ota_order_id']) ? $this->body['ota_order_id'] : '';
            //有ota_order_id时，判断该订单号是否存在
            if($source_id) {
                $order_detail_params = array(
                    'source' => $user_model['source'],
                    'source_id' => $source_id,
                );
                $order_info = ApiOrderModel::model()->detail($order_detail_params);    
                Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                    'action' => 'check_otaorder_by_sourceid',
                    'params' => $order_detail_params,
                    'result' => $order_info,
                ),'','创建订单-检测OTA订单号是否存在',$source_id);
                if($order_info['code'] == 'succ') {
                    Lang_Msg::output(array(
                        'code' => 400, 
                        'message' => '该OTA订单id已存在',
                        'result' => array(),
                    ),200,JSON_UNESCAPED_UNICODE);
                }
            }
            $is_sms = isset($this->body['send_sms']) && $this->body['send_sms'] == 0 ? 0 : 1;

            $params = array(
                'product_id'     => $product['body']['product_id'],
                'source_id'      => $source_id,
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
                'ota_type'       => 'ota',
                'ota_account'    => $this->userinfo['id'],
                'ota_name'       => $this->userinfo['name'],
                'is_sms'         => $is_sms,   
            );

            $r = ApiOrderModel::model()->create($params);
            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'create_order',
                'body'   => $this->body,
                'params' => $params,
                'result' => $r,
            ),'','创建订单-创建',$product['body']['product_id']);
            if(!isset($r['code'])) {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => '订单创建失败',
                    'result'  => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }
            if($r['code'] == 'fail') {
                Lang_Msg::output(array(
                    'code'    => 400,
                    'message' => $r['message'],
                    'result'  => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }

            $orderInfo = $r['body'];
            Lang_Msg::output(array(
                'code' => 200,
                'message' => '',
                'result' => array(
                    'id'=>$orderInfo['id'] ,
                    'created_at'=>date("Y-m-d H:i:s",$orderInfo['created_at'])
                ),
            ),200,JSON_UNESCAPED_UNICODE);
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
            ),200,JSON_UNESCAPED_UNICODE);
        }
        try {
            $params = array();
            $id = $this->body['id'];
            if(isset($this->body['id_type']) && $this->body['id_type'] == 1) {
                $get_result = $this->_get_order_id_by_source_id($id);
                if(!$get_result['status']) {
                    Lang_Msg::output(array(
                        'code' => 400,
                        'message' => $get_result['message'],
                        'result' => array()
                    ),200,JSON_UNESCAPED_UNICODE);
                }
                $id = $get_result['data'];
            }
            $params['id'] = $id;
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
                ),200,JSON_UNESCAPED_UNICODE);
            }
            if(isset($this->body['traveler_mobile']) && !Validate::isMobilePhone($params['owner_mobile'])) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => Lang_Msg::getLang('ERROR_OWNER_2'),
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE);
            }
            if(isset($this->body['identify_card']) && !Validate::isCard($params['owner_card'])) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => Lang_Msg::getLang('ERROR_GENCODE_4'),
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE);
            }
            $r = ApiOrderModel::model()->detail(array('id'=>$params['id']));
            if(!isset($r['code'])) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => '订单更新失败_1',
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }
            if($r['code'] == 'fail') {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $r['message'],
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }
            $detail = $r['body'];
            if($detail['status'] != 'paid') {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => '订单已使用或过期',
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE);
            }

            $r = ApiOrderModel::model()->update($params);
            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'update_order',
                'body' => $this->body,
                'params' => $params,
                'result' => $r,
            ),'','更新订单-更新操作',$this->body['id']);
            if(!isset($r['code'])) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => '订单更新失败_2',
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }
            if($r['code'] == 'fail') {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $r['message'],
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE); 
            }
           
            Lang_Msg::output(array( //ticket_order/v1/order/update需同步改orderIterm
                'code' => 200,
                'message' => '',
                'result' => array(
                    'id' => $r['body']['id'],
                ),
            ),200,JSON_UNESCAPED_UNICODE);
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
            ),200,JSON_UNESCAPED_UNICODE);
        }
        $id = $this->body['id'];
        if(isset($this->body['id_type']) && $this->body['id_type'] == 1) {
            $get_result = $this->_get_order_id_by_source_id($id);
            if(!$get_result['status']) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $get_result['message'],
                    'result' => array()
                ),200,JSON_UNESCAPED_UNICODE);
            }
            $id = $get_result['data'];
        }
        $r = ApiOrderModel::model()->detail(array('id'=>$id));
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'order_detail',
            'body' => $this->body,
            'result' => $r,
        ),'','获取订单详情-获取操作',$id);
        if(!isset($r['code'])) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '订单查询失败',
                'result' => array(),
            ),200,JSON_UNESCAPED_UNICODE); 
        }
        if($r['code'] == 'fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $r['message'],
                'result' => array(),
            ),200,JSON_UNESCAPED_UNICODE); 
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
        ),'','获取订单详情-获取产品详情',$order_item['ticket_template_id']);
        
        $result = array( 
            'id'              => $orderInfo['id'] ,
            'ota_order_id'    => isset($orderInfo['source_id']) ? $orderInfo['source_id'] : '',
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
        ),'','获取订单详情-返回数据',$orderInfo['id']);
        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => $result,
        ),200,JSON_UNESCAPED_UNICODE);
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
            ),200,JSON_UNESCAPED_UNICODE);
        }
        $id = $this->body['id'];
        if(isset($this->body['id_type']) && $this->body['id_type'] == 1) {
            $get_result = $this->_get_order_id_by_source_id($id);
            if(!$get_result['status']) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $get_result['message'],
                    'result' => array()
                ),200,JSON_UNESCAPED_UNICODE);
            }
            $id = $get_result['data'];
        }
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'check_refund',
            'body' => $this->body,
        ),'','检测是否可退款-获取参数',$id);

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
        Lang_Msg::output($output,200,JSON_UNESCAPED_UNICODE);
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
            ),200,JSON_UNESCAPED_UNICODE);
        }
        try {
            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'refund_apply',
                'body' => $this->body,
            ),'','申请退款-接收参数',$this->body['id']);
            $id = $this->body['id'];
            if(isset($this->body['id_type']) && $this->body['id_type'] == 1) {
                $get_result = $this->_get_order_id_by_source_id($id);
                if(!$get_result['status']) {
                    Lang_Msg::output(array(
                        'code' => 400,
                        'message' => $get_result['message'],
                        'result' => array()
                    ),200,JSON_UNESCAPED_UNICODE);
                }
                $id = $get_result['data'];
            }

            $check_result = $this->_check_refund($id,'refund_apply');
            if(!$check_result['status']) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $check_result['message'],
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE);
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
                ),200,JSON_UNESCAPED_UNICODE);
            }

            $params['order_id'] = $this->body['id'];
            $params['user_id'] = $this->userinfo['id'];
            $params['nums'] = $count;
            if(isset($this->body['remark']) && $this->body['remark']) {
                $remark = $this->body['reamrk'];
            } else {
                $remark = 'OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']';
            }
            $params['remark'] = $remark;

            try {
                $r = ApiOrderModel::model()->refundApply($params);
                Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                    'action' => 'refund_apply',
                    'body' => $this->body,
                    'params' => $params,
                    'result' => $r,
                ),'','申请退款-申请操作',$this->body['id']);
                if(!$r || !$r['code'] || $r['code'] == 'fail') {
                    Lang_Msg::output(array(
                        'code' => 400,
                        'message' => $r['message'],
                        'result' => array(),
                    ),200,JSON_UNESCAPED_UNICODE);
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
            ),200,JSON_UNESCAPED_UNICODE);
        } catch(Exception $e) {
            Log_Base::save('OpenApiOrder', 'error:'.$e->getMessage());
            Log_Base::save('OpenApiOrder', var_export($this->body,true));
            Lang_Msg::error( 'ERROR_GLOBAL_3',500 );
        }
    }

    /**
     * 不经过票台审核，直接退款
     */
    public function directRefundAction() {
        $required_params = array(
            'id'
        );
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '参数不完整',
                'result' => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }
        try {
            Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                'action' => 'direct_refund',
                'body' => $this->body,
            ),'','直接退款-接收参数',$this->body['id']);
            $id = $this->body['id'];
            if(isset($this->body['id_type']) && $this->body['id_type'] == 1) {
                $get_result = $this->_get_order_id_by_source_id($id);
                if(!$get_result['status']) {
                    Lang_Msg::output(array(
                        'code' => 400,
                        'message' => $get_result['message'],
                        'result' => array()
                    ),200,JSON_UNESCAPED_UNICODE);
                }
                $id = $get_result['data'];
            }

            $check_result = $this->_check_refund($id,'direct_refund');
            if(!$check_result['status']) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $check_result['message'],
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE);
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
                ),200,JSON_UNESCAPED_UNICODE);
            }

            $params['order_id'] = $this->body['id'];
            $params['user_id'] = $this->userinfo['id'];
            $params['nums'] = $count;
            if(isset($this->body['remark']) && $this->body['remark']) {
                $remark = $this->body['reamrk'];
            } else {
                $remark = 'OTA客户：'.$this->userinfo['name'].'[ID:'.$this->userinfo['id'].']';
            }
            $params['remark'] = $remark;
            $params['user_account'] = $params['user_name'] = $this->userinfo['name']; 

            try {
                $r = ApiOrderModel::model()->directRefund($params);
                Util_Logger::getLogger('openapi')->info(__METHOD__, array(
                    'action' => 'direct_refund',
                    'body' => $this->body,
                    'params' => $params,
                    'result' => $r,
                ),'','直接退款-退款操作',$this->body['id']);
                if(!$r || !$r['code'] || $r['code'] == 'fail') {
                    Lang_Msg::output(array(
                        'code' => 400,
                        'message' => $r['message'],
                        'result' => array(),
                    ),200,JSON_UNESCAPED_UNICODE);
                }
            } catch (Exception $ex) {
                Lang_Msg::error($ex->getMessage());
            }

            Lang_Msg::output(array(
                'code' => 200,
                'message' => '',
                'result' => array(
                    'id' => $r['body']['id'],
                    'count' => $count,
                ),
            ),200,JSON_UNESCAPED_UNICODE);
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
            ),200,JSON_UNESCAPED_UNICODE);
        }
        $id = $this->body['id'];
        if(isset($this->body['id_type']) && $this->body['id_type'] == 1) {
            $get_result = $this->_get_order_id_by_source_id($id);
            if(!$get_result['status']) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $get_result['message'],
                    'result' => array()
                ),200,JSON_UNESCAPED_UNICODE);
            }
            $id = $get_result['data'];
        }
        $r = ApiOrderModel::model()->detail(array('id'=>$id));
        Util_Logger::getLogger('openapi')->info(__METHOD__, array(
            'action' => 'cancel_order',
            'body' => $this->body,
            'r' => $r,
        ),'','取消订单-获取订单详情',$this->body['id']);
        if(isset($r['code']) && $r['code']=='fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $r['message'],
                'result' => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }

        if($r['status'] != 'unpaid') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '该订单状态不是未支付，不能取消',
                'result' => array(),
            ),200,JSON_UNESCAPED_UNICODE);
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
            ),'取消订单-取消',$this->boddy['id']);
            if(!$r || !$r['code'] || $r['code'] == 'fail') {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $r['message'],
                    'result' => array(),
                ),200,JSON_UNESCAPED_UNICODE);
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
        ),200,JSON_UNESCAPED_UNICODE);
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
            ),200,JSON_UNESCAPED_UNICODE);
        }
        
        $id = $this->body['id'];
        if(isset($this->body['id_type']) && $this->body['id_type'] == 1) {
            $get_result = $this->_get_order_id_by_source_id($id);
            if(!$get_result['status']) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $get_result['message'],
                    'result' => array()
                ),200,JSON_UNESCAPED_UNICODE);
            }
            $id = $get_result['data'];
        }
        $r = ApiOrderModel::model()->detail(array('id'=> $id));
        if (isset($r['code']) && $r['code']=='fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $r['message'],
                'result' => array(),
            ),200,JSON_UNESCAPED_UNICODE);
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
            ),200,JSON_UNESCAPED_UNICODE);
        } else {
            Lang_Msg::output(array(
                'code' => 200,
                'message' => '',
                'result' => array(),
            ));
        }
    }

    /**
     * 退款查询
     * @param int $refund_id
     * @return array $refund_detail
     */
    public function refundDetailAction() {
        $required_params = array('id');
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => '参数不完整',
                'result'  => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }

        $id = $this->body['id'];
        $r = ApiOrderModel::model()->refundDetail(array('id' => $id));
        Util_Logger::getLogger('openapi')->info(__METHOD__,array(
            'action' => 'refund_detail',
            'params' => $this->body,
            'result' => $r,
        ),'','获取退款信息-获取',$this->body['id']);
        if($r['code'] == 'fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $r['message'],
                'result' => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }

        $data = isset($r['body']['data'][$id]) ? $r['body']['data'][$id] : '';
        if(!$data) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '无相关退款记录',
                'result' => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }
        $payment_array = array(
            2 => 'credit',
            3 => 'advance',
            4 => 'union',
        );
        $payment = isset($data['pay_type']) ? $data['pay_type'] : 'credit';
        $payment = isset($payment_array[$payment]) && array_search($payment,$payment_array) ? array_search($payment,$payment_array) : 2;
        $result = array(
           'id'            => $data['id'],
           'order_id'      => $data['order_id'],
           'remark'        => $data['remark'],
           'product_name'  => $data['name'],
           'refund_money'  => $data['money'],
           'count'         => $data['count'],
           'reject_reason' => $data['reject_reason'],
           'payment'       => $payment,
           'status'        => $data['status'],
           'created_at'    => date('Y-m-d H:i:s',$data['created_at']),
        );

        Util_Logger::getLogger('openapi')->info(__METHOD__,array(
            'action' => 'refund_detail',
            'result' => $result,
        ),'','获取退款信息-返回退款信息',$this->body['id']);
        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => $result,
        ),200,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 核销查询
     * @param int $order_id
     * @return array $consume_detail
     */
    public function consumeDetailAction() {
        $required_params = array('id');
        if(!Util_Common::checkParams($this->body,$required_params)) {
            Lang_Msg::output(array(
                'code'    => 400,
                'message' => '参数不完整',
                'result'  => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }

        $id = $this->body['id'];
        if(isset($this->body['id_type']) && $this->body['id_type'] == 1) {
            $get_result = $this->_get_order_id_by_source_id($id);
            if(!$get_result['status']) {
                Lang_Msg::output(array(
                    'code' => 400,
                    'message' => $get_result['message'],
                    'result' => array()
                ),200,JSON_UNESCAPED_UNICODE);
            }
            $id = $get_result['data'];
        }
        $order_detail = ApiOrderModel::model()->detail(array('id' => $id));
        if($order_detail['code'] == 'fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $order_detail['message'],
                'result' => array()
            ),200,JSON_UNESCAPED_UNICODE);
        }
        $r = ApiOrderModel::model()->consumeInfo(array('order_id' => $id));
        Util_Logger::getLogger('openapi')->info(__METHOD__,array(
            'action' => 'consume_info',
            'params' => $this->body,
            'result' => $r,
        ),'','获取核销信息-获取',$this->body['id']);
        if($r['code'] == 'fail') {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => $r['message'],
                'result' => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }

        if(!isset($r['body']['total_nums'])) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => '无法获取核销数据',
                'result' => array(),
            ),200,JSON_UNESCAPED_UNICODE);
        }
        $consumed_count = $r['body']['total_nums'];

        $result = array(
            'order_id'       => $id,
            'ota_order_id'   => $order_detail['source_id'],
            'count'          => $order_detail['nums'],
            'consumed_count' => $consumed_count,
        );
        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => $result,
        ),200,JSON_UNESCAPED_UNICODE);
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
        ),'','检测是否可退款-获取订单详情',$order_id);

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
        ),'','检测是否可退款-计算可退款票数',$id);
        if($refundable_num <= 0) {
            return array('status' => false, 'message' => '该订单没有可退的票');
        }
    
        $data = array(
            'refundable_num' => $refundable_num,
        );
        return array('status' => true, 'data' => $data);
    }

    private function _get_user_source() {
        if(!$this->body['token']) {
            return array('status' => false, 'message' => 'token已过期[ERROR_002]');
        }
        $user_model = OtaAccountModel::model()->getUserInfoByAttributes(array('token' => $this->body['token']));
        if(!$user_model || !$user_model['source']) {
            return array('status' => false, 'message' => '无法获取用户信息');
        }

        return $user_model['source'];
    }

    /**
     * 根据source_id获取订单id
     * @params string $source_id
     * @return int $id
     */
    private function _get_order_id_by_source_id($source_id) {
        $source = $this->_get_user_source();
        $order_list = ApiOrderModel::model()->lists(array(
            'source_id' => $source_id,
            'source' => $source,
        ));
        if($order_list['code'] == 'fail') {
            return array('status' => false, 'message' => $order_list['message']);
        }
        $data = $order_list['body']['data'];
        if(!$data || !isset($data[0]) || !$data[0]['id']) {
            return array('status' => false, 'message' => '无相关订单信息');
        }

        return array('status' => true, 'data' => $data[0]['id']);
    }

    /**
     * 根据订单id获取source_id
     * @param int $order_id
     * @return unknown $source_id
     */
    private function _get_source_id_by_order_id($order_id) {
        $order_list = ApiOrderModel::model()->lists(array(
            'id' => $order_id,
        ));
        if($order_list['code'] == 'fail') {
            return array('status' => false, 'message' => $order_list['message']);
        }
        $data = $order_list['body']['data'];
        if(!$data || !isset($data[0])) {
            return array('status' => false, 'message' => '无相关订单信息');
        }

        return array('status' => true, 'data' => $data[0]['source_id']);
    }
}
