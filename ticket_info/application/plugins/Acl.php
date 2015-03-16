<?php
/**
 * @name SamplePlugin
 * @desc Yaf定义了如下的6个Hook,插件之间的执行顺序是先进先Call
 * @see http://www.php.net/manual/en/class.yaf-plugin-abstract.php
 * @author root
 */
class AclPlugin extends Yaf_Plugin_Abstract {

	public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        if($config = Yaf_Registry::get('config')) {
            if(strtolower(substr($_SERVER['REQUEST_URI'], - strlen($config['application']['url_suffix']))) == strtolower($config['application']['url_suffix'])) {
                $request->setRequestUri(substr($_SERVER['REQUEST_URI'], 0 , - strlen($config['application']['url_suffix'])));
            }
        }
	}

	public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		$all = $request->getPost() + $request->getQuery();
		if ($all) {
			foreach($all as $key => $value) {
				$request->setParam($key, $value);
			}
		}
	}

	public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}

	public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}

	public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}

	public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
	}
}
