<?php

/**
 * 控制器基类
 * @author  mosen
 */
class Base_Controller_Server extends Base_Controller_Abstract {

    protected $yafAutoRender = false;

    public function init() {
        //实例化服务端，开启服务
        header("Content-type: text/html; charset=utf-8");
        $server = new Server();
        $server->startServer($this);
    }

}

require(APPLICATION_PATH . '/application/library/Phprpc/phprpc_server.php');

class Server {

    //phprpc服务端
    private $_server;
    //是否开启调试,打印错误
    private $_debug = false;
    //设置编码
    private $_charset = 'UTF-8';
    //启动压缩输出虽然可以让传输的数据量减少，但是它会占用更多的内存和 CPU，因此它默认是关闭的
    private $_enableZip = false;

    public function __construct() {
        $this->_server = new PHPRPC_Server();
    }

    //添加服务
    private function _addActions($object) {
        //获取需要开启的服务列表
        $methods = get_class_methods($object);
        $exception = array(0 => 'init', 1 => 'indexAction', 2 => '__get', 3 => '__set', 4 => '__isset', 5 => '__unset', 6 => '__call', 7 => 'getRawBody', 8 => 'getIp', 9 => 'getRequest', 10 => 'getResponse', 11 => 'getModuleName', 12 => 'getView', 13 => 'initView', 14 => 'setViewpath', 15 => 'getViewpath', 16 => 'forward', 17 => 'redirect', 18 => 'getInvokeArgs', 19 => 'getInvokeArg', 20 => '__construct',);
        foreach ($methods as $method) {
            if (in_array($method, $exception)) {
                continue;
            }
            //phprpc添加服务
            $this->_server->add($method, $object);
        }
    }

    //准备：设置服务端的参数
    private function _prepare() {
        $this->_server->setCharset($this->_charset);
        $this->_server->setDebugMode($this->_debug);
        $this->_server->setEnableGZIP($this->_enableZip);
    }

    //phprpc开启服务
    public function startServer($object) {
        $this->_addActions($object);
        $this->_prepare();
        //$this->_server->start();
    }

}