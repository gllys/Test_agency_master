<?php

/**
 * Class ConfigModel
 */
class ConfigModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'config';
    protected $pkKey = 'config_key';
    protected $preCacheKey = 'cache|ConfigModel|';
    
    public function getTable() {
        return $this->tblname;
    }

    public function getConfig($keys) {
    	$data = array();
    	if (!is_array($keys)) $keys = array($keys);
    	$list = ConfigModel::model()->search(array('config_key|in'=>$keys));
        if ($list) {
            foreach($list as $key => $item) {
                $data[$key] = $item['config_value'];
            }
        }
        return count($keys)==1 ? reset($data) : $data;
    }

    public function setConfig($items) {
    	$fields = array('config_key','config_value');
    	$items = array_merge(array($fields), $items);
    	return $this->replace($items);
    }
}
