<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-9
 * Time: 下午2:45
 */

class V1Controller extends Base_Controller_ApiDispatch{
    const AGENCY_ID = 147;
    var $ERROR_MAP = array();
    private $service = null;
      
    public function init(){
        parent::init(false);
        $this->initErrorMap();

        self::echoLog('body', var_export($this->body, true), 'qunar_bee.log');
    }

    /**
     * 接口入口方法
     */
    public function restAction(){

//        $this->body = array (
//            'method' => 'getProductByQunar',
//            'requestParam' => '{\\"data\\":\\"PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/Pg0KPHJlcXVlc3QgeG1sbnM9Imh0dHA6Ly9waWFvLnF1bmFyLmNvbS8yMDEzL1FNZW5waWFvUmVxdWVzdFNjaGVtYSI+DQo8aGVhZGVyPg0KPGFwcGxpY2F0aW9uPg0KUXVuYXIuTWVucGlhby5BZ2VudDwvYXBwbGljYXRpb24+DQo8cHJvY2Vzc29yPg0KU3VwcGxpZXJEYXRhRXhjaGFuZ2VQcm9jZXNzb3I8L3Byb2Nlc3Nvcj4NCjx2ZXJzaW9uPg0KdjIuMC4wPC92ZXJzaW9uPg0KPGJvZHlUeXBlPg0KR2V0UHJvZHVjdEJ5UXVuYXJSZXF1ZXN0Qm9keTwvYm9keVR5cGU+DQo8Y3JlYXRlVXNlcj4NClF1bmFyLk1lbnBpYW8uQWdlbnQ8L2NyZWF0ZVVzZXI+DQo8Y3JlYXRlVGltZT4NCjIwMTUtMDMtMTUgMTE6MTg6MjA8L2NyZWF0ZVRpbWU+DQo8c3VwcGxpZXJJZGVudGl0eT4NCk1FSUpJTkdURVNUMjwvc3VwcGxpZXJJZGVudGl0eT4NCjwvaGVhZGVyPg0KPGJvZHkgeHNpOnR5cGU9IkdldFByb2R1Y3RCeVF1bmFyUmVxdWVzdEJvZHkiIHhtbG5zOnhzaT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS9YTUxTY2hlbWEtaW5zdGFuY2UiPg0KPG1ldGhvZD4NClNJTkdMRTwvbWV0aG9kPg0KPGN1cnJlbnRQYWdlPg0KPC9jdXJyZW50UGFnZT4NCjxwYWdlU2l6ZT4NCjwvcGFnZVNpemU+DQo8cmVzb3VyY2VJZD4NCjkzNTwvcmVzb3VyY2VJZD4NCjwvYm9keT4NCjwvcmVxdWVzdD4=\\",\\"securityType\\":\\"MD5\\",\\"signed\\":\\"debug\\"}',
//        );

        if (isset($this->body['method']) && !empty($this->body['method'])) {
            $method = $this->body['method'];
            $requestParam = $this->body['requestParam'];
            $this->service = new Qunar_Service($requestParam);
            try {
                
                if (method_exists($this, $method)) {
                    
                    $res = $this->$method();

                    echo json_encode($res);
                    exit;
                } else {
                    //todo 错误处理
                }
            } catch (Exception $e) {
                $requestParam = $this->body['requestParam'];
                $this->service = new Qunar_Service($requestParam);
                $this->errorHandle($e->getMessage(),$e->getCode());
                exit;
            }
        } else {
            //todo 错误处理
        }
    }

