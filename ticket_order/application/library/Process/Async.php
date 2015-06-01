<?php
/**
 * 异步操作
 * @author  mosen
 */
class Process_Async
{
	protected static $queue = 'async';

	public static function run($data) {
		try {
			Log_Base::save('async', 'run:'.var_export($data, true));
			$data = unserialize($data);
			call_user_func_array($data[0], $data[1]);
		} catch(Exception $e) {
			$msg = '[' . date('Y-m-d H:i:s') . ']' . $e->getMessage();
			Log_Base::save('async', $msg);
		}
	}

	/**
	 * Process_Async::send(array('Process_Async','test'),array('mosen'))
	 * @param  [type] $cls    [description]
	 * @param  [type] $method [description]
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public static function send($call, $params = array()) {
		$data = serialize(array($call, $params));
		Log_Base::save('async', 'send:'.var_export(array($call, $params), true));
		return Util_Queue::send(self::$queue, $data);
	}

	public static function presend($call, $params = array()) {
		$data = serialize(array($call, $params));
		Log_Base::save('async', 'presend:'.var_export(array($call, $params), true));
		return Util_Queue::presend(self::$queue, $data);
	}
	
}

