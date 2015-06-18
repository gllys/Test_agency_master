<?php

/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-5-21
 * Time: 下午4:23
 * 针对OTA回调通知
 */
class OtaCallbackModel extends Base_Model_Api
{
    protected $srvKey = 'openapi_ticket';
    protected $url = '';
    protected $method = 'POST';

    //退款通知接口
    public function productChanged($params)
    {

        $this->url = '/common/notice/productChanged';
        $this->params = array(
            'product_id' => $params['product_id'], //产品id
            'code' => $params['code'], //产品对接码（agency_product表的code字段）
            'agency_id' => (isset($params['agency_id']) && intval($params['agency_id'])>0) ? intval($params['agency_id']) : 0, //分销商id
            'source' => $params['source'], //外部来源 0默认 1淘宝 2八爪鱼 3同程 4途牛 5驴妈妈 6携程 7景点通 8度周末 9途家 10去哪儿 13淘在路上 15美团
            'Timestamp' => time(), //发起请求时的时间戳
        );
        if (isset($params['is_sale'])) { //上下架状态(0下架1上架)（若不传，表示仅产品信息发生变化）
            $this->params['is_sale'] = $tmp['is_sale'] = $params['is_sale'] > 0 ? 1 : 0;
        }

        Log_Base::save('OtaCallback_productChanged', "[" . date('Y-m-d H:i:s') . "] [Require] Url:".$this->getSrvUrl() . $this->url."\nParams: " . var_export($this->params, true));
        $response = $this->request(null, 10);
        Log_Base::save('OtaCallback_productChanged', "[" . date('Y-m-d H:i:s') . "] [Response] " . $response);
        if (!empty($response)) {
            $response = json_decode($response, true);
            if ($response !== false) {
                if (array_key_exists('code', $response) && $response['code'] == '200')
                    return true;
            }
        }
        return false;
    }

    //异步执行通知接口
    public static function productChangedAsync($params)
    {
        if (!empty($params['product_id']) && !empty($params['code']) && !empty($params['source'])) {
            OtaCallbackModel::model()->productChanged($params);
        }
    }

}