    private function getProductByQunar(){

        $req = $this->service->request_body;

        $products = array();
        $pagination = array();

        if($req->method == 'ALL'){
            //todo 按分页查找
            $api_arr = array(
                'current' => $req->currentPage,
                'items' => $req->pageSize,
                'source' => 1,              //source的值？
                'agency_id' =>self::AGENCY_ID             //agency_id的值？
            );

            $api_res = ApiProductModel::model()->getProductListByCode($api_arr);
            $products = $api_res['body']['data'];
            $pagination = $api_res['body']['pagination'];
            self::echoLog('proALL', var_export($api_res, true), 'qunar_getProductByQunar.log');

        }else if($req->method = 'SINGLE'){
            $api_arr = array(
                'code' => $req->resourceId
            );


            $api_res = ApiProductModel::model()->getProductByCode($api_arr);
            
            $products[] = $api_res['body'];
            self::echoLog('proSINGLE', var_export($api_res, true), 'qunar_getProductByQunar.log');

        }
//        $api_arr = array('sign' => 'debug');
//        $res = ApiProductModel::model()->getProductListByCode($api_arr);
//        $res = ApiProductModel::model()->products($api_arr);

        //todo 数据构造提供给模版使用
        $remind = "1、订单付款后商家会尽快为您发送电子门票，凭商家发送的电子门票（不是去哪网发的订单短信）到景点售票处换票入园，部份景区凭下单时填写的身份信息游玩。\n2、商家发票时间为9：00~22：00，因短信有可能存在延时，如您急需入园，请及时致电15355755871索取门票。";
        $productInfos = array();
        foreach($products as $key=>$product){

            $productInfos[$key]['resourceId'] = $product['code'];
            $productInfos[$key]['productName'] = $product['product_name'];
            $productInfos[$key]['paymentType'] = 'PREPAY';
            $productInfos[$key]['remind'] = $remind;
            $productInfos[$key]['validType'] = 'BETWEEN_BOOK_DATE_AND_N_DAYSAFTER';

            $productInfos[$key]['daysAfterBookDateValid'] = $product['valid'];   //几天内有效
            $productInfos[$key]['periodStart'] = date('Y-m-d',$product['expire_start']);             //有效期开始日
            $productInfos[$key]['periodEnd'] =  date('Y-m-d',$product['expire_end']);               //有效期结束日
            $productInfos[$key]['marketPrice'] = intval($product['listed_price']) * 100;             //票面价格单位：分
            $productInfos[$key]['sellPrice'] =  floatval($product['price']) * 100;               //Qunar 销售产品单价单位：分

            //景区信息
            $api_res = ApiScenicModel::model()->lists(array('ids',$product['scenic_id']));
                      
            $sights = $api_res['body']['data'];
            if($sights){
            foreach($sights as $keysight=> $sight){
                $productInfos[$key]['sights'][$keysight] = array(
                    'sightName' => $sight['name'],
                    'sightAddress' => $sight['address'],
                    'city'  => $sight['district'][0],
                );
            }
            }
//            self::echoLog('time', var_export(time(), true), 'qunar_getProductByQunar.log'); die;

        }

        $arr = array(
            'count' => $req->method == 'ALL' ? $pagination['count'] : 1,
            'productInfos' => $productInfos
        );
            //    var_dump($arr);die;
        self::echoLog('productInfos', var_export($arr, true), 'qunar_getProductByQunar.log');

        return $this->service->generateResponse("getProductByQunarResponse.xml", $arr);

        //self::echoLog('body', var_export($res, true), 'qunar_getProductByQunar.log');
        //echo ($res); die;
    }

