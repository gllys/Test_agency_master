<?php

/**
 * Description of ApiNotifyModel
 *
 * @author wfdx1_000
 */
class ApiNotifyModel extends Base_Model_Api {

    public static function sendByAsync($params) {
        try {
            $data = Tools::curl($params['notify_url'], 'POST', $params['callbackParams']);
        } catch (Exception $ex) {
        }
        print_r($params);
        
        $r = json_decode($data, true);
        if (!$r['code'] || $r['code'] == 'fail') {
            $logData = array(
                'order_id' => $params['order_id'],
                'desc' => '取消訂單回調失敗'
            );
        }
    }

}
