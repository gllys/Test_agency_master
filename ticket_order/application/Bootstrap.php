<?php
/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

    public function _initConfig() {
		//把配置保存起来
		$arr = Yaf_Application::app()->getConfig()->toArray();

		if ($arr['memcache'] && $arr['memcache']['servers']) {
			$arr['memcache']['servers'] = unserialize($arr['memcache']['servers']);
		}

		if ($arr['db'] && $arr['db']['servers']) {
			$arr['db']['servers'] = unserialize($arr['db']['servers']);
		}

		if ($arr['redis'] && $arr['redis']['servers']) {
			$arr['redis']['servers'] = unserialize($arr['redis']['servers']);
		}

		Yaf_Registry::set('config', $arr);
	}

	public function _initLoader(Yaf_Dispatcher $dispatcher) {
		// Yaf_Loader::getInstance()->registerLocalNamespace(array("Cache"));
	}

	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		$items = Yaf_Registry::get("config");
		if ($items['plugins']) {
			foreach ($items['plugins'] as $key => $cls) {
				$dispatcher->registerPlugin(new $cls());
			}
		}
	}

	public function _initSession(Yaf_Dispatcher $dispatcher) {
		Yaf_Registry::set("sess", new Session_Proxy);
	}

	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
		$items = Yaf_Registry::get("config");
		if($items['plugins']) 
			$dispatcher->getRouter()->addConfig($items['plugins']);
	}
	
	public function _initView(Yaf_Dispatcher $dispatcher){
		//在这里注册自己的view控制器，例如smarty,firekylin
	}
}