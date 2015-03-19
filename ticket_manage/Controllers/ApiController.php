<?php
/**
 *
 * @author liuhe(liuhe009@gmail.com)
 * 2014-01-02
 * 
 * @version 1.0
 */
class ApiController extends Controller
{
	/**
	 * 简单的路由
	 * 
	 * 2014-01-02
	 * 
	 * @version 1.0
	 */
	public function router()
	{
		header('Content-Type:text/html; charset=utf-8');
		$get              = $this->getGet();
		$apiMappingName   = $get['api_mapping_name'];
		$apiMappingMethod = $get['api_mapping_method'];
		$apiMapping       = unserialize(PI_API_MAPPING);
		if(!$apiMapping) {
			echo '配置文件不存在';exit;
		}
		if(array_key_exists($apiMappingName, $apiMapping)) {
			if(array_key_exists($apiMappingMethod, $apiMapping[$apiMappingName]['methods'])) {
				$className = $apiMapping[$apiMappingName]['class_name'];
				$method    = $apiMapping[$apiMappingName]['methods'][$apiMappingMethod];
				if(class_exists($className)) {
					$classObj = new $className;
					if(method_exists($classObj, $method)) {
						$classObj->$method();
					}
				}
			} else {
				echo '不在白名单里';exit;
			}
		} else {
			echo '不在白名单里';exit;
		}
	}
}