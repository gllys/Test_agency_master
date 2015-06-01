<?php

class TaobaoOrderModel extends Base_Model_Api
{
    protected $srvKey = 'openapi_ticket';
    protected $url = '';
    protected $method = 'POST';

    public function verificate($order, $num, $record, $device,$landscape_id=0) {
        $this->url = '/common/v1/consume';
		$orderItems = OrderItemModel::model()->search(['order_id'=>$order['id']], 'id,status,use_time');
		foreach ($orderItems as $id=>$item) {
			if (!$item['status']) {
				$item['use_time'] = 0;
			} else {
				$item['use_time'] = date('Y-m-d H:i:s', $item['use_time']);
			}
			$orderItems[$id] = $item;
		}
        $this->params = array(
            'order_id' => $order['source_id'],
            'verify_code' => $order['id'],
            'consume_num' => $num,
            'used_num' => $order['used_nums']+$num,
            'num' => $order['nums'],
            'refunded_nums' => $order['refunded_nums'],
            'refunding_nums' => $order['refunding_nums'],
            'source' => $order['source'],
            'token' => $order['source_token'],
            'posid' => $device['id'],
            'serial_num' => $record['id'],
            'distributor_id' => $order['distributor_id'],
            'landscape_id' => $landscape_id,
			'order_items' => json_encode($orderItems, JSON_UNESCAPED_UNICODE),
        );
        $response = $this->request();
        Log_Base::save('Taobao_Verificate_Response','['.date('Y-m-d H:i:s').'] OrderId ['.$order['id'].']'.$response);
        if(!empty($response))
        {
            $response = json_decode($response,true);
            if($response !== false)
            {
                if(array_key_exists('code', $response) && $response['code'] == '200')
                    return true;
            }
        }
        return false;
    }

    public function cancel($order, $record, $device) {
        $this->url = '/common/v1/reverse';
        $this->params = array(
            'order_id' => $order['source_id'],
            'reverse_code' => $order['id'],
            'reverse_num' => 1,
            'token' => $order['source_token'],
            'source' => $order['source'],
            'posid' => $device['id'],
            'consume_serial_num' => $record['id']
        );
        $response = $this->request();
        Log_Base::save('Taobao_Cancel_Response','['.date('Y-m-d H:i:s').'] OrderId ['.$order['id'].']'.$response);
        if(!empty($response))
        {
            $response = json_decode($response,true);
            if($response !== false)
            {
                if(array_key_exists('code', $response) && $response['code'] == '200')
                    return true;
            }
        }
        return false;
    }

    //通知淘宝已发码接口
    public function send($order){
        $this->url = '/common/v1/send';
        $this->params = array(
            'id' => $order['id'],
            'nums' => $order['nums'],
            'orderId' => $order['source_id'],
            'token' => $order['source_token'],
            'source' => $order['source'],
        );
        $response = $this->request();
        if(!empty($response))
        {
            $response = json_decode($response,true);
            if($response !== false)
            {
                if(array_key_exists('code', $response) && $response['code'] == '200')
                    return true;
            }
            Log_Base::save('Taobao_Payment','['.date('Y-m-d H:i:s').'] OrderId ['.$order['id'].']'.var_export($response,true));
        }
        return false;
    }
}
