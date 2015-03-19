<?php
/**
 * 锁操作
 * @author  mosen
 */
class Util_Lock
{
	protected static $group = 'default';
	protected static $locks = array();
	
	/**
	 * [lock description]
	 * @param  [type]  $key [description]
	 * @param  integer $cd  [description]
	 * @return [type]       [description]
	 */
	public static function lock($key, $cd = 5) {
		$result = self::memcache()->add($key, 1, $cd);
		if($result)
			self::$locks[$key] = 1;
		
		return $result;
	}
	
	/**
	 * [unLock description]
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public static function unLock($key = null) {
		if (!$key) {
			foreach(self::$locks as $k => $v)
				self::memcache()->delete($k);
		} elseif(isset(self::$locks[$key])) {
			self::memcache()->delete($key);
			unset(self::$locks[$key]);
		}
	}

	/**
	 * [getMemcache description]
	 * @return [type] [description]
	 */
	public static function memcache() {
		return Cache_Memcache::factory(self::$group);
	}
}