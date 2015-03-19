<?php

/**
 * Class PoiModel
 */
class PoiModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_scenic';
    protected $url = '/v1/poi/detail';

    public function getInfo($poi_id){
        if(!$poi_id) return false;
        $this->url = '/v1/poi/detail';
        $this->params = array('id'=>$poi_id);
        $info = json_decode($this->request(),true);
        return $info && $info['code']=='succ' ? $info['body'] : false;
    }

    public function getPoiList($landscape_id) {
    	$return = array();
    	$this->url = '/v1/poi/lists';
    	$this->params = array('landscape_ids'=>$landscape_id,'status'=>1,'items'=>50);
        $info = json_decode($this->request(),true);
        $list = $info && $info['code']=='succ' ? $info['body']['data'] : array();
        foreach($list as $row) {
        	$return[$row['id']] = $row;
        }
        return $return;
    }
}
