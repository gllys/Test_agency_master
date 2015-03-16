<?php
class ScenicDifferenceModel extends Base_Model_Abstract
{
	protected $dbname = 'itourism';
    protected $tblname = 'scenic_difference';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|ScenicDifferenceModel|';
    
	 public function getTable() 
	 {
	      return $this->tblname;
	 }

}