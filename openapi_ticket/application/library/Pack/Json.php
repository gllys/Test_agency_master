<?php
/**
 * JSON
 * @author  mosen
 */
class Pack_Json
{
	/**
	 * [encode description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public static function encode($data, $type = NULL) {
        if($type){
            return json_encode($data, $type);
        }else{
            return json_encode($data);
        }
	}
	
	/**
	 * [decode description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public static function decode($data) {
		return json_decode($data, true);
	}
}

