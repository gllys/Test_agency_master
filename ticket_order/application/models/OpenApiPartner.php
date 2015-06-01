<?php

/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-4-24
 * Time: 下午3:57
 * 下单、退票对接大漠openapi
 * 下单步骤：1 下单（未支付），2支付并保存大漠订单信息到redis，3用定时脚本通知大漠获取大漠订单号，并将大漠订单号回写到订单记录
 * 下单支付后短信如何？
 * 核销步骤：1 大漠验票发出验票通知，2按收到通知信息进行核销操作
 * 退款步骤：1 分销商申请退票，2通知大漠退款，3按大漠反馈成功信息再作退款操作
 * 注：如果是自动退票，脚本需判断是否大漠订单，是大漠订单需通知大漠退票，反馈成功后本地才能通过退款申请
 */
class OpenApiPartnerModel extends Base_Model_Api
{
    protected $srvKey = 'openapi_supply';
    protected $url = '';
    protected $method = 'POST';
    protected $version = 'v1';

    //将支付后的订单添加至redis列队，以便定时脚本通知大漠
    public function orderToRds($order) {
        if(empty($order['partner_type']) || empty($order['partner_product_code'])) {
            return false;
        }
        Cache_Redis::factory()->rpush('OpenApiPartnerAddOrder' , $order['id']);
    }

    //"username" : xxx,"password" : xxxx,"key":xxx,"url" :xx,"cooperation_way":2 //合作方式...
    //实际参数视外部供应商而定
    private function getPartnerIndentify($supplier_id)
    {
        $partner_identify = array();
        $orgInfo = OrganizationModel::model()->getInfo($supplier_id);
        if (!empty($orgInfo) && !empty($orgInfo['partner_identify'])) {
            $partner_identify = json_decode($orgInfo['partner_identify'], true);
        }
        return $partner_identify;
    }

    /**
     * 大漠下单接口,返回bool值
     * @author zqf
     * @param $organization_id int
     * @param $partner_type int
     * @param $params array
     * @return bool
     */
    public function partnerAddOrder($order = array())
    {
        //return array('code'=>200,'message'=>'success','body'=>array('source_id'=>"1504077536005888"));
        if (empty($order['supplier_id']) || empty($order['partner_type']) || empty($order['partner_product_code'])
            || empty($order['nums']) || empty($order['use_day']) || empty($order['id'])
        ) {
            Log_Base::save('OpenApiPartner', '[' . date('Y-m-d H:i:s') . '] [addOrder] 参数不全: '.var_export($order,true)."\n");
            return false;
        }

        $partner_identify = $this->getPartnerIndentify($order['supplier_id']);
        if(empty($partner_identify)) {
            Log_Base::save('OpenApiPartner', '[' . date('Y-m-d H:i:s') . '] [addOrder] 身份识别信息有误: '.var_export($order,true)."\n");
            return false;
        }
        $this->url = '/common/order/add';
        $this->method='POST';
        $params = $this->params = array(
            'partner_type' => $order['partner_type'],
            'version' => $this->version,
            'identify' => json_encode($partner_identify,JSON_UNESCAPED_UNICODE),
            'body' => json_encode(array(
                'partner_product_code' => $order['partner_product_code'],
                'nums' => $order['nums'],
                'use_day' => $order['use_day'],
                'order_id' => $order['id'],
                'owner_name' => $order['owner_name'],
                'owner_mobile' => $order['owner_mobile'],
                'owner_card' => $order['owner_card'],
            ),JSON_UNESCAPED_UNICODE),
        );
        $response = $this->request(null,10);

        Log_Base::save('OpenApiPartner', '[' . date('Y-m-d H:i:s') . '] [addOrder] Params: ' . var_export($params, true) . "\nResponse: " . $response . "\n");
        if (!empty($response)) {
            $response = json_decode($response, true);
            if ($response !== false) {
                if (is_array($response) && array_key_exists('code', $response) && $response['code'] == '200') {
                    return $response;
                }
            }
        }
        return false;
    }

    /**
     * 通知合作伙伴退单，返回bool值
     * @param $order array
     * @param $nums int
     * @return bool
     * */
    public function partnerRefundOrder($order,$nums=0){
        if(empty($order['supplier_id']) || empty($order['partner_type'])  || empty($order['id']) || empty($order['partner_product_code']) || $nums<1) {
            Log_Base::save('OpenApiPartner', '[' . date('Y-m-d H:i:s') . '] [refundOrder] 参数不全: '.var_export($order,true)."\n");
            return false;
        }

        $partner_identify = $this->getPartnerIndentify($order['supplier_id']);
        if(empty($partner_identify)) {
            Log_Base::save('OpenApiPartner', '[' . date('Y-m-d H:i:s') . '] [refundOrder] 身份识别信息有误: '.var_export($order,true)."\n");
            return false;
        }

        $this->url = '/common/order/refund';

        $this->params = array(
            'partner_type' => $order['partner_type'],
            'version' => $this->version,
            'identify' => json_encode($partner_identify,JSON_UNESCAPED_UNICODE),
            'body' => json_encode(array(
                'partner_product_code' => $order['partner_product_code'],
                'nums' => $nums,
                'order_id' => $order['id'],
            ),JSON_UNESCAPED_UNICODE),
        );

        $response = $this->request(null,10);

        Log_Base::save('OpenApiPartner', '[' . date('Y-m-d H:i:s') . '] [refundOrder] Params: ' . var_export($this->params, true) . "\nResponse: " . $response . "\n");
        if (!empty($response)) {
            $response = json_decode($response, true);
            if ($response !== false) {
                if (array_key_exists('code', $response) && $response['code'] == '200') {
                    return true;
                }
            }
        }
        return false;
    }


}
