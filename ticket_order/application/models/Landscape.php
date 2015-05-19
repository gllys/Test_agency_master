<?php
class LandscapeModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_scenic';
    protected $url = '/v1/landscape/detail';
    protected $method = 'POST';

    public function getIdsByName($name){
        if(!$name)
            return false;
        $this->url = '/v1/landscape/listByName';
        $this->params = array('name'=>$name,'fields'=>'id');
        $r = $this->request();
        $r = json_decode($r, true);
        if(!$r || empty($r['body']))
            return false;
        return array_keys($r['body']);
    }

    public function getDetail($id){
        if(!$id)
            return array();
        $this->params = array('id'=>$id);
        $info = $this->request();
        $info = json_decode($info, true);
        if(!$info || empty($info['body']))
            return false;
        return $info['body'];
    }

    //按景点id获取景点名称列表
    public function poiLists($ids,$field = '*'){
        if(!$ids)
            return array();
        $this->url = '/v1/poi/lists';
        $this->params = array('ids'=>implode(',',$ids),'items'=>count($ids),'fields'=>$field);
        $info = $this->request();
        $info = json_decode($info, true);
        if(!$info || empty($info['body']['data']))
            return false;
        return $info['body']['data'];
    }
}