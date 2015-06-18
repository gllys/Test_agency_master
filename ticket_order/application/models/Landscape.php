<?php

class LandscapeModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_scenic';
    protected $url = '/v1/landscape/detail';
    protected $method = 'POST';

    public function getList($params = array())
    {
        $this->url = '/v1/landscape/lists';
        $this->params = $params;

        $this->preCacheKey = 'cache|LandscapeModel|';

        $cachekey = 'LandscapeModel_GetList_' . md5(json_encode($params));
        $r = $this->customCache($cachekey);
        if ($r == null) {
            $r = $this->customCache($cachekey, json_decode($this->request(), true));
        }

        if (!$r || empty($r['body']))
            return false;
        return $r['body'];
    }

    public function getIdsByName($name)
    {
        if (!$name)
            return false;
        $this->url = '/v1/landscape/listByName';
        $this->params = array('name' => $name, 'fields' => 'id');
        $r = $this->request();
        $r = json_decode($r, true);
        if (!$r || empty($r['body']))
            return false;
        return array_keys($r['body']);
    }

    public function getDetail($id)
    {
        if (!$id)
            return array();
        $this->params = array('id' => $id);
        $info = $this->request();
        $info = json_decode($info, true);
        if (!$info || empty($info['body']))
            return false;
        return $info['body'];
    }

    //按景点id获取景点名称列表
    public function poiLists($ids, $field = '*')
    {
        if (!$ids)
            return array();
        $this->url = '/v1/poi/lists';
        $this->params = $params = array('ids' => implode(',', $ids), 'items' => count($ids), 'fields' => $field);

        $this->preCacheKey = 'cache|PoiModel|';

        $cachekey = 'PoiModel_PoiLists_' . md5(json_encode($params));
        $info = $this->customCache($cachekey);
        if ($info == null) {
            $info = $this->customCache($cachekey, json_decode($this->request(), true));
        }

        if (!$info || empty($info['body']['data']))
            return false;
        return $info['body']['data'];
    }
}