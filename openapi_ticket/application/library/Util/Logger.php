<?php

if (!function_exists('fastcgi_finish_request')) {
    function fastcgi_finish_request() {}
}

/**
 * Description of Util_log
 *
 * @author wfdx1_000
 */
class Util_Logger {

    /**
     *
     * @var Util_Logger
     */
    private static $instance = array();
    private static $supportModules = array();
    
    private $logs = array();
    private $module;

    /**
     * 
     * @param string $module 当前的模块名称 从setting中读取
     * @return Util_logger
     * @throws Exception
     */
    public static function getLogger($module = 'common') {
        if (empty(self::$supportModules)) {
            $config = Yaf_Registry::get('config');
            $config = $config['openapi_log'];
            
            self::$supportModules = $config['module'];
        }
        if (!in_array($module, self::$supportModules)) {
            throw new Exception('不支持的模块名称');
        }
        if (!isset(self::$instance[$module])) {
            self::$instance[$module] = new self($module);
        }
        return self::$instance[$module];
    }
    
    private function __construct($module) {
        $this->module = $module;
        register_shutdown_function(array($this, 'flush'));
    }
    
    private function push($level, $params) {
        if (PHP_SAPI == 'cli') {
            $this->write(array_merge(array(
                'level' => $level
            ), $params));
        } else {
            $this->logs[$level][] = $params;    
        }
    }
    
    private function write($params) {
        $data = array(array_merge($params, array(
            'module' => $this->module
        )));
        Process_Async::send(array('LoggerModel', 'write2Db'), $data);
    }
    
    /**
     * @param string $method 当前的方法，使用常量 __METHOD__
     * @param mix $params 请求的参数
     * @param mix $comment 其他信息
     * @param string $category 对日志进行分组的组名
     * @param string $searchKey 通过分组可进行搜索的值
     */
    public function info($method, $params, $comment = '', $category = '', $searchKey = '') {
        $pra = $this->commonParams('info', $category, $method, $params, $comment, $searchKey);
//        $this->push('info', $category, $method, $params, $comment, $searchKey);
        $this->push('info', $pra);
    }
    public function error($method, $params, $comment = '', $category = '', $searchKey = '') {
        $pra = $this->commonParams('error', $category, $method, $params, $comment, $searchKey);
//        $this->push('error', $category, $method, $params, $comment, $searchKey);
        $this->push('error', $pra);
    }
    public function debug($method, $params, $comment = '', $category = '', $searchKey = '') {
        $pra = $this->commonParams('debug', $category, $method, $params, $comment, $searchKey);
//        $this->push('debug', $category, $method, $params, $comment, $searchKey);
        $this->push('debug', $pra);
    }
    public function warn($method, $params, $comment = '', $category = '', $searchKey = '') {
        $pra = $this->commonParams('warn', $category, $method, $params, $comment, $searchKey);
//        $this->push('warn', $category, $method, $params, $comment, $searchKey);
        $this->push('warn', $pra);
    }
    //普通日志表结构
    private function commonParams($level, $category, $method, $params, $comment, $searchVal){
        $params = array(
            'category' => $category,
            'method' => $method,
            'params' => var_export($params, true),
            'comment' => is_string($comment) ? $comment : var_export($params, false),
            'search_val' => $searchVal,
            'created_date' => time(),
            'level' => $level,
        );
        return $params;
    }

    public function exception($ee, $category = '', $search_val = ''){
        $trace = $ee->getTrace();
        $filename = $ee->getFile();
        $method = $trace[0]['class'].'::'.$trace[0]['function'];
        $message = $ee->getMessage();
        $trace = $trace;
        $pra = $this->errorParams($filename, $method, $message, $trace, $category, $search_val);

        $this->push('exception', $pra);
    }
    //错误日志表结构
    private function errorParams($filename, $method, $message, $trace, $category, $search_val){
        $params = array(
            'filename' => $filename,
            'method' => $method,
            'message' => $message,
            'trace' => var_export($trace, true),
            'category' => $category,
            'created_date' => time(),
            'search_val' => $search_val,
        );
        return $params;
    }
    
    public function flush() {
        fastcgi_finish_request();
        
        foreach ($this->logs as $level => $group) {
            foreach ($group as $params) {
                $this->write(array_merge(array(
                    'server_ip' => isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : ''
                ), $params));
            }
        }
    }
    
}
