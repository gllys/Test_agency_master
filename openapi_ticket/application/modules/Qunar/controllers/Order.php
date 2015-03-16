<?php

/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-9
 * Time: 下午2:45
 */
class OrderController extends Base_Controller_ApiDispatch {

    const SUPPLIER_IDENTITY = 'DEBUGSUPPLIER';
    const SIGN_KEY = 'DEBUGSINGKEY';

    var $ERROR_MAP = array();

    public function init() {
        parent::init(false);
        $this->initErrorMap();

        self::echoLog('body', var_export($this->body, true), 'qunar_bee.log');
    }

    /**
     * 接口入口方法
     */
    public function restAction() {$this->sendCodeNoticeAsync(1);die;
//
//       $this->body = array (
//            'method' => 'createOrderForAfterPaySync',
//           'requestParam' => //'{\\"data\\":\\"PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/Pg0KPHJlcXVlc3QgeG1sbnM9Imh0dHA6Ly9waWFvLnF1bmFyLmNvbS8yMDEzL1FNZW5waWFvUmVxdWVzdFNjaGVtYSI+DQogIDxoZWFkZXI+DQogICAgPGFwcGxpY2F0aW9uPlF1bmFyLk1lbnBpYW8uQWdlbnQuQXV0b1Rlc3Q8L2FwcGxpY2F0aW9uPg0KICAgIDxwcm9jZXNzb3I+U3VwcGxpZXJEYXRhRXhjaGFuZ2VQcm9jZXNzb3I8L3Byb2Nlc3Nvcj4NCiAgICA8dmVyc2lvbj52Mi4wLjA8L3ZlcnNpb24+DQogICAgPGJvZHlUeXBlPkdldE9yZGVyQnlRdW5hclJlcXVlc3RCb2R5PC9ib2R5VHlwZT4NCiAgICA8Y3JlYXRlVXNlcj5RdW5hci5NZW5waWFvLkFnZW50LkF1dG9UZXN0PC9jcmVhdGVVc2VyPg0KICAgIDxjcmVhdGVUaW1lPjIwMTUtMDMtMTEgMTg6MjA6MjM8L2NyZWF0ZVRpbWU+DQogICAgPHN1cHBsaWVySWRlbnRpdHk+REVCVUdTVVBQTElFUjwvc3VwcGxpZXJJZGVudGl0eT4NCiAgPC9oZWFkZXI+DQogIDxib2R5IHhzaTp0eXBlPSJHZXRPcmRlckJ5UXVuYXJSZXF1ZXN0Qm9keSIgeG1sbnM6eHNpPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxL1hNTFNjaGVtYS1pbnN0YW5jZSI+DQogICAgPHBhcnRuZXJPcmRlcklkPjE2NjUzOTI5NTA4NTI3NjwvcGFydG5lck9yZGVySWQ+DQogIDwvYm9keT4NCjwvcmVxdWVzdD4=\\",\\"securityType\\":\\"MD5\\",\\"signed\\":\\"777CBD82E2FD865410BEC90E9AB3BBB8\\"}',
//       '{\\"data\\":\\"PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/Pg0KPHJlcXVlc3QgeG1sbnM9Imh0dHA6Ly9waWFvLnF1bmFyLmNvbS8yMDEzL1FNZW5waWFvUmVxdWVzdFNjaGVtYSI+DQogIDxoZWFkZXI+DQogICAgPGFwcGxpY2F0aW9uPlF1bmFyLk1lbnBpYW8uQWdlbnQuQXV0b1Rlc3Q8L2FwcGxpY2F0aW9uPg0KICAgIDxwcm9jZXNzb3I+U3VwcGxpZXJEYXRhRXhjaGFuZ2VQcm9jZXNzb3I8L3Byb2Nlc3Nvcj4NCiAgICA8dmVyc2lvbj52Mi4wLjA8L3ZlcnNpb24+DQogICAgPGJvZHlUeXBlPkNyZWF0ZU9yZGVyRm9yQWZ0ZXJQYXlTeW5jUmVxdWVzdEJvZHk8L2JvZHlUeXBlPg0KICAgIDxjcmVhdGVVc2VyPlF1bmFyLk1lbnBpYW8uQWdlbnQuQXV0b1Rlc3Q8L2NyZWF0ZVVzZXI+DQogICAgPGNyZWF0ZVRpbWU+MjAxNS0wMy0xMiAxODo1MzoyODwvY3JlYXRlVGltZT4NCiAgICA8c3VwcGxpZXJJZGVudGl0eT5ERUJVR1NVUFBMSUVSPC9zdXBwbGllcklkZW50aXR5Pg0KICA8L2hlYWRlcj4NCiAgPGJvZHkgeHNpOnR5cGU9IkNyZWF0ZU9yZGVyRm9yQWZ0ZXJQYXlTeW5jUmVxdWVzdEJvZHkiIHhtbG5zOnhzaT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS9YTUxTY2hlbWEtaW5zdGFuY2UiPg0KICAgIDxvcmRlckluZm8+DQogICAgICA8b3JkZXJJZD5xdW5hck9yZGVySWQ8L29yZGVySWQ+DQogICAgICA8cHJvZHVjdD4NCiAgICAgICAgPHJlc291cmNlSWQ+OTAzPC9yZXNvdXJjZUlkPg0KICAgICAgICA8cHJvZHVjdE5hbWU+57uN5YW05LmU5rOi5ruR6Zuq5LiW55WM5aSn5Lq656WoPC9wcm9kdWN0TmFtZT4NCiAgICAgICAgPHZpc2l0RGF0ZT4yMDE1LTAzLTIxPC92aXNpdERhdGU+DQogICAgICAgIDxzZWxsUHJpY2U+MTgwPC9zZWxsUHJpY2U+DQogICAgICAgIDxjYXNoQmFja01vbmV5Pg0KICAgICAgICA8L2Nhc2hCYWNrTW9uZXk+DQogICAgICA8L3Byb2R1Y3Q+DQogICAgICA8Y29udGFjdFBlcnNvbj4NCiAgICAgICAgPG5hbWU+5byg5LiJPC9uYW1lPg0KICAgICAgICA8bmFtZVBpbnlpbj4NCiAgICAgICAgPC9uYW1lUGlueWluPg0KICAgICAgICA8bW9iaWxlPjEzODE2NTQ4Nzg5PC9tb2JpbGU+DQogICAgICAgIDxlbWFpbD4NCiAgICAgICAgPC9lbWFpbD4NCiAgICAgICAgPGFkZHJlc3M+DQogICAgICAgIDwvYWRkcmVzcz4NCiAgICAgICAgPHppcENvZGU+DQogICAgICAgIDwvemlwQ29kZT4NCiAgICAgIDwvY29udGFjdFBlcnNvbj4NCiAgICAgIDx2aXNpdFBlcnNvbj4NCiAgICAgICAgPHBlcnNvbj4NCiAgICAgICAgICA8bmFtZT7mnY7lm5s8L25hbWU+DQogICAgICAgICAgPG5hbWVQaW55aW4+DQogICAgICAgICAgPC9uYW1lUGlueWluPg0KICAgICAgICAgIDxjcmVkZW50aWFscz40MTA5MjgxOTczMDEyODQ0MzU8L2NyZWRlbnRpYWxzPg0KICAgICAgICAgIDxjcmVkZW50aWFsc1R5cGU+SURfQ0FSRDwvY3JlZGVudGlhbHNUeXBlPg0KICAgICAgICAgIDxkZWZpbmVkMVZhbHVlPnVzZXJSZXF1aXJlZERlZmluZWQxOjEzODE2NTQ4Nzg5PC9kZWZpbmVkMVZhbHVlPg0KICAgICAgICAgIDxkZWZpbmVkMlZhbHVlPg0KICAgICAgICAgIDwvZGVmaW5lZDJWYWx1ZT4NCiAgICAgICAgPC9wZXJzb24+DQogICAgICAgIDxwZXJzb24+DQogICAgICAgICAgPG5hbWU+5p2O5ZubPC9uYW1lPg0KICAgICAgICAgIDxuYW1lUGlueWluPg0KICAgICAgICAgIDwvbmFtZVBpbnlpbj4NCiAgICAgICAgICA8Y3JlZGVudGlhbHM+NDEwOTI4MTk3MzAxMjg0NDM1PC9jcmVkZW50aWFscz4NCiAgICAgICAgICA8Y3JlZGVudGlhbHNUeXBlPklEX0NBUkQ8L2NyZWRlbnRpYWxzVHlwZT4NCiAgICAgICAgICA8ZGVmaW5lZDFWYWx1ZT51c2VyUmVxdWlyZWREZWZpbmVkMToxMzgxNjU0ODc4OTwvZGVmaW5lZDFWYWx1ZT4NCiAgICAgICAgICA8ZGVmaW5lZDJWYWx1ZT4NCiAgICAgICAgICA8L2RlZmluZWQyVmFsdWU+DQogICAgICAgIDwvcGVyc29uPg0KICAgICAgICA8cGVyc29uPg0KICAgICAgICAgIDxuYW1lPuadjuWbmzwvbmFtZT4NCiAgICAgICAgICA8bmFtZVBpbnlpbj4NCiAgICAgICAgICA8L25hbWVQaW55aW4+DQogICAgICAgICAgPGNyZWRlbnRpYWxzPjQxMDkyODE5NzMwMTI4NDQzNTwvY3JlZGVudGlhbHM+DQogICAgICAgICAgPGNyZWRlbnRpYWxzVHlwZT5JRF9DQVJEPC9jcmVkZW50aWFsc1R5cGU+DQogICAgICAgICAgPGRlZmluZWQxVmFsdWU+dXNlclJlcXVpcmVkRGVmaW5lZDE6MTM4MTY1NDg3ODk8L2RlZmluZWQxVmFsdWU+DQogICAgICAgICAgPGRlZmluZWQyVmFsdWU+DQogICAgICAgICAgPC9kZWZpbmVkMlZhbHVlPg0KICAgICAgICA8L3BlcnNvbj4NCiAgICAgIDwvdmlzaXRQZXJzb24+DQogICAgICA8b3JkZXJRdWFudGl0eT4xPC9vcmRlclF1YW50aXR5Pg0KICAgICAgPG9yZGVyUHJpY2U+b3JkZXJQcmljZTwvb3JkZXJQcmljZT4NCiAgICAgIDxvcmRlckNhc2hCYWNrTW9uZXk+Y2FzaEJhY2tNb25leTwvb3JkZXJDYXNoQmFja01vbmV5Pg0KICAgICAgPG9yZGVyU3RhdHVzPlBSRVBBWV9PUkRFUl9QUklOVElORzwvb3JkZXJTdGF0dXM+DQogICAgICA8b3JkZXJSZW1hcms+cmVtYXJrPC9vcmRlclJlbWFyaz4NCiAgICAgIDxvcmRlclNvdXJjZT5vcmRlclNvdXJjZTwvb3JkZXJTb3VyY2U+DQogICAgICA8cGF5bWVudFNlcmlhbG5vPnBheW1lbnRTZXJpYWxubzwvcGF5bWVudFNlcmlhbG5vPg0KICAgIDwvb3JkZXJJbmZvPg0KICA8L2JvZHk+DQo8L3JlcXVlc3Q+\\",\\"securityType\\":\\"MD5\\",\\"signed\\":\\"debug\\"}'
//           );
        //  $a=1/0;

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

    public function createOrderForAfterPaySync() {
        $request = $this->service->request_body;

        $orderInfo = $request->orderInfo;
        $product = $orderInfo->product;
        $params = array();
        $params['source_id'] = $orderInfo->orderId;
        $params['ticket_template_id'] = $this->getProdIDByCode($product->resourceId);
        //$params['product_name'] = $product->productName;
        $params['use_day'] = trim($product->visitDate);
        $params['distributor_id'] = 167; // $this->userinfo['distributor_id'];
        $params['price_type'] = 0;
        $params['nums'] = intval($orderInfo->orderQuantity);


        $params['owner_name'] = trim($orderInfo->contactPerson->name);
        $params['owner_mobile'] = trim($orderInfo->contactPerson->mobile);

       // $params['owner_card'] = trim($orderInfo->visitPerson->person[0]->credentials);
       if(isset($orderInfo->visitPerson))
       {
           $visitors = array();
           foreach($orderInfo->visitPerson->person as $visitor){
               $visitors[] = array(
                   'visitor_name' => $visitor->name,
                   'visitor_mobile' => $visitor->credentials
               );
           }
           $params['visitors'] = json_encode($visitors);
       }

        $params['source'] = '10';
        $params['ota_type'] = 'qunar';
        $params['ota_account'] = 10;
        $params['ota_name'] = 'qunar';
        $params['user_id'] = '2147483647';         //user_id为ota_account中的主键
        $params['user_account'] = 'qunar';
        $params['user_name'] = 'qunar';
        $params['remark'] = isset($orderInfo->orderRemark) ? $orderInfo->orderRemark : 'quna订单';
        if (strpos(strtoupper($orderInfo->orderStatus), 'PREPAY') !== FALSE) {
            $params['payment'] = 'credit';
        } else {
            $params['payment'] = 'offline';
        }

        $params['price'] = $product->sellPrice / 100;
        $error = null;
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
            $item = $error;
            $id = 0;
            $status = $req_status . '_FAILED';
        } else {
            $item = ApiOrderModel::model()->create($params);

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

    public function checkCreateOrderForAfterPaySync() {
        $request = $this->service->request_body;

        $orderInfo = $request->orderInfo;
        $product = $orderInfo->product;
        $params = array();
        $params['ticket_template_id'] = $this->getProdIDByCode($product->resourceId);
        $params['use_day'] = trim($product->visitDate);
        $params['distributor_id'] = 167; // $this->userinfo['distributor_id'];
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
        }
        $this->setHeader($item, __METHOD__);

        //var_dump($params,$item);die;
        $data = array('message' => $item['message']);

        $rst = $this->service->generateResponse('CheckCreateOrderForAfterPaySyncResponse.xml', $data);
        return $rst;
    }

    public function getOrderByQunar() { 
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

    private function sendCodeNotice($id, $status = 'TRUE'){
        try{
            $data = array(
    //            'partnerorderId'=> $ctrl->body['verify_code'],
                'partnerorderId'=> $id,//166330007886080,
                'eticketNo'=>$id,//166330007886080,
                'eticketSended'=>$status,//'TRUE',
            );

            $service = new Qunar_Service();

            $arr = $service->request('NoticeOrderEticketSendedRequest.xml', 'noticeOrderEticketSended', $data);

            if($arr && isset($arr->message)){
                 return $arr->message;
            }    
        }
        catch(Exception $e){
            return false;
        }

    }

    public static function test(){echo 123;}
    private function sendCodeNoticeAsync($id, $status = 'TRUE'){
        try{
            $data = array(
                //            'partnerorderId'=> $ctrl->body['verify_code'],
                'partnerorderId'=> $id,//166330007886080,
                'eticketNo'=>$id,//166330007886080,
                'eticketSended'=>$status,//'TRUE',
            );
           // OrderController::test();
            Process_Async::send(array('OrderController','test'),array($data));

            
        }
        catch(Exception $e){echo $e->getMessage();
            return false;
        }

    }

    
}
