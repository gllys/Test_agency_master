<?php
/**
 * MSGPACK
 * @author  mosen
 */
class Pack_Msgpack
{
	/**
	 * [encode description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public static function encode($data) {
		if(!function_exists('msgpack_pack'))
			throw new Exception("msgpack extension not found.");
		return msgpack_pack($data);
	}
	
	/**
	 * [decode description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public static function decode($data) {
		if(!function_exists('msgpack_unpack'))
			throw new Exception("msgpack extension not found.");
		return msgpack_unpack($data);
	}
}

