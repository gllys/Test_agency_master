<?php
/**
 * MyPHP Framework [PI]
 *
 * PI的目标高性质
 * 追求极致性能-优化到每个字节、每K内存、每毫秒执行时间
 * 严格要求质量-高标准、高要求、高质量
 *
 * 2012-12-12 1.0 lizi 创建
 *
 * @author  lizi
 * @version 1.0
 */
define('PI_VER', '1.0');

class PI
{
	private $action;
	private $controller;
	private $controllerFile;
	static public $data = array();

	function __construct()
	{
		self::$data['get']    = magic_quotes($_GET);
		self::$data['post']   = magic_quotes($_POST);
		self::$data['method'] = $_SERVER['REQUEST_METHOD'];
		self::$data['server'] = $_SERVER;
		self::$data['debug']  = PI_DEBUG;
		unset($_POST, $_GET);
	}

	public function run()
	{
		// 获取控制器与方法
		$get = $this->get('get');
		$this->controller = $get['c'] ? $get['c'] : PI_DEFAULT_CONTROLLER;
		$this->action     = $get['a'] ? $get['a'] : PI_DEFAULT_ACTION;

		// session 先暂时放这里
		session_start();

		// 首字母大写及安全过滤处理
		$this->controller = ucfirst(preg_replace("/[^a-z\_1-9]/i", "", $this->controller)).'Controller';

		// 控制器文件
		$this->controllerFile = PI_APP_ROOT.'Controllers/'.$this->controller.'.php';

		// 控制器初始化
		require_once($this->controllerFile);
		if(class_exists($this->controller))
		{
			$objController = new $this->controller($this->action);
		}

		// 控制器处理
		if(is_object($objController))
		{
			// hook处理
			if(PI_APP_LOAD_HOOK)
			{
				// 加载hook配置并取得配置
				require_once(PI_APP_ROOT.'Configs/Hook.php');

				// 所有控制器开始运行前的钩子
				if (function_exists(HOOK_BEGIN))
				{
					$hookData  = call_user_func(HOOK_BEGIN);
					// require_once(PI_APP_ROOT.'Controllers/'.$hookData[0].'Controller.php');
					$hookController = new $hookData[0]($hookData[1]);
					$hookController->{$hookData[1]}();
				}

				// 控制器执行时钩子
				$bool = FALSE;
				if (function_exists(HOOK_EXECUTE_ACTION))
				{
					$hookData = array();
					$tmpData  = call_user_func(HOOK_EXECUTE_ACTION);
					$actionPath = $this->controller.'_'.$this->action;

					foreach ($tmpData as $key => $value) $hookData[$key] = $value;
					if (array_key_exists($actionPath, $hookData))
					{
						$bool = TRUE;
						if (array_key_exists('begin', $hookData[$actionPath]))
						{
							// 类方式调用
							if (is_array($hookData[$actionPath]['begin']))
							{
								require_once(PI_APP_ROOT.'controllers/'.$hookData[$actionPath]['begin'][0].'Controller.php');
								$hookController = new $hookData[$actionPath]['begin'][0]($hookData[$actionPath]['begin'][1]);
								$hookController->{$hookData[$actionPath]['begin'][1]}();
							}
							else
							{
								$hookData[$actionPath]['begin']();
							}
						}
					}
				}
			}

			// 执行
			$obj = $objController->{$this->action}();

			// hook end 处理 暂无
			// if($bool) {}
		}
		else
		{
			header("Location: ".PI_HTTP_404_PAGE);
			exit();
		}

		// DEBUG
		if(PI::$data['debug']) require_once(PI_CORE_ROOT.'Debug.php');
	}

	/**
	 * 设置数据
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	static public function set($key, $value = '')
	{
		self::$data[$key] = $value;
	}

	/**
	 * 获得数据
	 *
	 * @param string $key   :键名
	 * @param string $field :数组格式数据及特殊数据键名 如 get/post/debug
	 * @return unknown
	 */
	static public function get($key = '', $field = '')
	{
		if ($key === '')
			return self::$data['get'];
		else
			return $field === '' ? self::$data[$key] : self::$data[$key][$field];
	}

	/**
	 * 删除数据
	 *
	 * @param string $key   :键名
	 * @param string $field :数组格式数据及特殊数据键名 如 get/post/debug
	 * @return unknown
	 */
	static public function del($key, $field = '')
	{
		if ($field === '')
			unset(self::$data[$key]);
		else
			unset(self::$data[$key][$field]);
	}
}


/**
 * PI 加载组件
 *
 * @author  lizi
 * @version 1.0
 */
