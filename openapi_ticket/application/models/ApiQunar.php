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
             $config = Yaf_Registry::get('config');
             $send_code_url = $config['qunar']['sendcode_url'];

            $service = new Qunar_Service(array());
echo 'ApiQunar-sendCodeNotice:';
var_dump($data);
            $service->qunar_url = $send_code_url;
            $arr = $service->request('NoticeOrderEticketSendedRequest.xml', 'noticeOrderEticketSended', $data);
var_dump($arr);
            if($arr && isset($arr->message)){
                return $arr->message;
            }
        }
        catch(Exception $e){
            return false;
        }

    }

}