    /**
     * 创建订单校验（用于支付后下单）
     */
    private function createOrderForAfterPaySync() {
        $request = $this->service->request_body;

        $orderInfo = $request->orderInfo;
        $product = $orderInfo->product;
        $params = array();
        $params['source_id'] = $orderInfo->orderId;
        
        //$params['product_name'] = $product->productName;
        if(is_string($product->visitDate)){
             $params['use_day'] = trim($product->visitDate);
        }
        else{
            $params['use_day'] = date('Y-m-d');
        }
        $params['distributor_id'] = self::AGENCY_ID; // $this->userinfo['distributor_id'];
        $params['price_type'] = 0;
        $params['nums'] = intval($orderInfo->orderQuantity);


        $params['owner_name'] = trim($orderInfo->contactPerson->name);
        $params['owner_mobile'] = trim($orderInfo->contactPerson->mobile);

        // $params['owner_card'] = trim($orderInfo->visitPerson->person[0]->credentials);
        if(isset($orderInfo->visitPerson))
        {
            $visitors = array();
//            var_dump($orderInfo->visitPerson);die;
            if(is_array($orderInfo->visitPerson)){
            foreach($orderInfo->visitPerson->person as $visitor){
                $visitors[] = array(
                    'visitor_name' => $visitor->name,
                        'visitor_mobile' => ""
                );
            }
            }else {
                $visitors[] = array(
                    'visitor_name' => $orderInfo->visitPerson->person->name,
                       'visitor_mobile' => ""
                );
            }
//            var_dump($visitors);die;
            $params['visitors'] = json_encode($visitors);
        }

        $params['source'] = '10';
        $params['ota_type'] = 'qunar';
        $params['ota_account'] = 10;
        $params['ota_name'] = 'qunar';
        $params['user_id'] = '2147483647';         //user_id为ota_account中的主键
        $params['user_account'] = 'qunar';
        $params['user_name'] = 'qunar';
        $params['remark'] = is_string($orderInfo->orderRemark) ? $orderInfo->orderRemark : 'quna订单';
        if (strpos(strtoupper($orderInfo->orderStatus), 'PREPAY') !== FALSE) {
            $params['payment'] = 'credit';
        } else {
            $params['payment'] = 'offline';
        }

        $params['price'] = $product->sellPrice / 100;
        $error = null;
       
        //get prod detail by code
        $productDetail = $this->getProdByCode($product->resourceId);
            if($productDetail){
                $params['ticket_template_id'] = $productDetail['id'];
//                var_dump($params['price'],$productDetail);die;
                if($productDetail['price'] !=  $params['price']){
                    $error = array(
                    'code' => 'fail',
                    'message' => Lang_Msg::getLang('ERROR_PRODUCT_2')
                    ); 
                }
            }
            else{
                 $error = array(
                    'code' => 'fail',
                    'message' => Lang_Msg::getLang('ERROR_TKT_1')
                );      
            }
        
        //var_dump($params);die;
//
        if (!$params['ticket_template_id'] || !is_numeric($params['ticket_template_id'])) {
            $error = array(
                'code' => 'fail',
                'message' => Lang_Msg::getLang('ERROR_TKT_1')
            );
        }
       
        if (!$params['use_day'] || !Validate::isDateFormat($params['use_day'])) {
            $error = array(
                'code' => 'fail',
                'message' => Lang_Msg::getLang('ERROR_DATE_2')
            );
        }
        if ( $params['nums']<1) {
            $error = array(
                'code' => 'fail',
                'message' => Lang_Msg::getLang('ERROR_TK_NUMS_1')
            );
        }
        //       !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数


        $req_status = substr($orderInfo->orderStatus, 0, strlen($orderInfo->orderStatus) - 3);
        if ($error) {
                      // var_dump($error);die;
            $item = $error;
            $id = 0;
            $status = $req_status . '_FAILED';
        } else {
//            var_dump($params);die;
            $params['is_checked'] = 1;
            $item = ApiOrderModel::model()->create($params);
            self::echoLog('body', var_export(array('param'=>$params, 'item'=>$item), true), 'qunar_createorder.log');

          // var_dump($params,$item);die;
            if ($item['code'] == 'fail') {
                $id = 0;
                $status = $req_status . '_FAILED';
            } else {
                $id = $item['body']['id'];
                $status = $req_status . '_SUCCESS';
                $this->sendCodeNotice($id, 'TRUE');
            }
        }
        $this->setHeader($item, __METHOD__);
        $data = array('status' => $status, 'id' => $id);
        $rst = $this->service->generateResponse('CreateOrderForAfterPaySyncResponse.xml', $data);
        return $rst;

    }

    /**
     * 创建订单（用于支付后下单）
     */
    private function checkCreateOrderForAfterPaySync() {
        $request = $this->service->request_body;
       
        $orderInfo = $request->orderInfo;
        $product = $orderInfo->product;
        $params = array();
        $params['price'] = $product->sellPrice / 100;
        $error = null;
       
        //get prod detail by code
        $productDetail = $this->getProdByCode($product->resourceId);
    
            if($productDetail){
                $params['ticket_template_id'] = $productDetail['id'];
                if($productDetail['price'] !=  $params['price']){
                    $error = array(
                    'code' => 'fail',
                    'message' => Lang_Msg::getLang('ERROR_PRODUCT_2')
                    ); 
                }
            }
            else{
                 $error = array(
                    'code' => 'fail',
                    'message' => Lang_Msg::getLang('ERROR_TKT_1')
                );      
            }
        
        if(is_string($product->visitDate)){
             $params['use_day'] = trim($product->visitDate);
        }
        else{
            $params['use_day'] = date('Y-m-d');
        }
     
        $params['distributor_id'] = self::AGENCY_ID; // $this->userinfo['distributor_id'];
        $params['price_type'] = 0;
        $params['nums'] = intval($orderInfo->orderQuantity);
        if (!$params['ticket_template_id'] || !is_numeric($params['ticket_template_id'])) {
            $error = array(
                'code' => 'fail',
                'message' => Lang_Msg::getLang('ERROR_TKT_1')
            );
        }
        if (!$params['use_day'] || !Validate::isDateFormat($params['use_day'])) {
            $error = array(
                'code' => 'fail',
                'message' => Lang_Msg::getLang('ERROR_DATE_2')
            );
        }
        if ( $params['nums']<1) {
            $error = array(
                'code' => 'fail',
                'message' => Lang_Msg::getLang('ERROR_TK_NUMS_1')
            );
        }
//        !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数

        if ($error) {
            $item = $error;
            $id = 0;
            $status = $req_status . '_FAILED';
        } else {
            $item = ApiOrderModel::model()->check($params);
            self::echoLog('body', var_export(array('param'=>$params, 'item'=>$item), true), 'qunar_checkorder.log');

           // var_dump($params,$item);die;
        }
        $this->setHeader($item, __METHOD__);

        //var_dump($params,$item);die;
        $data = array('message' => $item['message']);

        $rst = $this->service->generateResponse('CheckCreateOrderForAfterPaySyncResponse.xml', $data);
        return $rst;
    }

