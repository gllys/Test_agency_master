<?php
/**
 * 通用控制器，放一些执行操作相同的方法
 *
 * @author liuhe(liuhe009@gmail.com)
 * 2013-11-07
 * 
 * @version 1.0
 */
class BaseController extends Controller
{
	//将之前的登陆的钩子移到这里来，多个平台的时候登陆的方式不一样
	public function __construct()
	{
		parent::__construct();
		$controller = $this->getGet('c') ? $this->getGet('c') : PI_DEFAULT_CONTROLLER;
		$action     = $this->getGet('a') ? $this->getGet('a') : PI_DEFAULT_ACTION;
		if($controller != 'login'){
			$this->load->common('user')->isLogin();
		}

		//访问权限检测
		if(!$this->load->common('permission')->allowInto(array('controller'=>$controller, 'action'=>$action))) {
			$this->load->view('common/no_permission');exit;
		}
	}

	//执行操作
	public function doAction($commonPrefix, $action, $param = array())
	{
		$common = $this->load->common($commonPrefix);
		$result    = $common->$action($param);
		echo $result;exit;
	}

	//获取分页信息
	public function getPagination($pagination) {
		//分页信息  count:总条数;per_page:每页显示条数
		$count    = $pagination['count'];
		$per_page = $pagination['items'];
		$params   = $pagination['params'];
		$pageTool = $this->load->tool('page');
		$result   = $pageTool->show($count, $per_page, 1, $params);
		return $result;
	}
        
        //获取Api分页信息
        public function getPaginationApi($pagination) {
		//分页信息  count:总条数;per_page:每页显示条数
		$count    = $pagination['count'];
		$per_page = $pagination['items'];
		$params   = $pagination['params'];
		$pageTool = $this->load->tool('page');
		$result   = $pageTool->show($count, $per_page, 1, $params);
		return $result;
	}
        
        //错误输出
        public function _end($err,$msg='',$param=array()){
            header('Content-type: application/json; charset=utf-8');
            echo json_encode(array('error'=>$err,'msg'=>$msg,'param'=>$param), JSON_UNESCAPED_UNICODE);
            exit;
        }
}
