<?php
/**
 * api基类 
 *
 * 2013-10-16 1.0 liuhe 创建
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class Api
{
	//api http返回值  
	public $responseCode;

	//用户自定义状态码
	static public $userCode = array(
		'200'  => array('status' => 'succ', 'code' => 200, 'msg' => ''),
		'4001' => array('status' => 'fail', 'code' => 4001, 'msg' => '错误的请求协议'),
		'4002' => array('status' => 'fail', 'code' => 4002, 'msg' => '错误的请求方法'),
		'4003' => array('status' => 'fail', 'code' => 4003, 'msg' => '账号参数错误'),
		'4004' => array('status' => 'fail', 'code' => 4004, 'msg' => '对不起，您输入的账号不存在，请重新输入。'),
		'4005' => array('status' => 'fail', 'code' => 4005, 'msg' => '对不起，您输入的账号密码不匹配，请重新输入。'),
		'4006' => array('status' => 'fail', 'code' => 4006, 'msg' => '参数错误'),
		'4007' => array('status' => 'fail', 'code' => 4007, 'msg' => '参数不能为空'),
		'4008' => array('status' => 'fail', 'code' => 4008, 'msg' => '无数据'),
	);

	//http对应状态
	static public $httpCode = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => '(Unused)',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	);

	//初始操作
	public function init()
	{
		$this->load = new Load();
	}

	//返回值处理 如有特殊需求须重载此方法
	public static function sendJsonResponse($status = '', $code = '', $msg ='', $data = array())
	{
		if($data){
			return json_encode(array('status' => $status, 'code' =>$code, 'msg' => $msg, 'data' => $data));
		}else{
			return json_encode(array('status' => $status, 'code' =>$code, 'msg' => $msg));
		}
		
	}

	/*
	 * CURL调用
	 * 
	 * @param string $url    url路径
	 * @param array  $param  参数
	 * @param string $method 请求类型
	 * @param string $headers 头信息
	 * @param string $cookie cookie，多个用;隔开
	 * @return 
	 */
	public function curl($url, $param, $method = 'POST', $headers = array(), $cookie = '')
	{
		// 初始化
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);

		//是否需要带cookie过去
		if($cookie){
			curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		}

		//不同的method不同的参数
		if (strtoupper($method) == 'POST'){
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
		}elseif(strtoupper($method) == 'PUT'){
			$headers = array_merge($headers, array('X-HTTP-Method-Override' => 'PUT'));
			curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
		}elseif(strtoupper($method) == 'DELETE'){
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}else{
			curl_setopt($curl, CURLOPT_POST, FALSE);
		}

		if($headers) {
			$newHeaders = array();
			foreach((array)$headers as $k=>$v){
				$newHeaders[] = $k.': '.$v;
			}
		}

		//头信息
		curl_setopt($curl, CURLOPT_HTTPHEADER, $newHeaders);
		// 执行输出
		$info = curl_exec($curl);

		//状态码
		$this->responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		//是否需要包含头部输出
		return $info;
	}

	//组织url后续地址
	protected function _getExtenseUrl($param = array())
	{
		$extenseUrl       = '';
		if($param){
			$extenseUrl .= '?';
			foreach($param as $key => $value){
				$extenseUrl .= $key.'='.$value.'&';
			}
		}
		return $extenseUrl;
	}

	/** 
	  * 获取用户自定义的错误信息, 如接口需要的参数不同可重载此方法
	  * @param string $code 状态码
	  * @return json
	  */
	protected function _getUserError($code)
	{
		return json_encode(self::$userCode[$code]);
	}
}