    /**
     * Qunar 获取订单信息
     */
    private function getOrderByQunar() {
        $request = $this->service->request_body;
        $orderId = $request->partnerOrderId;
        if (!$orderId || !is_numeric($orderId)) {
            $error = array(
                'code' => 'fail',
                'message' => Lang_Msg::getLang('缺少参数id')
            );
        }
        $data = array(
            'partnerOrderId' => NULL,
            'orderStatus' => NULL,
            'orderQuantity' => NULL,
            'eticketNo' => NULL,
            'eticketSended' => NULL, //$item['nums'],
            'useQuantity' => NULL,
            'consumeInfo' => NULL,
            // 'orderQuantity'=> $item['nums'],
        );
        if (0 && $error) {
            $r = $error;
        } else {
            $r = ApiOrderModel::model()->detail(array('id' => $orderId));
            $sms_log = ApiOrderModel::model()->eticketSent(array('order_id' => $orderId, 'state' => '1')); //search for successful sms log

            if ($r['code'] != 'fail') {
                $item = $r['body'];
                $payment = $item['payment'];
                $status = '';

                if ($payment == 'offline') {
                    $status .= 'CASHPAY_ORDER_';
                } else {
                    $status .= 'PREPAY_ORDER_';
                }
                switch ($item['status']) {
                    case 1:
                        $status .= '';
                        break;

                    default:
                        break;
                }
                $status = 'PREPAY_ORDER_PRINT_SUCCESS';
                $data = array(
                    'partnerOrderId' => $item['id'],
                    'orderStatus' => $status,
                    'orderQuantity' => $item['nums'],
                    'eticketNo' => $item['id'],
                    'eticketSended' => 'FALSE', //$item['nums'],
                    'useQuantity' => $item['used_nums'],
                    'consumeInfo' => $item['remark'] ? $item['remark'] : '无',
                    // 'orderQuantity'=> $item['nums'],
                );
                if ($sms_log['code'] != 'fail') {
                    if ($sms_log['body']['pagination']['count'] > 0) {
                        $data['eticketSended'] = 'TRUE';
                    }
                }
            }
        }

        $this->setHeader($r, __METHOD__);
        $rst = $this->service->generateResponse('GetOrderByQunarResponse.xml', $data);

        return $rst;
    }

    /**
     * （重）发入园凭证
     */
    private function sendOrderEticket(){
        $resq = $this->service->request_body->orderInfo;

        $api_arr = array(
            'id' => $resq->partnerOrderId,
            'phoneNumber' => $resq->phoneNumber,            //目前没有考虑让内部api添加该参数
        );
        $send = ApiOrderModel::model()->sendTicket($api_arr);

        self::echoLog('body', var_export($send, true), 'qunar_sendOrderEticket.log');

        $arr = array(
            'message' => $send['message'],
        );
        if($send['code'] != 'succ'){
            //以下传递上面错误码再定
            $this->service->response_code = 14012;
            $this->service->response_desc = '重发凭证失败，原因为订单未支付或订单状态不正确';
        }
        return $this->service->generateResponse("SendOrderEticketResponse.xml", $arr);
    }

    /**
     * Qunar 退款通知
     */
    private function noticeOrderRefundedByQunar(){
        $order = $this->service->request_body->orderInfo;

        self::echoLog('body', var_export($this->service->request_body, true), 'qunar_noticeOrderRefundedByQunar.log');
        self::echoLog('body', var_export($this->service->request_header, true), 'qunar_noticeOrderRefundedByQunar.log');

        $api_arr = array(
            'order_id' => $order->partnerorderId,       //我们的订单ID
            'nums'      => $order->orderQuantity,       //原始订单票数
            'user_id'   => '2147483647',                //user_id为ota_account中的主键
            'user_account'   => 'qunar',
            'user_name'   => 'qunar',
            'remark'    => '去哪儿退款',
        );

        $cancel = ApiOrderModel::model()->cancelAndRefund($api_arr);

        self::echoLog('body', var_export($cancel, true), 'qunar_noticeOrderRefundedByQunar.log');

        $arr = array(
            'message' => $cancel['message']
        );
        if($cancel['code'] != 'succ'){
            //以下传递上面错误码再定
            $this->service->response_code = 15002;
            $this->service->response_desc = '退款失败，系统出错';
        }
        return $this->service->generateResponse("NoticeOrderRefundedByQunarResponse.xml", $arr);
    }

