<?php

/**
 * Class OrderEvent
 */
class OrderEventModel extends Base_Model_Abstract
{

    public static function send($data)
    {
    	//return static::rsync($data);
        return Process_Async::presend(array(__CLASS__, 'rsync'), [$data]);
    }

    public static function rsync($data)
    {
        try {
        	$url = TICKET_URL.'/api/localOrders/add';
	    	$orders = OrderModel::model()->getList($data, '`id`, `landscape_ids`, `updated_at`');
	        $requestArgs['data'] = json_encode($orders);
	        $requestArgs['sign'] = Base_Model_Api::model()->getSign($requestArgs);
        	$response = Tools::curl($url, 'POST', $requestArgs);
        	if (!$response || !$response = json_decode($response, true)) {
        		throw new Lang_Exception('响应为空');
        	}
        	if ($response['code'] != 'succ') {
        		throw new Lang_Exception($response['message'] ?: $response['code']);
        	}
        }catch(Exception $e) {
        	$content = 'method: '. __METHOD__
				. PHP_EOL . ' message: ' . $e->getMessage()
				. PHP_EOL . ' params: '. var_export($requestArgs, true);
			echo $content, PHP_EOL;
            /*MailModel::sendSrvGroup("落地数据推送失败", $content);
            MailModel::sendTicketDevGroup("落地数据推送失败", $content);*/
        }
    }
}