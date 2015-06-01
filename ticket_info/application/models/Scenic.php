<?php

/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-11-20
 * Time: 下午2:56
 */
class ScenicModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_scenic';
    protected $url = '/v1/landscape/lists';
    protected $method = 'POST';

    public function getScenicList($params)
    {
        $this->params = $params;
        $key = 'ScenicList_' . md5(serialize($this->params));
        return $this->getCache($key, 10);
    }

    public function getPoiList($params)
    {
        $this->params = $params;
        $this->url = '/v1/poi/lists';
        $key = 'ScenicPoiList_' . md5(serialize($this->params));
        return $this->getCache($key, 10);
    }

    public function getScenicInfo($params)
    {
        if (empty($params) || empty($params['id'])) {
            return false;
        }
        $this->params = $params;
        $this->url = '/v1/landscape/detail';
        $key = 'ScenicInfo_' . $params['id'];
        return $this->getCache($key, 10);
    }

    //按景区ID获取图片列表
    public function imagesByIds($landscape_ids)
    {
        if (empty($landscape_ids)) {
            return false;
        }
        $this->params = array('landscape_id' => implode(',', $landscape_ids));
        $this->url = '/v1/LandscapeImage/lists';
        $key = 'ScenicImagesByIds_' . md5(json_encode($landscape_ids));
        $r = $this->getCache($key, 10);
        if (!empty($r['body']['data'])) {
            $images = $r['body']['data'];
            $arrByIds = array();
            foreach ($images as $v) {
                $arrByIds[$v['landscape_id']][] = $v['url'];
            }

            return $arrByIds;
        } else {
            return array();
        }
    }

    private function getCache($key, $expire = 10)
    {
        $data = Cache_Memcache::factory()->get($key);
        if (empty($data)) {
            $data = json_decode($this->request(), true);
            if (!empty($data) && !empty($data['body'])) {
                Cache_Memcache::factory()->set($key, $data, $expire);
            }
        }
        return $data;
    }

}