    /**
     * 接口心跳监测
     */
    private function testAlive(){

        echo 'alive'; die;
    }

    /**
     * 根据外部code转换成内部产品详情
     * @param $code
     * @return null
     */
    private function getProdByCode($code){
        $code = trim($code);
//$code = 3121979460;
        if($code){
            $rst = ApiProductModel::model()->getProductByCode(array('code'=>$code));
           // var_dump($rst,$code);die;
            if($rst['code'] == 'succ'){
                $prod = $rst['body'];
                return $prod;
            }
        }
        return null;
    }
    /**
     * 根据外部code转换成内部产品id
     * @param $code
     * @return null
     */
    private function getProdIDByCode($code){
             
        if($code){
            $rst = ApiProductModel::model()->getProductByCode(array('code'=>$code));
         
            if($rst['code'] == 'succ'){
                $prod = $rst['body'];
                return $prod['id'];
            }
        }
        return null;
    }

    /**
     * 发码通知
     * @param $id
     * @param string $status
     * @return bool
     */
    private function sendCodeNotice($id, $status = 'TRUE'){
        try{
            $data = array(
                //            'partnerorderId'=> $ctrl->body['verify_code'],
                'partnerorderId'=> $id,//166330007886080,
                'eticketNo'=>$id,//166330007886080,
                'eticketSended'=>$status,//'TRUE',
            );

            $service = new Qunar_Service();
            $service->qunar_url = 'http://agent.beta.qunar.com/api/external/supplierServiceV2.qunar';
            $arr = $service->request('NoticeOrderEticketSendedRequest.xml', 'noticeOrderEticketSended', $data);
            self::echoLog('body', var_export(array('param'=>$data, 'item'=>$arr), true), 'qunar_sendcode.log');

            if($arr && isset($arr->message)){
                return $arr->message;
            }
        }
        catch(Exception $e){
            return false;
        }

    }

    /*=========================错误处理=============================*/
    private function errorHandle($msg,$code) {
        $this->setHeader(array('code'=>'fail','message'=>'供应商接口出错'), __METHOD__);
        $res = $this->service->generateResponse('Error.xml', array('message'=>$msg, 'code'=>$code));
        echo json_encode($res);
        exit;
    }

    private function initErrorMap() {
        $this->ERROR_MAP[Lang_Msg::getLang('ERROR_TKT_1')] = array(
            'code' => '12001',
            'message' => '产品不存在，不可预订'
        );
        $this->ERROR_MAP[Lang_Msg::getLang('缺少参数id')] = array(
            'code' => '13001',
            'message' => '订单不存在'
        );
        $this->ERROR_MAP[Lang_Msg::getLang('ERROR_DATE_2')] = array(
            'code' => '20011',
            'message' => '创建订单异常，您选择的出行日期格式不合法'
        );
        $this->ERROR_MAP[Lang_Msg::getLang('ERROR_TK_NUMS_1')] = array(
            'code' => '20002',
            'message' => '创建订单异常，选购产品数量&lt;=0'
        );
        $this->ERROR_MAP[Lang_Msg::getLang('ERROR_PRODUCT_2')] = array(
            'code' => '20024',
            'message' => '创建订单异常，选择的价格排期已经下架'
        );
        
    }

    private function setHeader($item, $caller) {

        if ($item['code'] == 'fail') {

            if (key_exists($item['message'], $this->ERROR_MAP)) {
                $this->service->response_desc = $this->ERROR_MAP[$item['message']]['message'];
                $this->service->response_code = $this->ERROR_MAP[$item['message']]['code'];
            } else {
                $cls = $fuc = '';
                if($caller){
                    $arr = explode('::', $caller);
                    $cls = $arr[0];
                    $fuc = $arr[1];
                }
                switch($fuc){
                    case 'checkCreateOrderForAfterPaySync':
                        $this->service->response_desc = $item['message'];
                        $this->service->response_code = 99999;
                        break;
                    default:
                        $this->service->response_desc = $item['message'];
                        $this->service->response_code = 99999;
                        break;

                }

            }
        }
    }

}
