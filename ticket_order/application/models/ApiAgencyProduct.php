<?php

/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-5-21
 * Time: 下午3:11
 */
class ApiAgencyProductModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_info';
    protected $url = '/v1/Agencyproduct/detail';
    protected $method = 'POST';

    public function detail($params)
    {
        if (empty($params['code']) && (empty($params['agency_id']) || empty($params['product_id']) || empty($params['source']))) {
            return false;
        }
        $this->preCacheKey = 'cache|AgencyProductModel|';

        $this->url = '/v1/Agencyproduct/detail';
        $this->params = $params;

        $cachekey = 'AgencyproductDetail_' . md5(json_encode($params));
        $r = $this->customCache($cachekey);
        if ($r == null) {
            $r = $this->customCache($cachekey, json_decode($this->request(), true));
        }

        if (empty($r) || empty($r['body']))
            return false;
        return $r['body'];
    }


}