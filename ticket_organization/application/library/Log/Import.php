<?php
/**
 * SQL操作日志
 * @author  mosen
 */
class Log_Import
{
	protected static $dbname = 'log';
	protected static $db;

	public static function getDb() {
		if(!self::$db) {
			self::$db = Db_Mysql::factory(self::$dbname);
		}
		return self::$db;
	}

	public static function run($sql) {
		try {
			//
			self::getDb()->query($sql);
		} catch(Exception $e) {
			//
			$file = 'error_'. date('Ymd') . '.sql';
			$sql = rtrim($sql, ';').';';

			Log_Base::save($file, $sql);
			echo '[' . date('Y-m-d H:i:s') . ']' . $e->getMessage();
		}
	}
}

