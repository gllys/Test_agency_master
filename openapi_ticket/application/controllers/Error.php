<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author root
 */
class ErrorController extends Yaf_Controller_Abstract {

	//从2.1开始, errorAction支持直接通过参数获取异常
	public function errorAction($exception) {
		//1. assign to view engine

        Util_Logger::getLogger('error')->exception($exception, 'openapi_ticket');

		header("HTTP/1.0 404 Not Found");exit();
		//5. render by Yaf 
	}
}
