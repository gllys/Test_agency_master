<?php

/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-5-12
 * Time: 下午3:10
 */
class OtaCallbackModel extends Base_Model_Api
{
    protected $srvKey = 'openapi_ticket';
    protected $url = '';
    protected $method = 'POST';

    //退款通知接口
    public function refund($order, $refundApplyInfo, $succ = true)
    {
        $this->url = '/common/notice/refunded';
        $this->params = $params = array(
            'refund_id' => $refundApplyInfo['id'],
            'order_id' => $order['id'],
            'source_id' => $order['source_id'],
            'status' => $succ==true ? 'success' : 'failed',
            'nums' => $refundApplyInfo['nums'],
            'remark' => $refundApplyInfo['reject_reason']===null?'':$refundApplyInfo['reject_reason'],
            'token' => $order['source_token'],
            'source' => $order['source'],
            'Timestamp' => $refundApplyInfo['updated_at'],
        );
        $response = $this->request(null,10);
        Log_Base::save('OtaCallback_Refund', "[".date('Y-m-d H:i:s')."]Params: ".var_export($params,true)."\nResult: " . var_export($response, true));
        if (!empty($response)) {
            $response = json_decode($response, true);
            if ($response !== false) {
                if (array_key_exists('code', $response) && $response['code'] == '200')
                    return true;
            }
        }
        return false;
    }

}
