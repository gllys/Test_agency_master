<?php

/**
 * 自定义函数库
 *
 * 2013-06-12 1.0 zhouli 创建
 *
 * @author  zhouli
 * @version 1.0
 */

/**
 * 字符串按字节截取，支持中英文
 *
 * @param  string $string 需要转换的字符串
 * @param  string $length 截取长度(字节数)
 * @param  string $etc    后缀
 * @param  string $countWords 是否判断字节
 * @return string
 */
function msubstr($string, $length = 80, $etc = '..', $countWords = TRUE) {
    if ($length == 0)
        return '';
    if (strlen($string) <= $length)
        return $string;
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $info);
    if ($countWords) {
        $j = 0;
        for ($i = 0; $i < count($info[0]); $i++) {
            $wordscut .= $info[0][$i];
            if (ord($info[0][$i]) >= 128)
                $j = $j + 2;
            else
                $j = $j + 1;
            if ($j >= $length)
                return $wordscut . $etc;
        }
        return join('', $info[0]);
    }
    return join('', array_slice($info[0], 0, $length)) . $etc;
}

/**
 * 获取项目路径
 *
 * @return string html
 */
function base_url($param = '') {
    switch ($param) {
        case '':
            return 'http://' . PI_APP_DOMAIN;
            break;
        case 'view':
            return 'http://' . PI_APP_DOMAIN . '/Views';
            break;
        case 'css':
            return 'http://' . PI_APP_DOMAIN . '/Views/css';
            break;
        case 'js':
            return 'http://' . PI_APP_DOMAIN . '/Views/js';
            break;
        case 'images':
            return 'http://' . PI_APP_DOMAIN . '/Views/images';
            break;
        default:
            return 'http://' . PI_APP_DOMAIN;
            break;
    }
}

/**
 * 页面重定向
 *
 * @param string $url     :将要跳转的URL,如果为空则自动返回到上一页.
 * @param string $message :消息文本
 * @param int $time       :页面显示停留的时间,单位:秒
 * @param string $tplFile :为空则使用框架自带的消息模板 未来扩展使用
 */
function redirect($url, $message = '', $time = 0, $tplFile = '') {
    if (empty($message))
        $message = "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            $param['url'] = $url;
            $param['message'] = $message;
            $param['time'] = $time;
            load_action('ViewCommon', 'redirect', $param);
        }
        exit();
    } else {
        $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $message;
        exit($str);
    }
}

/**
 * 获取公共页头
 *
 * @param  int $menu 当前菜单
 * @return string html
 */
function get_header($menu = 0) {
    load_action('ViewCommon', 'header', $menu);
}

/**
 * 获取公共页脚
 *
 * @return string html
 */
function get_footer() {
    load_action('ViewCommon', 'footer');
}

/**
 * 获取左侧菜单
 *
 * @return string html
 */
function get_menu($route = '') {
    load_action('ViewCommon', 'menu', $route);
}

/**
 * 获取面包屑
 *
 * @return string html
 */
function get_top_nav() {
    load_action('ViewCommon', 'topNav');
}

/**
 * 获取面包屑
 *
 * @return string html
 */
function get_crumbs($route = '') {
    load_action('ViewCommon', 'crumbs', $route);
}

/**
 * 获取城市信息
 *
 * 
 */
function get_city($code = 0) {
    load_action('ViewCommon', 'getCityInfo', $code);
}

/**
 * 获取用户列表
 *
 * @return string html
 */
// function get_user_list($data)
// {
// 	load_action('ViewCommon', 'userList', $data);
// }

/**
 * 获取用户信息
 *
 * @return string html
 */
function get_user_info() {
    $userInfo = load_action('UserCommon', 'getUserInfo');
    return $userInfo;
}

/*
 * CURL调用
 * 
 * @param string $url    url路径
 * @param array  $param  参数
 * @param string $method 请求类型
 * @param bool $header_opt 是否输出头信息
 * @param string $cookie cookie，多个用;隔开
 * @return 
 */

function curl($url, $param, $method = 'post', $header_opt = 0, $cookie = '') {
    // 初始化
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, $header_opt);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);

    if ($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    // post处理
    if ($method == 'post') {
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
    } else {
        curl_setopt($curl, CURLOPT_POST, FALSE);
    }

    // 执行输出
    $info = curl_exec($curl);
    curl_close($curl);
    return $info;
}

/**
 * 获取当前 ip
 * @return string $ip
 */