class Load
{
	/**
	 * 加载 视图
	 *
	 * @param  string $tpl :视图文件
	 * @param  array $data :数据数组
	 * @return object
	 */
	public function view($tpl, $data = array())
	{
		if(PI::$data['debug']) $beginTime = microtime(TRUE);$beginMem = memory_get_usage();
		if (!empty($data)) foreach ($data as $key => $value) $$key = $value;
		$tplFile = PI_APP_ROOT.'Views/tpl/'.$tpl.'.php';
		if(PI::$data['debug']) PI::$data['flow']['tpl'] = $data;
		if(file_exists($tplFile)) include_once($tplFile);
		if(PI::$data['debug']) PI::$data['flow']['module']['view'][] = array('txt'=>$tpl, 'time'=>microtime(TRUE)-$beginTime, 'mem'=>memory_get_usage()-$beginMem);
	}

	/**
	 * 加载 Model
	 *
	 * @param  string $model :模型文件
	 * @param  string $db    :加载库
	 * @return object
	 */
	public function model($model, $chipId = 0)
	{
		// 赋值
		$model = ucfirst($model);

		// model处理
		$modelFile = PI_APP_ROOT.'Models/'.$model.'Model.php';
		if(file_exists($modelFile))
		{
			// 加载横亘
			require_once(PI_CORE_ROOT.'/Model.php');
			require_once($modelFile);
			$modelClass = $model.'Model';

			// 实例化并输出
			$modelObj = new $modelClass($chipId);
			return $modelObj;
		}
		else
		{
			die('err:modelFile'.$modelFile);
		}
	}

	/**
	 * 加载 MongoDB
	 *
	 * 加载示例：$this->load->mongo('table')->find()->limit(2);
	 *
	 * @param  string $table :表
	 * @param  string $db    :加载库
	 * @return object
	 */
	public function mongo($table, $db = 'master')
	{
		// 参数处理
		$dbs = unserialize(PI_MONGO);
		$dba = $dbs[$db];

		// 数据检验
		if(!is_array($dba)) die('err:mongo');

		// MongoDB
		$conn       = new Mongo($dba['host'].':'.$dba['port']);
		$db         = $conn->$dba['db'];
		$collection = $db->$table;
		return $collection;
	}

	/**
	 * 加载 MemCache
	 *
	 * 加载示例：$this->load->memcache->get('key');
	 *
	 * @param  string $db    :加载库
	 * @return object
	 */
	public function memcache($db = 'master')
	{
		// 参数处理
		$dbs = unserialize(PI_MEMCACHE);
		$dba = $dbs[$db];

		// 数据检验
		if(!is_array($dba)) die('err:memcache');

		// Memcache
		$conn = new Memcache();
		$conn->connect($dba['host'], $dba['port']);
		return $conn;
	}

	/**
	 * 加载 Common
	 *
	 * @param  string $class  :加载类库.
	 * @param  string $action :方法
	 * @param  mixed  $param  :参数
	 * @return object
	 */
	public function common($class, $action = '', $param = '')
	{
		if(PI::$data['debug']) $beginTime = microtime(TRUE);$beginMem = memory_get_usage();
		$class = ucfirst($class).'Common';
		$obj   = new $class();
		$common = empty($action) ? $obj :$obj->$action($param);
		if(PI::$data['debug']) PI::$data['flow']['module']['common'][] = array('txt' => $class.' '.$action, 'time' => microtime(TRUE)-$beginTime, 'mem' => memory_get_usage()-$beginMem);
		return $common;
	}

	/**
	 * 加载 Tool
	 *
	 * @param  string $class :tool类
	 * @return object
	 */
	public function tool($class)
	{
		$class = ucfirst($class).'Tool';
		$obj   = new $class();
		return $obj;
	}

	/**
	 * 加载 COOKIE
	 *
	 * @param  string $class  :加载类库.
	 * @param  string $action :方法
	 * @param  mixed  $param  :参数
	 * @return object
	 */
	public function cookie()
	{
		return new Cookie();
	}

}

/**
 * Cookie组件
 *
 * @author  lizi
 * @version 1.0
 */
class Cookie
{
	/**
	 * 设置COOKIE
	 *
	 * @param string $name :COOKIE名称
	 * @param array $value :值
	 * @param int $time    :有效时间,以秒为单位,0:表示会话期间内
	 * @param boolean $encode :是否加密
	 * @param string $domain  :cookie所在的域
	 * @return boolean
	 */
	function set($name, $value = '', $time = 0, $encode = FALSE, $domain = PI_DOMAIN)
	{
		$time  = ($time == 0) ? 0 : (time()+$time);
		$value = $encode ? __base64_encode($value) : $value;
		return setcookie($name, $value, $time, '/', '');
	}

