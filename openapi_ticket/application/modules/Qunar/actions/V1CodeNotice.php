<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-3-11
 * Time: 上午10:38
 */

class CodeNoticeAction extends Yaf_Action_Abstract{
    /**
     * 用户消费(核销)通知（去哪儿）
     */
    public function execute(){
        //获取本动作对应的控制器实例
        $ctrl = $this->getController();

        $data = array(
//            'partnerorderId'=> $ctrl->body['verify_code'],
            'partnerorderId'=> $ctrl->body['order_id'],//166330007886080,
            'eticketNo'=>$ctrl->body['order_id'],//166330007886080,
            'eticketSended'=>$ctrl->body['code_sended'],//'TRUE',
        );

        $service = new Qunar_RequestService();

        $arr = $service->request('NoticeOrderEticketSendedRequest.xml', 'noticeOrderEticketSended', $data);

        var_dump($arr->message);die;

//        $rep_service = new Qunar_Service($arr['requestParam']);
//
//        var_dump($rep_service->request_body);

        $ctrl->echoLog('body', var_export($arr, true), 'qunar_noticeOrderEticketSended.log');

//        $postData = array (
//            'method' => 'noticeProductChanged',
//            'requestParam' => '{\\"data\\":\\"PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxyZXF1ZXN0IHhtbG5zPSJodHRwOi8vcGlhby5xdW5hci5jb20vMjAxMy9RTWVucGlhb1JlcXVlc3RTY2hlbWEiPjxoZWFkZXI+PGFwcGxpY2F0aW9uPlF1bmFyLk1lbnBpYW8uQWdlbnQuQXV0b1Rlc3Q8L2FwcGxpY2F0aW9uPjxwcm9jZXNzb3I+U3VwcGxpZXJEYXRhRXhjaGFuZ2VQcm9jZXNzb3I8L3Byb2Nlc3Nvcj48dmVyc2lvbj52Mi4wLjA8L3ZlcnNpb24+PGJvZHlUeXBlPkdldFByb2R1Y3RCeVF1bmFyUmVxdWVzdEJvZHk8L2JvZHlUeXBlPjxjcmVhdGVVc2VyPlF1bmFyLk1lbnBpYW8uQWdlbnQuQXV0b1Rlc3Q8L2NyZWF0ZVVzZXI+PGNyZWF0ZVRpbWU+MjAxNS0wMy0xMCAxMzo1Nzo1NTwvY3JlYXRlVGltZT48c3VwcGxpZXJJZGVudGl0eT5ERUJVR1NVUFBMSUVSPC9zdXBwbGllcklkZW50aXR5PjwvaGVhZGVyPjxib2R5IHhzaTp0eXBlPSJHZXRQcm9kdWN0QnlRdW5hclJlcXVlc3RCb2R5IiB4bWxuczp4c2k9Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvWE1MU2NoZW1hLWluc3RhbmNlIj48bWV0aG9kPkFMTDwvbWV0aG9kPjxjdXJyZW50UGFnZT4xPC9jdXJyZW50UGFnZT48cGFnZVNpemU+MTA8L3BhZ2VTaXplPjxyZXNvdXJjZUlkPgogICAgPC9yZXNvdXJjZUlkPjwvYm9keT48L3JlcXVlc3Q+\\",\\"securityType\\":\\"MD5\\",\\"signed\\":\\"B25344AF7D094F766CC7CDB7B6245830\\"}',
//        );


//        $resp = $service->curl($arr);

//        var_dump($resp); die;
    }




}