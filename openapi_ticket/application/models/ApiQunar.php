<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-27
 * Time: 下午6:28
 */
class ApiQunarModel extends Base_Model_Api{
    public static function sendCodeNotice($data){
        try{
            $service = new Qunar_Service(array());

            $config = Yaf_Registry::get('config');
            $service->qunar_url = $config['qunar']['sendcode_url'];
            $arr = $service->request('NoticeOrderEticketSendedRequest.xml', 'noticeOrderEticketSended', $data);

            Util_Logger::getLogger('qunar')->info(__METHOD__,
                array('data' => $data,'header' => $service->response_header, 'body' => $service->response_body), '', '发码通知',
                $data['partnerorderId']
            );

            if($arr && isset($arr->message)){
                return $arr->message;
            }
        }
        catch(Exception $e){
            return false;
        }

    }

}