	/**
	 * 获取COOKIE
	 *
	 * @param  string  $name   :COOKIE名称,如果为空则返回整个 $COOKIE 数组
	 * @param  boolean $decode :是否自动解密
	 * @return mixed
	 */
	function get($name = '', $decode = FALSE)
	{
		$cookie = magic_quotes($_COOKIE);
		$value  =  $name ? (isset($cookie[$name]) ? $cookie[$name] : NULL) : $cookie;
		return (!empty($name) && $decode) ? __base64_decode($value) : $value;
	}

	/**
	 * 删除COOKIE
	 *
	 * @param string $name :COOKIE名称
	 * @return boolean
	 */
	function del($name)
	{
		$this->set($name, '', -86400*365);
	}
}

/**
 * 控制器类
 *
 * @author  lizi
 * @version 1.0
 */
abstract class Controller
{
	protected $load;
	public $debug;

	function __construct()
	{
		$this->load  = new Load();
		$this->debug = &PI::$data['debug'];
	}

	/**
	 * 获得数据
	 *
	 * @param string $key   :键名
	 * @param string $field :数组格式数据及特殊数据键名 如 get/post/debug
	 * @return unknown
	 */
	public function get($key = '', $field = '')
	{
		return PI::get($key, $field);
	}

	/**
	 * 删除数据
	 *
	 * @param string $key   :键名
	 * @param string $field :数组格式数据及特殊数据键名 如 get/post/debug
	 * @return unknown
	 */
	public function del($key, $field = '')
	{
		PI::del($key, $field);
	}
	/**
	 * 设置数据
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set($key, $value = '')
	{
		PI::set($key, $value);
	}

	/**
	 * 附加数据
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function append($key, $value = '')
	{
		PI::set($key, $value);
	}

	/**
	 * 快捷方式
	 */
	public function getGet($field = '')
	{
		return PI::get('get', $field);
	}
	public function getPost($field = '')
	{
		return PI::get('post', $field);
	}
	public function getMethod()
	{
		return PI::get('method');
	}

}

/**
 * Common类
 *
 * @author  lizi
 * @version 1.0
 */
class Common extends Controller
{

}


/**
 * 是否加载项目函数库
 */
if (PI_APP_LOAD_FUN) require_once(PI_APP_ROOT.'Libs/Function.php');

/**
 * 下面为核心函数库
 */

/**
 * 根据设置处理魔术引用
 *
 * @param  mixed  $value
 * @return string
 */
function magic_quotes($value, $flag = FALSE)
{
	if (!get_magic_quotes_gpc() || $flag)
	{
		return is_array($value) ? array_map('magic_quotes', $value) : addslashes($value);
	}
	else
	{
		return $value;
	}
}

/**
 * 任何变量的调试输出
 *
 * @param  mixed $var
 * @return mixed
 */
function dump($var, $exit = FALSE)
{
	echo '<pre style="font-size:16px; color:#0000FF">';
	if (is_array($var)) { print_r($var); }
	elseif(is_object($var)) { echo get_class($var)." Object"; }
	elseif(is_resource($var)) { echo (string)$var; }
	else { echo var_dump($var); }
	echo '</pre>';
	if ($exit) exit;
}


/**
 * 加载方法公用函数
 *
 * 可用于加载控制器、模型及公共方法
 *
 * @param  string $class 类
 * @param  string $action 方法
 * @param  mixed $param 参数
 * @return string html
 */
function load_action($class, $action, $param = '')
{
	$obj = new $class();
	return $obj->$action($param);
}

/**
 * debug
 *
 * @param  string $field
 * @param  mixed  $data
 * @return mixed
 */
function debug($field, $data, $exit = FALSE)
{
	PI::$data['flow'][$field] = $data;
	if ($exit) dump($data, TRUE);
}

/**
 * 过滤数值
 *
 * @param  string $int
 * @return int
 */
function __intval($int)
{
	// 处理方式还比较粗糙
	if(!is_int($int)) die('error:int');
	return $int;
}

/**
 * 自动装载类库组件
 *
 * @param string $class :组件名称
 */
function __autoload($class)
{
	require_once(ucfirst($class).'.php');
}

/**
 * base64编码加强版
 *
 * @param  string $str
 * @return string
 */
function __base64_encode($str)
{
	$rep1 = array('+', '=', '/');
	$rep2 = array('jiajia', 'dengdeng', 'xiexie');
	return str_replace($rep1, $rep2, base64_encode($str));
}

/**
 * base64解码加强版
 *
 * @param  string $str
 * @return string
 */
function __base64_decode($str)
{
	$rep1 = array('+', '=', '/');
	$rep2 = array('jiajia', 'dengdeng', 'xiexie');
	$code = str_replace($rep2, $rep1, $str);
	return base64_decode($code);
}

/* End */
