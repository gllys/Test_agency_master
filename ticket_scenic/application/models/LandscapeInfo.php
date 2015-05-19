<?php

/**
 * Class TestapiModel
 */
class LandscapeInfoModel extends Base_Model_Api
{
	protected $srvKey = 'itourism-api';
    protected $method = 'GET';
    protected $useSign  = 0;
    protected $auth = array('username'=>'itourism-distribution-api','password'=>'itourism-distribution-api');
    
    public function getList($name, $ids, $page=1,$items=15,$showChild=false) {
    	$where['name'] = 'like_%'.$name.'%';
        !$showChild && $where['parent_id'] = 'null_true';
    	$this->url = '/advanced/landscapes/?'.$this->getFilter($where, $ids).'&page='.$page.'&items='.$items;
        // echo $this->url;
        $cacheKey = $this->url;
        $data = Cache_Memcache::factory()->get($cacheKey);
        if(empty($data)) {
            $data = $this->request($this->auth);
            $data = json_decode($data, true);
            Cache_Memcache::factory()->set($cacheKey,$data,3);
        }
        return $data;
    }

    protected function getFilter($where, $ids) {
    	$tmp = array();
    	foreach($where as $key=>$value) {
    		$tmp[] = $key.':'.urlencode($value);
    	}
    	if ($ids) $tmp[] = 'id:in_'.implode('_', $ids);
    	return 'filter='.implode(',', $tmp);
    }

    public function getInfo($id=0) {
    	$this->url = '/advanced/landscapes/'.$id;
        $cacheKey = $this->url;
        $data = Cache_Memcache::factory()->get($cacheKey);
        if(empty($data)) {
            $data = $this->request($this->auth);
            $data = json_decode($data, true);
            Cache_Memcache::factory()->set($cacheKey,$data,3);
        }
        return $data ? $data['data'][0] : array();
    }
}

// ["GET","",{"filter":"name:like_%\u6e29\u6cc9%,parent_id:null_true","items":10,"page":1,"embed":"districts","link":""},false] []