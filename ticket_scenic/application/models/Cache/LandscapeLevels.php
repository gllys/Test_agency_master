<?php

/**
 * @date 2014-10-21 09:43:18
 * @author Gencache
 */
class Cache_LandscapeLevelsModel extends Base_Model_Abstract
{
	public function getLevel($id = null) {
		static $configs = array (  0 =>   array (    'id' => '0',    'name' => '非A景区',    'rank' => '0',  ),  1 =>   array (    'id' => '1',    'name' => 'A景区',    'rank' => '0',  ),  2 =>   array (    'id' => '2',    'name' => 'AA景区',    'rank' => '0',  ),  3 =>   array (    'id' => '3',    'name' => 'AAA景区',    'rank' => '0',  ),  4 =>   array (    'id' => '4',    'name' => 'AAAA景区',    'rank' => '0',  ),  5 =>   array (    'id' => '5',    'name' => 'AAAAA景区',    'rank' => '0',  ),);
		if (!is_null($id))
			return $configs[$id];
		else	
		return $configs;
	}

}