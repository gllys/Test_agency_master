<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 2015/04/20
 * Time: 13:34
 * 需要用的产品相关API
 */

class ApiProductModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_info';
    protected $url = '/v1/subscribes/count';
    protected $method = 'POST';

    public function subscribeCount($org_id) //订阅记录数
    {
        if (!$org_id)
            return false;
        $r = Cache_Memcache::factory()->get('subscribeCount_'.$org_id);
        if(empty($r)) {
            $this->url = '/v1/subscribes/count';
            $this->params = array('organization_id' => $org_id);
            $r = json_decode($this->request(), true);
            Cache_Memcache::factory()->set('subscribeCount_'.$org_id,$r,3);
        }
        if (!$r || empty($r['body']['pagination']))
            return false;
        return $r['body']['pagination']['count'];
    }
}