<?php

class DeviceModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'equipment';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|equipmentModel|';
	
	public function getTable() {
        return $this->tblname;
    }

    public function getByCode($code) {
    	$list = $this->search(array('code'=>$code));
    	return $list ? reset($list) : false;
    }
    
}