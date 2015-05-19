<?php

class Tools {

	const FLAG_NUMERIC = 1;
	const FLAG_NO_NUMERIC = 2;
	const FLAG_ALPHANUMERIC = 3;

	/**
	 * 生成随机密码
	 *
	 * @param integer $length Desired length (optional)
	 * @param string  $flag   Output type (NUMERIC, ALPHANUMERIC, NO_NUMERIC)
	 *
	 * @return string Password
	 */
	public static function passwdGen($length = 8, $flag = self::FLAG_NO_NUMERIC) {
		switch ($flag)
		{
			case self::FLAG_NUMERIC:
				$str = '0123456789';
				break;
			case self::FLAG_NO_NUMERIC:
				$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case self::FLAG_ALPHANUMERIC:
			default:
				$str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
		}

		for ($i = 0, $passwd = ''; $i < $length; $i++)
			$passwd .= Tools::substr($str, mt_rand(0, Tools::strlen($str) - 1), 1);

		return $passwd;
	}

	/**
	 * 替换第一次出现的字符串
	 *
	 * @param     $search
	 * @param     $replace
	 * @param     $subject
	 * @param int $cur
	 *
	 * @return mixed
	 */
	public static function strReplaceFirst($search, $replace, $subject, $cur = 0) {
		return (strpos($subject, $search, $cur)) ? substr_replace($subject, $replace, (int)strpos($subject, $search, $cur), strlen($search)) : $subject;
	}