function getIp() {
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

if (!function_exists('array_flatten')) {

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array  $array
     * @return array
     */
    function array_flatten($array) {
        $return = array();
        array_walk_recursive($array, function($x) use (&$return) {
            $return[] = $x;
        });
        return $return;
    }

}


if (!function_exists('utf8_strlen')) {

    function utf8_strlen($string = null) {
        // 将字符串分解为单元  
        preg_match_all("/./us", $string, $match);
        // 返回单元个数  
        return count($match[0]);
    }

}

/**
 * 生成日志文件，自动创建目录
 * @param $file 文件的绝对路径
 * @param $logs 要写的内容
 * @return void
 */
function logMsgToFile($file, $logs) {
    $file = str_replace('\\', '/', $file);
    $fileList = explode('/', $file);
    $fc = count($fileList);
    $tf = '';
    for ($index = 0; $index < ($fc - 1); $index++) {
        $tf .= $fileList[$index] . '/';
    }
    if (!is_dir($tf))
        mkdir($tf, 0777, true);
    if (!file_exists(substr($file, 1))) {
        file_put_contents($tf . '/' . $fileList[$fc - 1], $logs, FILE_APPEND | LOCK_EX);
    }
}

//获取未读的消息的数量
function getUnreadMsgNums() {
    return load_action('MessageCommon', 'getUnreadMsgNums');
}

function convertSize($size) {
    $unit = array('B', 'K', 'M', 'G', 'T', 'P');
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . '' . $unit[$i];
}

/**
 * 得到数组的唯一值用于联合查询
 * @param $model 数组名称
 * @param $key 唯一值名称
 * array(array('id'=>1,'name'=>2),array('id'=>2,'name'=>2))=>arrayKey($model, 'id')=>array(1,2)
 */
function arrayKey($model, $key) {
    $_model = array();
    foreach ($model as $val) {
        $_model[] = $val[$key];
    }
    return $_model;
}

/**
 * 得到数组中可能是多个值，转换成唯一值用于选择得到
 * @param $model 数组名称
 * @param $key 唯一值名称
 * array(array('id'=>1,'name'=>2),array('id'=>2,'name'=>2))=>arrayKey($model, 'name')=>array('2'=>array(array('id'=>1,'name'=>2),array('id'=>2,'name'=>2)))
 */
function arrayByKeys($model, $key) {
    $_model = array();
    foreach ($model as $val) {
        $_model[$val[$key]][] = $val;
    }
    return $_model;
}

function output_monitor_select($rel, $indent = '', $c_id = 0, $disabled = '', $p_id = 0) {
	$opts = '';
	$has = false;
	$children = array();
	foreach ($rel as $item) {
		$_disabled = $disabled;
		if ($item['id'] == $c_id) {
			$_disabled = ' disabled="disabled"';
			$children = $item['children'];
			$has = true;
		}
		$opt = '<option value="'.$item['id'].'" '.$_disabled;
		if ($item['id'] == $p_id) {
			$opt .= ' selected="selected"';
		}
		if (isset($item['children'])) {
			list($grp, $sel, $_children) = output_monitor_select($item['children'], $indent.'|　', $c_id, $_disabled, $p_id);
			if ($sel) {
				$opt .= ' selected="selected"';
				$has = false;
			}
			$opt .= '>'.$indent.$item['name'].'</option>'.$grp;
			$children = !empty($children) ? $children : $_children;
		}
		else {
			$opt .= '>'.$indent.$item['name'].'</option>';
		}
		$opts .= $opt;
	}
	return array($opts, $has, $children);
}

/**
 * 当前时间
 * @param unknown_type $format
 */
function currentDateTime($format = 'Y-m-d H:i:s') {
    return date('Y-m-d H:i:s');
}

/**
 * 输出日志
 * @param $message
 * @return 无
 */
function outputLog($message, $filename = '', $level = '1') {
    //调试代码时
    if ((!defined('PI_DEBUG') || !constant('PI_DEBUG')) && !empty($filename) && $filename != 'error') {
        //return;
    }

    if (empty($filename)) {
        $filename   = PI_PROJECT_ROOT.'Logs/' . 'debug.'.date('Ymd').'.log';
    } else {
        $filename   = PI_PROJECT_ROOT.'Logs/' . $filename.'.'.date('Ymd').'.log';
    }

    $log = fopen($filename, 'a');
    $user =get_current_user();
    @chown($filename, $user);

    if (version_compare(substr(phpversion(),0,6), '5.3.10', '<=')) {
        $calledFrom     = debug_backtrace(false);
    } else {
        $calledFrom     = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $level);
    }
    $calledFromStr  = "FILE:".substr(str_replace(PI_APP_ROOT, '', $calledFrom[0]['file']), $level). " ";
    $calledFromStr .= "LINE:".$calledFrom[0]['line'];
    $output     = date('Y-m-d H:i:s') . ' Debug: ' . $calledFromStr. "\n". print_r($message, true) . "\n";
    fwrite($log, $output);
    fclose($log);
}

/**
 * 捕捉系统错误
 */
function appExceptionHandler()
{
    /**
     *
     1	E_ERROR (integer)	 致命的运行时错误。这类错误一般是不可恢复的情况，例如内存分配导致的问题。后果是导致脚本终止不再继续运行。
     2	E_WARNING (integer)	 运行时警告 (非致命错误)。仅给出提示信息，但是脚本不会终止运行。
     4	E_PARSE (integer)	 编译时语法解析错误。解析错误仅仅由分析器产生。
     8	E_NOTICE (integer)	 运行时通知。表示脚本遇到可能会表现为错误的情况，但是在可以正常运行的脚
     */
    if($e = error_get_last()) {
        //outputLog($e, 'error');
        //$e['type']对应php_error常量
        $message = "\n";
        $message  = "error message:".$e['message']."\n";
        $message .= "File:".$e['file']."\n";
        $message .= "Line:".$e['line']."\n";
        outputLog($message, 'error', 2);
    }
}

/**
 * 取得配置文件内容
 * @param unknown_type $configValue
 * @param unknown_type $fileName
 */
function loadConfigINI(&$configValue = array(), $fileName) {
    $configValue = parse_ini_file($fileName, true);
}

/* End */
