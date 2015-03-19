<?php
/**
 * 视图公共方法
 *
 * 前端视图公共页头、页脚调用
 *
 * 2012-12-12 1.0 lizi
 *
 * @author  lizi
 * @version 1.0
 */
class ViewCommon extends Common
{
	/**
	 * 输出header html
	 *
	 * 主要用于前端公共页头调用
	 *
	 * @param  mixed $param 参数
	 * @return string html
	 */
	public function header($param = '')
	{
		// 参数设置
		$data['c']   = $this->getGet('c');

		// 加载视图
		if ($param === 'empty')
			$this->load->view('common/header_empty', $data);
		else
			$this->load->view('common/header', $data);
	}

	/**
	 * 输出footer html
	 *
	 * 主要用于前端公共页脚调用
	 *
	 * @return string html
	 */
	public function footer()
	{
		// 加载视图
		$this->load->view('common/footer', $data);
	}

	/**
	 * 输出 menu html
	 *
	 * 主要用于前端公共菜单调用
	 *
	 * @return string html
	 */
	public function menu()
	{
		// 参数设置
		$data['c'] = strtolower($this->getGet('c'));
		$data['a'] = strtolower($this->getGet('a'));

		// 调用菜单配置文件
		$data['menu'] = unserialize(PI_MENU);

		// 加载视图
		$this->load->view('common/menu', $data);
	}

	/**
	 * 输出 crumbs html
	 *
	 * 主要用于前端公共面包屑调用
	 *
	 * @return string html
	 */
	public function crumbs()
	{
		// 参数设置
		$data['c'] = strtolower($this->getGet('c'));
		$data['a'] = strtolower($this->getGet('a'));

		// 调用菜单配置文件
		$data['menu'] = unserialize(PI_MENU);

		// 加载视图
		$this->load->view('common/bread', $data);
	}

	/**
	 * 输出 error html
	 *
	 * 主要用于错误信息的调试
	 *
	 * @param  mixed $param 参数
	 *
	 * @return string html
	 */
	public function error($param = '')
	{
		// 参数设置
		$data['message'] = $param;

		// 加载视图
		$this->load->view('common/error', $data);
	}

	/**
	 * 输出 redirect html
	 *
	 * 主要用于前端公共菜单调用
	 *
	 * @param  mixed $param 参数
	 *
	 * @return string html
	 */
	public function redirect($param='')
	{
		// 参数设置
		$data['url']     = $param['url'];
		$data['message'] = $param['message'];
		$data['time']    = $param['time'];

		// 加载视图
		$this->load->view('common/redirect', $data);
	}

	/**
	 * 输出3级城市初始化 html
	 *
	 * @return string html
	 */
	public function getCityInfo($param = '')
	{
		$scenicCommon     = $this->load->common('scenic');
		$result           = $scenicCommon->getCityInfo();
		$data['cityInfo'] = $result;

		$get              = $this->getGet();
		//所在1级区域选中
		if($get['province'] != '__NULL__' && $get['province']){
			$secondArea               = $scenicCommon->getCityInfo($get['province']);
			$data['secondArea']       = $secondArea;
			$data['area']['province'] = $get['province'];
		}

		//所在2级区域选中
		if($get['city'] != '__NULL__' && $get['city']){
			$thirdArea                = $scenicCommon->getCityInfo($get['city']);
			$data['thirdArea']        = $thirdArea;
			$data['area']['city']     = $get['city'];
		}

		//所在3级区域选中
		if($get['area'] != '__NULL__' && $get['area']){
			$data['area']['area']     = $get['area'];
		}
		$this->load->view('common/area', $data);
	}

	public function topNav()
	{
		$this->load->view('common/top_nav');
	}
}

/* End */
