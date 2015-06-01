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
        if(empty($order) || empty($refundApplyInfo) || $order['local_source']!=1 || $order['source']<=0) {
            return true;
        }

        $this->url = '/common/notice/refunded';
        $this->params = $params = array(
            'refund_id' => $refundApplyInfo['id'],
            'order_id' => $order['id'],
            'source_id' => $order['source_id'],
            'status' => $succ==true ? 'success' : 'failed',
            'nums' => $refundApplyInfo['nums'],
            'remark' => $refundApplyInfo['reject_reason']===null?'':$refundApplyInfo['reject_reason'],
            'refund_money'=>$refundApplyInfo['money'], //退款金额
            'refund_fee'=>0, //退款费用
            'token' => $order['source_token'],
            'source' => $order['source'],
            'Timestamp' => $refundApplyInfo['updated_at'],
        );

        //获取退款费用
        $param = array(
            'product_id'=>$order['product_id'],
            'agency_id'=> $order['distributor_id'],
            'source'=> $order['source'],
        );

        $otaProductInfo = ApiAgencyProductModel::model()->detail($param);
        if(is_array($otaProductInfo) && isset($otaProductInfo['extra']['refund_fee'])) {
            $this->params['refund_fee'] = doubleval($otaProductInfo['extra']['refund_fee']);
        }

        $response = $this->request(null,10);
        Log_Base::save('OtaCallback_Refund', "[".date('Y-m-d H:i:s')."]Params: ".var_export($params,true)."\nResult: " . $response);
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