	/**
	 * 获取POST或GET的指定字段内容
	 *
	 * @param      $key
	 * @param bool $default_value
	 *
	 * @return bool|string
	 */
	public static function getValue($key, $default_value = false) {
		if (!isset($key) || empty($key) || !is_string($key))
			return false;
		$ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default_value));

		if (is_string($ret) === true)
			$ret = trim(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret))));

		return !is_string($ret) ? $ret : stripslashes($ret);
	}

	/**
	 * 判断POST或GET中是否包含指定字段
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public static function getIsset($key) {
		if (!isset($key) || empty($key) || !is_string($key))
			return false;

		return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
	}

	/**
	 * 判断是否为提交操作
	 *
	 * @param $submit
	 *
	 * @return bool
	 */
	public static function isSubmit($submit) {
		return (isset($_POST[$submit]) || isset($_POST[$submit . '_x']) || isset($_POST[$submit . '_y']) || isset($_GET[$submit]) || isset($_GET[$submit . '_x']) || isset($_GET[$submit . '_y']));
	}

	/**
	 * 过滤HTML内容后返回
	 *
	 * @param      $string
	 * @param bool $html
	 *
	 * @return array|string
	 */
	public static function safeOutput($string, $html = false) {
		if (!$html)
			$string = strip_tags($string);

		return @Tools::htmlentitiesUTF8($string, ENT_QUOTES);
	}

	public static function htmlentitiesUTF8($string, $type = ENT_QUOTES) {
		if (is_array($string))
			return array_map(array('Tools', 'htmlentitiesUTF8'), $string);

		return htmlentities((string)$string, $type, 'utf-8');
	}

	public static function htmlentitiesDecodeUTF8($string) {
		if (is_array($string))
			return array_map(array('Tools', 'htmlentitiesDecodeUTF8'), $string);

		return html_entity_decode((string)$string, ENT_QUOTES, 'utf-8');
	}

	/**
	 * 对POST内容进行处理
	 *
	 * @return array
	 */
	public static function safePostVars() {
		if (!is_array($_POST))
			return array();
		$_POST = array_map(array('Tools', 'htmlentitiesUTF8'), $_POST);
	}

	/**
	 * 显示错误信息
	 *
	 * @param string $string
	 * @param array  $error
	 * @param bool   $htmlentities
	 *
	 * @return mixed|string
	 */
	public static function displayError($string = 'Fatal error', $error = array(), $htmlentities = true) {
		if (DEBUG_MODE)
		{
			if (!is_array($error) || empty($error))
				return str_replace('"', '&quot;', $string) . ('<pre>' . print_r(debug_backtrace(), true) . '</pre>');
			$key = md5(str_replace('\'', '\\\'', $string));
			$str = (isset($error) AND is_array($error) AND key_exists($key, $error)) ? ($htmlentities ? htmlentities($error[$key], ENT_COMPAT, 'UTF-8') : $error[$key]) : $string;

			return str_replace('"', '&quot;', stripslashes($str));
		}
		else
		{
			return str_replace('"', '&quot;', $string);
		}
	}

	/**
	 * 打印出对象的内容
	 *
	 * @param      $object
	 * @param bool $kill
	 *
	 * @return mixed
	 */
	public static function dump($object, $kill = true) {
		echo '<pre style="text-align: left;">';
		print_r($object);
		echo '</pre><br />';
		if ($kill)
			die('END');

		return ($object);
	}

	public static function encrypt($passwd) {
		return md5(_COOKIE_KEY_ . $passwd);
	}

	public static function getToken($string) {
		return !empty($string) ? Tools::encrypt($string) : false;
	}

	/**
	 * 截取字符串，支持中文
	 *
	 * @param        $str
	 * @param        $max_length
	 * @param string $suffix
	 *
	 * @return string
	 */
	public static function truncate($str, $max_length, $suffix = '...') {
		if (Tools::strlen($str) <= $max_length)
			return $str;
		$str = utf8_decode($str);

		return (utf8_encode(substr($str, 0, $max_length - Tools::strlen($suffix)) . $suffix));
	}

	public static function cleanNonUnicodeSupport($pattern) {
		if (!defined('PREG_BAD_UTF8_OFFSET'))
			return $pattern;

		return preg_replace('/\\\[px]\{[a-z]\}{1,2}|(\/[a-z]*)u([a-z]*)$/i', "$1$2", $pattern);
	}

	/**
	 * 转换成小写字符，支持中文
	 *
	 * @param $str
	 *
	 * @return bool|string
	 */
	public static function strtolower($str) {
		if (is_array($str))
			return false;
		if (function_exists('mb_strtolower'))
			return mb_strtolower($str, 'utf-8');

		return strtolower($str);
	}

	/**
	 * 转换为int类型
	 *
	 * @param $val
	 *
	 * @return int
	 */
	public static function intval($val) {
		if (is_int($val))
			return $val;
		if (is_string($val))
			return (int)$val;

		return (int)(string)$val;
	}

	/**
	 * 计算字符串长度
	 *
	 * @param        $str
	 * @param string $encoding
	 *
	 * @return bool|int
	 */
	public static function strlen($str, $encoding = 'UTF-8') {
		if (is_array($str) || is_object($str))
			return false;
		$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
		if (function_exists('mb_strlen'))
			return mb_strlen($str, $encoding);

		return strlen($str);
	}

	public static function stripslashes($string) {
		if (get_magic_quotes_gpc())
			$string = stripslashes($string);

		return $string;
	}

	/**
	 * 转换成大写字符串
	 *
	 * @param $str
	 *
	 * @return bool|string
	 */
	public static function strtoupper($str) {
		if (is_array($str))
			return false;
		if (function_exists('mb_strtoupper'))
			return mb_strtoupper($str, 'utf-8');

		return strtoupper($str);
	}

	/**
	 * 截取字符串
	 *
	 * @param        $str
	 * @param        $start
	 * @param bool   $length
	 * @param string $encoding
	 *
	 * @return bool|string
	 */
	public static function substr($str, $start, $length = false, $encoding = 'utf-8') {
		if (is_array($str) || is_object($str))
			return false;
		if (function_exists('mb_substr'))
			return mb_substr($str, intval($start), ($length === false ? self::strlen($str) : intval($length)), $encoding);

		return substr($str, $start, ($length === false ? Tools::strlen($str) : intval($length)));
	}

	/**首字母大写
	 *
	 * @param $str
	 *
	 * @return string
	 */
	public static function ucfirst($str) {
		return self::strtoupper(self::substr($str, 0, 1)) . self::substr($str, 1);
	}

	public static function nl2br($str) {
		return preg_replace("/((<br ?\/?>)+)/i", "<br />", str_replace(array("\r\n", "\r", "\n"), "<br />", $str));
	}

	public static function br2nl($str) {
		return str_replace("<br />", "\n", $str);
	}

	/**
	 * 判断是否真为空
	 *
	 * @param $field
	 *
	 * @return bool
	 */
	public static function isEmpty($field) {
		return ($field === '' || $field === null);
	}

	public static function ceilf($value, $precision = 0) {
		$precisionFactor = $precision == 0 ? 1 : pow(10, $precision);
		$tmp = $value * $precisionFactor;
		$tmp2 = (string)$tmp;
		// If the current value has already the desired precision
		if (strpos($tmp2, '.') === false)
			return ($value);
		if ($tmp2[strlen($tmp2) - 1] == 0)
			return $value;

		return ceil($tmp) / $precisionFactor;
	}

	public static function floorf($value, $precision = 0) {
		$precisionFactor = $precision == 0 ? 1 : pow(10, $precision);
		$tmp = $value * $precisionFactor;
		$tmp2 = (string)$tmp;
		// If the current value has already the desired precision
		if (strpos($tmp2, '.') === false)
			return ($value);
		if ($tmp2[strlen($tmp2) - 1] == 0)
			return $value;

		return floor($tmp) / $precisionFactor;
	}

	public static function replaceSpace($url) {
		return urlencode(strtolower(preg_replace('/[ ]+/', '-', trim($url, ' -/,.?'))));
	}

	/**
	 * 判断是否64位架构
	 *
	 * @return bool
	 */
	public static function isX86_64arch() {
		return (PHP_INT_MAX == '9223372036854775807');
	}

	public static function convertBytes($value) {
		if (is_numeric($value))
			return $value;
		else
		{
			$value_length = strlen($value);
			$qty = (int)substr($value, 0, $value_length - 1);
			$unit = strtolower(substr($value, $value_length - 1));
			switch ($unit)
			{
				case 'k':
					$qty *= 1024;
					break;
				case 'm':
					$qty *= 1048576;
					break;
				case 'g':
					$qty *= 1073741824;
					break;
			}

			return $qty;
		}
	}

	/**
	 * 获取内存限制
	 *
	 * @return int
	 */
	public static function getMemoryLimit() {
		$memory_limit = @ini_get('memory_limit');

		return Tools::getOctets($memory_limit);
	}

	public static function getOctets($option) {
		if (preg_match('/[0-9]+k/i', $option))
			return 1024 * (int)$option;

		if (preg_match('/[0-9]+m/i', $option))
			return 1024 * 1024 * (int)$option;

		if (preg_match('/[0-9]+g/i', $option))
			return 1024 * 1024 * 1024 * (int)$option;

		return $option;
	}

	/**
	 * 从array中取出指定字段
	 *
	 * @param $array
	 * @param $key
	 *
	 * @return array|null
	 */
	public static function simpleArray($array, $key) {
		if (!empty($array) && is_array($array))
		{
			$result = array();
			foreach ($array as $k => $item)
			{
				$result[$k] = $item[$key];
			}

			return $result;
		}

		return null;
	}

	public static function object2array(&$object) {
		return json_decode(json_encode($object), true);
	}

	public static function cmpWord($a, $b) {
		if ($a['word'] > $b['word'])
		{
			return 1;
		}
		elseif ($a['word'] == $b['word'])
		{
			return 0;
		}
		else
		{
			return -1;
		}
	}

	/**
	 * HackNews热度计算公式
	 *
	 * @param $time
	 * @param $viewcount
	 *
	 * @return float|int
	 */
	public static function getGravity($time, $viewcount) {
		$timegap = ($_SERVER['REQUEST_TIME'] - strtotime($time)) / 3600;
		if ($timegap <= 24)
		{
			return 999999;
		}

		return round((pow($viewcount, 0.8) / pow(($timegap + 24), 1.2)), 3) * 1000;
	}

	public static function getGravityS($stime, $viewcount) {
		$timegap = ($_SERVER['REQUEST_TIME'] - $stime) / 3600;
		if ($timegap <= 24)
		{
			return 999999;
		}

		return round((pow($viewcount, 0.8) / pow(($timegap + 24), 1.2)), 3) * 1000;
	}

	/**
	 * 优化的file_get_contents操作，超时关闭
	 *
	 * @param      $url
	 * @param bool $use_include_path
	 * @param null $stream_context
	 * @param int  $curl_timeout
	 *
	 * @return bool|mixed|string
	 */
	public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 8) {
		if ($stream_context == null && preg_match('/^https?:\/\//', $url))
			$stream_context = @stream_context_create(array('http' => array('timeout' => $curl_timeout)));
		if (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url))
			return @file_get_contents($url, $use_include_path, $stream_context);
		elseif (function_exists('curl_init'))
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			$opts = stream_context_get_options($stream_context);
			if (isset($opts['http']['method']) && Tools::strtolower($opts['http']['method']) == 'post')
			{
				curl_setopt($curl, CURLOPT_POST, true);
				if (isset($opts['http']['content']))
				{
					parse_str($opts['http']['content'], $datas);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
				}
			}
			$content = curl_exec($curl);
			curl_close($curl);

			return $content;
		}
		else
			return false;
	}

	/**
	 * 以固定格式将数据及状态码返回手机端
	 *
	 * @param      $code
	 * @param      $data
	 * @param bool $native
	 */
	public static function exitJson($code, $data, $native = false) {
		if (!headers_sent())
		{
			header("Content-Type: application/json; charset=utf-8");
		}
		if (is_array($data) && $native)
		{
			self::walkArray($data, 'urlencode', true);
			echo(urldecode(json_encode(array('code' => $code, 'data' => $data))));
		}
		elseif (is_string($data) && $native)
		{
			echo(urldecode(json_encode(array('code' => $code, 'data' => urlencode($data)))));
		}
		else
		{
			echo(json_encode(array('code' => $code, 'data' => $data)));
		}
		ob_end_flush();
		exit;
	}

    /**
     * @param $code
     * @param $message
     * @param $body
     */
    public static function tbJson($code)
    {
        if (!headers_sent())
        {
            header("Content-Type: application/json; charset=utf-8");
        }
        echo json_encode(array('code'=>$code));
        ob_end_flush();
        exit;
    }

    /**
     * @param $code
     * @param $message
     * @param $body
     */
    public static function lsJson($code,$message="ok",$body=array())
    {
        if (!headers_sent())
        {
            header("Content-Type: application/json; charset=utf-8");
        }
        $code = $code ? 'succ':'fail';
        echo json_encode(array('code'=>$code,'message'=>$message,'body'=>(array)$body));
        ob_end_flush();
        exit;
    }

    public static function lsPost($param)
    {
        if(is_array($param))
            return isset($param)?$param:null;
        return trim($param);
    }

	/**
	 * 遍历数组
	 *
	 * @param      $array
	 * @param      $function
	 * @param bool $keys
	 */
	public static function walkArray(&$array, $function, $keys = false) {
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				self::walkArray($array[$key], $function, $keys);
			}
			elseif (is_string($value))
			{
				$array[$key] = $function($value);
			}

			if ($keys && is_string($key))
			{
				$newkey = $function($key);
				if ($newkey != $key)
				{
					$array[$newkey] = $array[$key];
					unset($array[$key]);
				}
			}
		}
	}

	public static function arrayUnique($array) {
		if (version_compare(phpversion(), '5.2.9', '<'))
			return array_unique($array);
		else
			return array_unique($array, SORT_REGULAR);
	}

	public static function arrayUnique2d($array, $keepkeys = true) {
		$output = array();
		if (!empty($array) && is_array($array))
		{
			$stArr = array_keys($array);
			$ndArr = array_keys(end($array));

			$tmp = array();
			foreach ($array as $i)
			{
				$i = join("¤", $i);
				$tmp[] = $i;
			}

			$tmp = array_unique($tmp);

			foreach ($tmp as $k => $v)
			{
				if ($keepkeys)
					$k = $stArr[$k];
				if ($keepkeys)
				{
					$tmpArr = explode("¤", $v);
					foreach ($tmpArr as $ndk => $ndv)
					{
						$output[$k][$ndArr[$ndk]] = $ndv;
					}
				}
				else
				{
					$output[$k] = explode("¤", $v);
				}
			}
		}

		return $output;
	}

	public static function transCase($str) {
		$str = preg_replace('/(e|ｅ|Ｅ)(x|ｘ|Ｘ)(p|ｐ|Ｐ)(r|ｒ|Ｒ)(e|ｅ|Ｅ)(s|ｓ|Ｓ)(s|ｓ|Ｓ)(i|ｉ|Ｉ)(o|ｏ|Ｏ)(n|ｎ|Ｎ)/is', 'expression', $str);

		Return $str;
	}

	/**
	 * @param        $url
	 * @param string $method
	 * @param null   $postFields
	 * @param null   $header
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function curl($url, $method = 'GET', $postFields = null, $header = null, $timeout = 5) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

		if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https")
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}

		switch ($method)
		{
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, true);
				if (!empty($postFields))
				{
					if (is_array($postFields) || is_object($postFields))
					{
						if (is_object($postFields))
							$postFields = Tools::object2array($postFields);
						$postBodyString = "";
						$postMultipart = false;
						foreach ($postFields as $k => $v)
						{
							if ("@" != substr($v, 0, 1))
							{ //判断是不是文件上传
								$postBodyString .= "$k=" . urlencode($v) . "&";
							}
							else
							{ //文件上传用multipart/form-data，否则用www-form-urlencoded
								$postMultipart = true;
							}
						}
						unset($k, $v);
						if ($postMultipart)
						{
							curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
						}
						else
						{
							curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
						}
					}
					else
					{
						curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
					}

				}
				break;
			default:
				if (!empty($postFields) && is_array($postFields))
					$url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($postFields);
				break;
		}
		curl_setopt($ch, CURLOPT_URL, $url);

		if (!empty($header) && is_array($header))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		$response = curl_exec($ch);
		if (curl_errno($ch))
		{
			throw new Exception(curl_error($ch), 0);
		}
		curl_close($ch);

		return $response;
	}

	/**
	 * 判断是否命令行执行
	 *
	 * @return bool
	 */
	public static function isCli() {
		if (isset($_SERVER['SHELL']) && !isset($_SERVER['HTTP_HOST']))
		{
			return true;
		}

		return false;
	}

    /**
     * 获得访客真实ip
     * @return mixed
     */
    public static function getIp()
    {
        $ip = '0.0.0.0';
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * 生成同步用的HASH值
     * @param unknown_type $serialno
     * @return HASH值
     */
    public static function generateSyncID($serialno) {
        list($usec, $sec) = explode(' ', microtime());
        $mtrand = (float) $sec + ((float) $usec * 100000);
        mt_srand($mtrand);
        $randval = mt_rand(1000,9000);
        $platform = getenv('FX_REMOTE');
        $platform = !empty($platform)?$platform:2;
        return md5($serialno.'|'.$platform.microtime().$randval);
    }

    public static function getPagination($params,$count=0){
        $pagination = array('count'=>$count);
        $pagination['current'] = isset($params['current']) ? intval($params['current']):1; //当前页
        $pagination['items'] = isset($params['items']) ? intval($params['items']):15; //每页记录数
        $pagination['current']<=0 && $pagination['current']=1;
        $pagination['items']<=0 && $pagination['items']=15;
        $pagination['total'] = ceil($count/$pagination['items']); //总页数
        $pagination['current'] = $pagination['current']>$pagination['total'] ? $pagination['total'] : $pagination['current'];
        $pagination['limit'] = ($pagination['items']*($pagination['current']-1)).",".$pagination['items']; //初始值
        return $pagination;
    }
}

?>
