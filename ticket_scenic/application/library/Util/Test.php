<?php
/**
 * 命令行测试脚本基类
 * @author  mosen
 */
class Util_Test
{
	protected $request;
	protected $method = 'GET';
	protected $module = 'index';
	protected $controller = 'index';
	protected $action = 'index';
	protected $params = array();

	public function __construct() {
        $this->start();
    }

    /**
     * [getRequest description]
     * @return [type] [description]
     */
    public function getRequest() {
    	if (!$this->request)
    		$this->setRequest();
    	return $this->request;
    }

    /**
     * [setRequest description]
     * @param [type] $request [description]
     */
    public function setRequest(Yaf_Request_Abstract $request = null) {
    	if ($request === null) {
    		$request = new Yaf_Request_Simple(
	        	$this->method,
	        	$this->module,
	        	$this->controller,
	        	$this->action,
	        	$this->params
        	);
    	}
    	$this->request = $request;
    	return $this;
    }
    
    /**
     * [start description]
     * @return [type] [description]
     */
    public function start() {
        Yaf_Application::app()->getDispatcher()->dispatch($this->getRequest());
    }
}