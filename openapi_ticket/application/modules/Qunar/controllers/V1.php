<?php

/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-9
 * Time: 下午2:45
 */
class V1Controller extends Base_Controller_ApiDispatch {

//    protected $agency_id ;//147;
    protected $user_id = '3383470907'; //'2147483647';
    
    const SOURCE = 10;
    const PRICE_TYPE = 0;
    const LOCAL_SOURCE = 1;
    
    const OTA_ACCOUNT = 10;
    const OTA_TYPE = 'qunar';
    const OTA_NAME = 'qunar';
    
    const USER_ACCOUNT = 'qunar';
    const USER_NAME = 'qunar';

    
    private $errorMap = array();
    private $service = null;

    public function init() {
        parent::init(false);
        $this->user_id = $this->config['qunar']['user_id'];
//        $this->agency_id = $this->config['qunar']['agency_id'];
        $this->initErrorMap();

        self::echoLog('body', var_export($this->body, true), 'qunar_bee.log');
    }

    /**
     * 接口入口方法
     */
    public function restAction() {
//        $this->body = array (
//            'method' => 'createOrderForAfterPaySync',
//            'requestParam' => '{\\"data\\":\\"PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/Pg0KPHJlcXVlc3QgeG1sbnM9Imh0dHA6Ly9waWFvLnF1bmFyLmNvbS8yMDEzL1FNZW5waWFvUmVxdWVzdFNjaGVtYSI+DQo8aGVhZGVyPg0KPGFwcGxpY2F0aW9uPg0KUXVuYXIuTWVucGlhby5BZ2VudDwvYXBwbGljYXRpb24+DQo8cHJvY2Vzc29yPg0KU3VwcGxpZXJEYXRhRXhjaGFuZ2VQcm9jZXNzb3I8L3Byb2Nlc3Nvcj4NCjx2ZXJzaW9uPg0KdjIuMC4wPC92ZXJzaW9uPg0KPGJvZHlUeXBlPg0KQ3JlYXRlT3JkZXJGb3JBZnRlclBheVN5bmNSZXF1ZXN0Qm9keTwvYm9keVR5cGU+DQo8Y3JlYXRlVXNlcj4NClF1bmFyLk1lbnBpYW8uQWdlbnQ8L2NyZWF0ZVVzZXI+DQo8Y3JlYXRlVGltZT4NCjIwMTUtMDMtMTYgMjM6NDE6MDg8L2NyZWF0ZVRpbWU+DQo8c3VwcGxpZXJJZGVudGl0eT4NCk1FSUpJTkdURVNUMjwvc3VwcGxpZXJJZGVudGl0eT4NCjwvaGVhZGVyPg0KPGJvZHkgeHNpOnR5cGU9IkNyZWF0ZU9yZGVyRm9yQWZ0ZXJQYXlTeW5jUmVxdWVzdEJvZHkiIHhtbG5zOnhzaT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS9YTUxTY2hlbWEtaW5zdGFuY2UiPg0KPG9yZGVySW5mbz4NCjxvcmRlcklkPg0KNjAwMzYzODA3PC9vcmRlcklkPg0KPHByb2R1Y3Q+DQo8cmVzb3VyY2VJZD4NCjkzNTwvcmVzb3VyY2VJZD4NCjxwcm9kdWN0TmFtZT4NCuWOu+WTquWEv1RXTzwvcHJvZHVjdE5hbWU+DQo8dmlzaXREYXRlPg0KPC92aXNpdERhdGU+DQo8c2VsbFByaWNlPg0KMTAwPC9zZWxsUHJpY2U+DQo8Y2FzaEJhY2tNb25leT4NCjA8L2Nhc2hCYWNrTW9uZXk+DQo8L3Byb2R1Y3Q+DQo8Y29udGFjdFBlcnNvbj4NCjxuYW1lPg0KYWRkPC9uYW1lPg0KPG5hbWVQaW55aW4+DQo8L25hbWVQaW55aW4+DQo8bW9iaWxlPg0KMTg4MTcyMDk0ODA8L21vYmlsZT4NCjxlbWFpbD4NCjwvZW1haWw+DQo8YWRkcmVzcz4NCjwvYWRkcmVzcz4NCjx6aXBDb2RlPg0KPC96aXBDb2RlPg0KPC9jb250YWN0UGVyc29uPg0KPHZpc2l0UGVyc29uPg0KPHBlcnNvbj4NCjxuYW1lPg0KYTI8L25hbWU+DQo8bmFtZVBpbnlpbj4NCjwvbmFtZVBpbnlpbj4NCjxjcmVkZW50aWFscz4NCjwvY3JlZGVudGlhbHM+DQo8Y3JlZGVudGlhbHNUeXBlPg0KSURfQ0FSRDwvY3JlZGVudGlhbHNUeXBlPg0KPGRlZmluZWQxVmFsdWU+DQo8L2RlZmluZWQxVmFsdWU+DQo8ZGVmaW5lZDJWYWx1ZT4NCjwvZGVmaW5lZDJWYWx1ZT4NCjwvcGVyc29uPg0KPHBlcnNvbj4NCjxuYW1lPg0KYTE8L25hbWU+DQo8bmFtZVBpbnlpbj4NCjwvbmFtZVBpbnlpbj4NCjxjcmVkZW50aWFscz4NCjwvY3JlZGVudGlhbHM+DQo8Y3JlZGVudGlhbHNUeXBlPg0KSURfQ0FSRDwvY3JlZGVudGlhbHNUeXBlPg0KPGRlZmluZWQxVmFsdWU+DQo8L2RlZmluZWQxVmFsdWU+DQo8ZGVmaW5lZDJWYWx1ZT4NCjwvZGVmaW5lZDJWYWx1ZT4NCjwvcGVyc29uPg0KPC92aXNpdFBlcnNvbj4NCjxvcmRlclF1YW50aXR5Pg0KMjwvb3JkZXJRdWFudGl0eT4NCjxvcmRlclByaWNlPg0KODAwMDwvb3JkZXJQcmljZT4NCjxvcmRlckNhc2hCYWNrTW9uZXk+DQowPC9vcmRlckNhc2hCYWNrTW9uZXk+DQo8b3JkZXJTdGF0dXM+DQpQUkVQQVlfT1JERVJfUFJJTlRJTkc8L29yZGVyU3RhdHVzPg0KPG9yZGVyUmVtYXJrPg0KPC9vcmRlclJlbWFyaz4NCjxvcmRlclNvdXJjZT4NCk5PUk1BTDwvb3JkZXJTb3VyY2U+DQo8cGF5bWVudFNlcmlhbG5vPg0KcGlhbzYwMDM2MzgwNzwvcGF5bWVudFNlcmlhbG5vPg0KPC9vcmRlckluZm8+DQo8L2JvZHk+DQo8L3JlcXVlc3Q+\\",\\"securityType\\":\\"MD5\\",\\"signed\\":\\"debug\\"}',
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
                $this->errorHandle($e->getMessage(), $e->getCode());
                exit;
            }
        } else {
            //todo 错误处理
        }
    }

    private function getProductByQunar() {

        $req = $this->service->request_body;
        self::echoLog('req', var_export($req, true), 'qunar_getProductByQunar.log');

        $products = array();
        $pagination = array();

        if ($req->method == 'ALL') {
            //todo 按分页查找
            $api_arr = array(
                'current' => $req->currentPage,
                'items' => $req->pageSize,
                'source' => 10, //source的值？
                'agency_id' => $this->service->agency_id             //agency_id的值？
            );

            $api_res = ApiProductModel::model()->getProductListByCode($api_arr);
            $products = $api_res['body']['data'];
            $pagination = $api_res['body']['pagination'];
            self::echoLog('proALL', var_export($api_res, true), 'qunar_getProductByQunar.log');
        } else if ($req->method = 'SINGLE') {
            $api_arr = array(
                'code' => $req->resourceId
            );


            $api_res = ApiProductModel::model()->getProductByCode($api_arr);

            $products[] = $api_res['body'];
            self::echoLog('proSINGLE', var_export($api_res, true), 'qunar_getProductByQunar.log');
        }


        //todo 数据构造提供给模版使用
        $remind = "1、订单付款后商家会尽快为您发送电子门票，凭商家发送的电子门票（不是去哪网发的订单短信）到景点售票处换票入园，部份景区凭下单时填写的身份信息游玩。\n2、商家发票时间为9：00~22：00，因短信有可能存在延时，如您急需入园，请及时致电15355755871索取门票。\n重要提示：\n景点门票、酒店等属于自助旅游产品，本店只提供代订服务，凡订购者均视为具有完全民事行为能力人，买家需自己对游玩可能产生的风险进行评估且自行负责，如在游玩中发生意外伤害等事故，本店不承担任何责任。本产品不含任何保险，建议买家另购旅游意外险！买家拍下宝贝，视同买家对旅游活动期间有关安全事项做了充分了解与准备，购买成功即视为已经阅读本声明并接受以上条款，条款自动生效。";
        $smsTemplet = '感谢您购买“浙风国际旅行社”的旅游产品，稍候将为您发送电子门票，请凭浙风发送的电子门票消费游玩。如长时间未收到电子票，请及时致电15355755871索取。';
        $productInfos = array();
        foreach ($products as $key => $product) {

            $productInfos[$key]['resourceId']   = $product['code'];
            $productInfos[$key]['productName']  = $product['product_name'];
            $productInfos[$key]['paymentType']  = 'PREPAY';
            $productInfos[$key]['remind']       = !empty($product['description']) ? $product['description'] : $remind;
            $productInfos[$key]['smsTemplet']   = !empty($product['extra']['msg_custom']) ? $product['extra']['msg_custom'] : $smsTemplet;

            $productInfos[$key]['canRefund'] = $product['refund'] == 1 ? 'TRUE' : 'FALSE';
            $productInfos[$key]['bookAdvanceDay'] = floor($product['scheduled_time'] / 86400);
            $productInfos[$key]['bookAdvanceTime'] = gmstrftime('%H:%M', $product['scheduled_time'] % 86400);
            $productInfos[$key]['autoCancelTime'] = $product['extra']['cancel_time'];
            $productInfos[$key]['visitPersonRequiredForQuantity'] = $product['extra']['user_per_infos'];

            $buyer_fileds = explode( ',', $product['extra']['buyer_fileds']);
            $productInfos[$key]['contactNamePinyinRequired']    = in_array('namePinyinRequired', $buyer_fileds) ? 'TRUE' : 'FALSE';
            $productInfos[$key]['contactEmailRequired']         =  in_array('emailRequired', $buyer_fileds) ? 'TRUE' : 'FALSE';
            $productInfos[$key]['contactAddressRequired']       =  in_array('addressRequired', $buyer_fileds) ? 'TRUE' : 'FALSE';
//            $user_fileds = explode($product['extra']['user_fileds'], ',');
//            $productInfos[$key]['visitNamePinyinRequired'] = in_array('namePinyinRequired', $user_fileds) ? TRUE : FALSE;
            $productInfos[$key]['perPhoneMaximum'] = $product['extra']['mobile_limit'];

            $productInfos[$key]['feeInfo']          = $product['consumption_detail'];
            $productInfos[$key]['cashBackMoney']    = intval($product['extra']['derate']) * 100;
            $productInfos[$key]['refundApplyTimeBeforeValidEndDay'] = $product['extra']['refund_time'];
            $refundChargeType = array(1 => 'QUANTITY', 2 => 'ORDER');
            $productInfos[$key]['refundChargeType'] = isset($product['extra']['refund_type']) ? $refundChargeType[$product['extra']['refund_type']] : '';
            $productInfos[$key]['refundCharge']     = intval($product['extra']['refund_fee']) * 100;
            $productInfos[$key]['refundInfo']       = $product['refund_detail'];


            $productInfos[$key]['validType'] = $product['valid_flag'] == 0 ? 'BETWEEN_BOOK_DATE_AND_N_DAYSAFTER' : 'BETWEEN_USE_DATE_START_AND_END';
            $productInfos[$key]['daysAfterBookDateValid'] = $product['valid'] + 1;   //几天内有效，valid==0 表示当天有效，故需要加1
            $date_available = explode(',',$product['date_available']);
            $productInfos[$key]['periodStart'] = date('Y-m-d',$date_available[0]);             //有效期开始日
            $productInfos[$key]['periodEnd'] = date('Y-m-d',$date_available[1]);               //有效期结束日

            $productInfos[$key]['validWeek']    = $product['week_time'];
            $productInfos[$key]['marketPrice']  = intval($product['listed_price2']) * 100;             //票面价格单位：分
            $productInfos[$key]['sellPrice']    = floatval($product['price']) * 100;               //Qunar 销售产品单价单位：分
            $productInfos[$key]['minimum']      = $product['mini_buy'];//最小购买量
            $productInfos[$key]['maximum']      = $product['max_buy'];//最大购买量
            //景区信息
            $api_res = ApiScenicModel::model()->lists(array('ids' => $product['scenic_id']));

            $sights = $api_res['body']['data'];
            if ($sights) {
                foreach ($sights as $keysight => $sight) {
                    $productInfos[$key]['sights'][$keysight] = array(
                        'sightName' => $sight['name'],
                        'sightAddress' => $sight['address'],
                        'city' => $sight['district'][1],        //城市
                    );
                }
            }
//            self::echoLog('time', var_export(time(), true), 'qunar_getProductByQunar.log'); die;
        }

        $arr = array(
            'count' => $req->method == 'ALL' ? $pagination['count'] : 1,
            'productInfos' => $productInfos
        );

        self::echoLog('productInfos', var_export($arr, true), 'qunar_getProductByQunar.log');
        return $this->service->generateResponse("GetProductByQunarResponse.xml", $arr);

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

        if (is_string($product->visitDate)) {
            $params['use_day'] = trim($product->visitDate);
        } else {
            $params['use_day'] = date('Y-m-d');
        }
        $params['distributor_id'] = $this->service->agency_id;
        $params['price_type'] = self::PRICE_TYPE;
        $params['local_source'] = self::LOCAL_SOURCE;
        
        $params['nums'] = intval($orderInfo->orderQuantity);


        $params['owner_name'] = trim($orderInfo->contactPerson->name);
        $params['owner_mobile'] = trim($orderInfo->contactPerson->mobile);

        // $params['owner_card'] = trim($orderInfo->visitPerson->person[0]->credentials);  
        if (isset($orderInfo->visitPerson)) {
            $visitors = array();
            if (is_array($orderInfo->visitPerson->person)) {
                foreach ($orderInfo->visitPerson->person as $visitor) {
                    $visitors[] = array(
                        'visitor_name' => $visitor->name,
                    );
                }
            } else {
                $visitors[] = array(
                    'visitor_name' => $orderInfo->visitPerson->person->name,
                );
            }
            $params['visitors'] = json_encode($visitors);
        }
        $params['source'] = self::SOURCE;
        $params['ota_type'] = self::OTA_TYPE;
        $params['ota_account'] = self::OTA_ACCOUNT;
        $params['ota_name'] = self::OTA_NAME;
        $params['user_id'] = $this->user_id;         //user_id为ota_account中的主键
        $params['user_account'] = self::USER_ACCOUNT;
        $params['user_name'] = self::USER_NAME;
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
        if ($productDetail) {
            $params['ticket_template_id'] = $productDetail['product_id'];
            if ($productDetail['price'] != $params['price']) {
                $error = array(
                    'code' => 'fail',
                    'message' => '该产品价格不符，无法购买'
                );
            }
        } else {
            $error = array(
                'code' => 'fail',
                'message' => '缺少票种ID参数'
            );
        }

        if (!$params['ticket_template_id'] || !is_numeric($params['ticket_template_id'])) {
            $error = array(
                'code' => 'fail',
                'message' => '缺少票种ID参数'
            );
        }

        if (!$params['use_day'] || !Validate::isDateFormat($params['use_day'])) {
            $error = array(
                'code' => 'fail',
                'message' => '日期格式必须是xxxx-xx-xx'
            );
        }
        if ($params['nums'] < 1) {
            $error = array(
                'code' => 'fail',
                'message' => '订购票数不能少于1'
            );
        }
        //       !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数


        $req_status = substr($orderInfo->orderStatus, 0, strlen($orderInfo->orderStatus) - 3);
        if ($error) {
            $item = $error;
            $id = 0;
            $status = $req_status . '_FAILED';
        } else {
            $params['is_checked'] = 1;
            $item = ApiOrderModel::model()->create($params);
            self::echoLog('body', var_export(array('param' => $params, 'item' => $item), true), 'qunar_createorder.log');

            if ($item['code'] !== 'succ') {
                $id = 0;
                $status = $req_status . '_FAILED';
            } else {
                $id = $item['body']['id'];
                $status = $req_status . '_SUCCESS';
                $this->sendCodeNoticeAsync($id, 'TRUE');
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

        if ($productDetail) {
            $params['ticket_template_id'] = $productDetail['product_id'];
            if ($productDetail['price'] != $params['price']) {
                $error = array(
                    'code' => 'fail',
                    'message' => '该产品价格不符，无法购买'
                );
            }
        } else {
            $error = array(
                'code' => 'fail',
                'message' => '缺少票种ID参数'
            );
        }

        if (is_string($product->visitDate)) {
            $params['use_day'] = trim($product->visitDate);
        } else {
            $params['use_day'] = date('Y-m-d');
        }

        $params['distributor_id'] = $this->service->agency_id; // $this->userinfo['distributor_id'];
        $params['price_type'] = self::PRICE_TYPE;
        $params['nums'] = intval($orderInfo->orderQuantity);
        if (!$params['ticket_template_id'] || !is_numeric($params['ticket_template_id'])) {
            $error = array(
                'code' => 'fail',
                'message' => '缺少票种ID参数'
            );
        }
        if (!$params['use_day'] || !Validate::isDateFormat($params['use_day'])) {
            $error = array(
                'code' => 'fail',
                'message' => '日期格式必须是xxxx-xx-xx'
            );
        }
        if ($params['nums'] < 1) {
            $error = array(
                'code' => 'fail',
                'message' => '订购票数不能少于1'
            );
        }
//        !$params['distributor_id'] && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数
                    
        if ($error) {
            $item = $error;
        } else {
            $item = ApiOrderModel::model()->check($params);
            self::echoLog('body', var_export(array('param' => $params, 'item' => $item), true), 'qunar_checkorder.log');

        }
        $this->setHeader($item, __METHOD__);

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
            'eticketSended' => NULL, 
            'useQuantity' => NULL,
            'consumeInfo' => NULL,
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
                    'eticketSended' => 'FALSE', 
                    'useQuantity' => $item['used_nums'],
                    'consumeInfo' => $item['remark'] ? $item['remark'] : '无',
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
    private function sendOrderEticket() {
        $resq = $this->service->request_body->orderInfo;

        $api_arr = array(
            'id' => $resq->partnerOrderId,
            'phoneNumber' => $resq->phoneNumber, //目前没有考虑让内部api添加该参数
        );
        $send = ApiOrderModel::model()->sendTicket($api_arr);

        self::echoLog('body', var_export($send, true), 'qunar_sendOrderEticket.log');

        $arr = array(
            'message' => $send['message'],
        );
        if ($send['code'] != 'succ') {
            //以下传递上面错误码再定
            $this->service->response_code = 14012;
            $this->service->response_desc = '重发凭证失败，原因为订单未支付或订单状态不正确';
        }
        return $this->service->generateResponse("SendOrderEticketResponse.xml", $arr);
    }

    /**
     * Qunar 退款通知
     */
    private function noticeOrderRefundedByQunar() {
        $order = $this->service->request_body->orderInfo;

        self::echoLog('body', var_export($this->service->request_body, true), 'qunar_noticeOrderRefundedByQunar.log');
        self::echoLog('body', var_export($this->service->request_header, true), 'qunar_noticeOrderRefundedByQunar.log');

        $api_arr = array(
            'order_id' => $order->partnerorderId, //我们的订单ID
            'nums' => $order->refundQuantity, //原始订单票数
            'user_id' =>  $this->user_id, //user_id为ota_account中的主键
            'user_account' => self::USER_ACCOUNT,
            'user_name' => self::USER_NAME,
            'remark' => '去哪儿退款',
        );

        $cancel = ApiOrderModel::model()->cancelAndRefund($api_arr);

        self::echoLog('body', var_export($cancel, true), 'qunar_noticeOrderRefundedByQunar.log');
        self::echoLog('body', var_export($api_arr, true), 'qunar_noticeOrderRefundedByQunar.log');
        $arr = array(
            'message' => $cancel['message']
        );
        if ($cancel['code'] != 'succ') {
            //以下传递上面错误码再定
            $this->service->response_code = 15002;
            $this->service->response_desc = '退款失败，系统出错';
        }
        return $this->service->generateResponse("NoticeOrderRefundedByQunarResponse.xml", $arr);
    }

    /**
     * 接口心跳监测
     */
    private function testAlive() {

        echo 'alive';
        die;
    }

    /**
     * 根据外部code转换成内部产品详情
     */
    private function getProdByCode($code) {
        $code = trim($code);
        if ($code) {
            $rst = ApiProductModel::model()->getProductByCode(array('code' => $code));
            if ($rst['code'] == 'succ') {
                $prod = $rst['body'];
                return $prod;
            }
        }
        return null;
    }

    /**
     * 发码通知
     */
    private function sendCodeNoticeAsync($id, $status = 'TRUE') {
        //$id = '166558779596696' ; 
        try {
            $data = array(
                'partnerorderId' => $id,
                'eticketNo' => $id,
                'eticketSended' => $status,
            );
            Process_Async::send(array('ApiQunarModel', 'sendCodeNotice'), array($data));
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /* =========================错误处理============================= */

    private function errorHandle($msg, $code) {
        $this->setHeader(array('code' => 'fail', 'message' => '供应商接口出错'), __METHOD__);
        $res = $this->service->generateResponse('Error.xml', array('message' => $msg, 'code' => $code));
        echo json_encode($res);
        exit;
    }

    private function initErrorMap() {
        $this->errorMap['缺少票种ID参数'] = array(
            'code' => '12001',
            'message' => '产品不存在，不可预订'
        );
        $this->errorMap[Lang_Msg::getLang('缺少参数id')] = array(
            'code' => '13001',
            'message' => '订单不存在'
        );
        $this->errorMap['日期格式必须是xxxx-xx-xx'] = array(
            'code' => '20011',
            'message' => '创建订单异常，您选择的出行日期格式不合法'
        );
        $this->errorMap['订购票数不能少于1'] = array(
            'code' => '20002',
            'message' => '创建订单异常，选购产品数量&lt;=0'
        );
        $this->errorMap['该产品价格不符，无法购买'] = array(
            'code' => '20024',
            'message' => '创建订单异常，选择的价格排期已经下架'
        );
    }

    private function setHeader($item, $caller) {

        if ($item['code'] == 'fail') {

            if (key_exists($item['message'], $this->errorMap)) {
                $this->service->response_desc = $this->errorMap[$item['message']]['message'];
                $this->service->response_code = $this->errorMap[$item['message']]['code'];
            } else {
                $cls = $fuc = '';
                if ($caller) {
                    $arr = explode('::', $caller);
                    $cls = $arr[0];
                    $fuc = $arr[1];
                }
                switch ($fuc) {
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
