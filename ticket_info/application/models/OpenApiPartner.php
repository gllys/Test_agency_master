<?php

/**
 * 访问合作伙伴景区的接口，如：大漠
 * zqf
 * 2015-04-23
 */
class OpenApiPartnerModel extends Base_Model_Api
{
    protected $srvKey = 'openapi_supply';
    protected $url = '';
    protected $method = 'POST';
    protected $version = 'v1';

    //"username" : xxx,"password" : xxxx,"key":xxx,"url" :xx,"cooperation_way":2 //合作方式...
    //实际参数视外部供应商而定
    private function getPartnerIndentify($organization_id)
    {
        $partner_identify = array();
        $orgInfo = ApiOrganizationModel::model()->orgInfo($organization_id);
        if (!empty($orgInfo) && !empty($orgInfo['partner_identify'])) {
            $partner_identify = json_decode($orgInfo['partner_identify'], true);
        }
        return $partner_identify;
    }

    /**
     * 判断票种在哪一天是否可售接口,返回bool值
     * @author zqf
     * @param $organization_id int
     * @param $partner_type int
     * @param $partner_product_code string
     * @param $use_day date
     * @return bool
     */
    public function checkProduct($productInfo, $use_day = '')
    {
        if ($productInfo['organization_id'] <= 0 || $productInfo['partner_type'] <= 0 || $productInfo['partner_product_code'] == '') {
            return false;
        }
        if ($use_day == '') {
            $use_day = date("Y-m-d");
        }

        $partner_identify = $this->getPartnerIndentify($productInfo['organization_id']);

        if(empty($partner_identify)) {
            return false;
        }

        $this->url = '/common/product/checkProduct';

        $this->params = $params = array(
            'partner_type' => $productInfo['partner_type'],
            'version' => $this->version,
            'identify' => json_encode($partner_identify,JSON_UNESCAPED_UNICODE),
            'body' => json_encode(array(
                'partner_product_code' => $productInfo['partner_product_code'],
                'use_day' => $use_day,
            ),JSON_UNESCAPED_UNICODE),
        );
        $cacheKey = "OpenApiPartner_" . md5($this->url.json_encode($this->params));
        $response = Cache_Memcache::factory()->get($cacheKey);
        if (empty($response)) {
            $response = $this->request(null,10);
            if (!empty($response)) {
                Cache_Memcache::factory()->set($cacheKey, $response, 60);
            }
        }

        Log_Base::save('OpenApiPartner', '[' . date('Y-m-d H:i:s') . '] [checkProduct] Params: ' . var_export($params, true) . "\nResponse: " . $response . "\n");
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
