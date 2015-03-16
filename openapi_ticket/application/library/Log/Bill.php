<?php
/**
 * @author  mosen
 */
class Log_Bill extends Log_Base
{
	protected $tblname = 'log_bill';
    
	public function getTable() {
        return $this->tblname;
    }

    public function add($data) {
  		$item = array();
		$item['type'] = intval($data['type']);
        $item['bill_id'] = intval($data['bill_id']);
		$item['content'] = $data['content'];
    	$this->write($item);
    }
}
