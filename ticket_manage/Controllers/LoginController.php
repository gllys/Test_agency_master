<?php
/**
 * 登录控制器
 * 2014-1-3
 * @package controller
 * @author cuiyulei
 **/
class LoginController extends BaseController
{
	// 登录验证
	public function index()
	{       
                if(isset($_SESSION['backend_userinfo'])){
                    redirect ('/landscape_lists.html') ;
                }
		$this->load->view('login/index');
	}

	// 登录验证
	public function authVerify()
	{
		$this->doAction('admin', 'authVerify', $this->getPost());
	}

	// 登录验证
	public function logout()
	{
		unset($_SESSION['backend_userinfo']);
		redirect ('/login.html', '成功退出!', 0);
		exit();
	}
	
} // END class 