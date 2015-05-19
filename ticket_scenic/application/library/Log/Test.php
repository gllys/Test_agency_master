<?php
/**
 * 测试LOG
 * @author  mosen
 */
class Log_Test extends Log_Base
{
	protected $tblname = 'log_test';
    public static $type = array('CREATE'=>1,'UPDATE'=>2,'DEL'=>3);
    
	public function getTable() {
        return $this->tblname;
    }

    public function add($data) {
  		$item = array();
		$item['type'] = intval($data['type']);
		$item['num'] = is_numeric($data['num']) ? $data['num'] : intval($data['num']);
		$item['content'] = $data['content'];
    	$this->write($item);
    }

    public function addList($data) {
    	$items = array();
    	foreach($data as $value) {
    		$item = array();
			$item['type'] = intval($value['type']);
			$item['num'] = is_numeric($value['num']) ? $value['num'] : intval($value['num']);
			$item['content'] = $value['content'];
			$items[] = $item;
    	}
  		
    	$this->write($items);
    }
}
