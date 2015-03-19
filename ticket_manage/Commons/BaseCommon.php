<?php
/**
 *  
 * 
 * 2013-09-09
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class BaseCommon extends Common
{
	protected static $err = array();
	protected $_code = array(
		'-1'  => '{"errors":{"msg":["null post"]}}',
	);

	//获取用户自定义的错误信息
	protected function _getUserError($code)
	{
		return $this->_code[$code];
	}

	protected function hasError($data) {
		if (!is_array($data)) {
			$data = json_decode($data, true);
		}
		return is_array($data) && $data['errors'] ? true : false;
	}

	//组织url后续地址
	protected function _getExtenseUrl($param = array())
	{
		$arg       = '';
		if($param){
			// while (list ($key, $val) = each ($param)) {
			// 	$arg.=$key."=".$val."&";
			// }
			// //去掉最后一个&字符
			// $arg = substr($arg,0,count($arg)-2);
			$arg = http_build_query($param);
		}
		return $arg;
	}

	/**
	 * 获取数据中心api地址
	 * @param string $mappingKey PI_ITOURISM_API_MAPPING的key值
	 * @param string $replaceArg1 apimapping中需要替换的第一个参数 可空
	 * @param string $replaceArg2 apimapping中需要替换的第二个参数 可空
	 * @return string
	 */
	public function getItourismApiUrl($mappingKey, $replaceArg1 = '', $replaceArg2 = '')
	{
		$itourismApiConf = unserialize(PI_ITOURISM_API_MAPPING);
		$url             = $itourismApiConf[$mappingKey]['targetUrlPrefix'];
		$url             = str_replace('<arg1>', $replaceArg1, $url);
		$url             = str_replace('<arg2>', $replaceArg2, $url);
		return $url;
	}

	/**
	 * 获取数据中心api的提交方式
	 * @param string $mappingKey PI_ITOURISM_API_MAPPING的key值
	 * @return string
	 */
	public function getItourismApiMethod($mappingKey)
	{
		$itourismApiConf = unserialize(PI_ITOURISM_API_MAPPING);
		$method          = $itourismApiConf[$mappingKey]['method'];
		return $method;
	}

	/**
	 * 获取数据中心api的验证数据
	 * @return string
	 */
	public function getItrouIsmAuthHeader()
	{
		$itourismApiAuth = unserialize(PI_ITOURISM_API_AUTH);
		$authStr         = $itourismApiAuth['username'].':'.$itourismApiAuth['password'];
		$header          = array('Authorization' => 'Basic '.base64_encode($authStr));
		return $header;
	}

	/**
	 * 通用的请求，如无特殊需求可直接调用
	 * @param string $mappingKey PI_ITOURISM_API_MAPPING的key值
	 * @param string $replaceArg2 需要替换的第二个参数 可空
	 * @param string $replaceArg1 apimapping中需要替换的第一个参数 可空
	 * @param string $replaceArg2 apimapping中需要替换的第二个参数 可空
	 * @return string
	 */
	public function commonItourismRequest($mappingKey, $param = array(), $replaceArg1 = '', $replaceArg2 = '')
	{
		$url          = $this->getItourismApiUrl($mappingKey, $replaceArg1, $replaceArg2);
		$method       = $this->getItourismApiMethod($mappingKey);

		//假如是get的话需要将参数组到url上面
		if(strtolower($method) == 'get' && $param) {
			if(strpos('?', $url)) {
				$url .= '&'.$this->_getExtenseUrl($param);
			} else {
				$url .= '?'.$this->_getExtenseUrl($param);
			}
		}

		$headers      = $this->getItrouIsmAuthHeader();
		$apiObj       = new Api();
		$result       = $apiObj->curl($url, $param, $method, $headers);
		$responseCode = $apiObj->responseCode;
		$resultArr    = json_decode($result, 1);
		if($responseCode == '401') {
			return Api::sendJsonResponse('fail', '401', '错误的验签');
		} elseif($responseCode == '201') {
			return Api::sendJsonResponse('succ', '201');
		} elseif($resultArr['errors']) {
			$errors = '';
			foreach($resultArr['errors'] as $key => $value) {
				if(is_array($value)) {
					$errors .= $key.':'.implode(';', $value).'。';
				} else {
					$errors .= $key.':'.$value.'。';
				}
			}
			return Api::sendJsonResponse('fail', '400', $errors);
		} elseif($resultArr['data']) {
			return Api::sendJsonResponse('succ', '200', '', $resultArr);
		} else {
			return Api::sendJsonResponse('fail', '400', '未知错误');
		}
	}

	/**
	 * 生成日志文件，自动创建目录
	 * @param $file
	 * @param $logs
	 * @return unknown_type
	 */
	protected function _logAllToFile($file , $logs )
	{
		$file     = str_replace( '\\','/',$file );
		$fileList = explode( '/',$file );
		$fc       = count( $fileList );
		$tf       = '';
		for($index = 0; $index < ($fc - 1); $index++) {
			$tf .= $fileList[$index].'/';
		}
		if(!is_dir($tf)) mkdir($tf,0777,true);
		if(!file_exists(substr($file,1))) {
			file_put_contents( $tf.'/'.$fileList[$fc-1] , $logs , FILE_APPEND | LOCK_EX );
		}
	}

	protected function _getLogFile($method)
	{
		$this->_logBasePath = PI_APP_ROOT.'Logs/'.strtolower(get_class($this)).'/';
		$logFile  = $this->_logBasePath.strtolower($method).'/'.date('Y-m-d', time());
		return $logFile;
	}

	protected function setLog($method, $msg)
	{
		$logFile  = $this->_getLogFile($method);
		$this->_logAllToFile($logFile, $msg);
	}
}