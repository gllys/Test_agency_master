<?php
/**
 * 分页工具
 *
 * $page = $this->load->tool('Page');
 * dump($page->show(100, 20));
 *
 * 2012-12-12 1.0 lizi 创建
 *
 * @author  lizi
 * @version 1.0
 */
class PageTool
{
	public $listRows      = 10;       // 默认列表每页显示行数
	public $firstRow      = 1;        // 起始行数
	protected $totalPages = 0;        // 分页总页面数
	protected $totalRows  = 0;        // 总行数
	protected $nowPage    = 1;        // 当前页数
	protected $varPage    = 'p';      // 默认分页变量名
	protected $isRewrite  = FALSE;   // rewirte 1.html/p=1
	protected $config     = array(); // 分页显示风格定制
	protected $theme      = '';       // 分页显示风格定制模板

	/**
	 * 构造函数,自动连接数据库
	 */
	// public function __construct() {}

	/**
	 * 分页显示
	 *
	 * @param int $totalRows 总的记录数
	 * @param int $listRows 每页显示记录数
	 * @param int $style 分页风格
	 * @param string $params 指定跳转url
	 * @return string html
	 */
	public function show($totalRows, $listRows = 10, $style = 1, $params = '')
	{
		// 参数设置
		if(0 == $totalRows) return '';
		$this->nowPage    = !empty(PI::$data['get'][$this->varPage]) ? intval(PI::$data['get'][$this->varPage]) : 1;
		$this->firstRow   = $listRows * ($this->nowPage - 1); // 超始行
		$this->totalRows  = $totalRows; // 总行数
		$this->listRows   = $listRows;  // 每页显示行数
		$this->totalPages = ceil($totalRows / $listRows);     //总页数
		$this->_setStyle($style);

		// 上下翻页字符串
		$prev = $this->nowPage - 1;
		$next = $this->nowPage + 1;
		$prevPage = ($prev > 0) ? '<a href="'.$this->_getUrl($prev, $params).'" class="previous paginate_button">'.$this->config['prev'].'</a>' : '';
		$nextPage = ($next <= $this->totalPages) ? '<a href='.$this->_getUrl($next, $params).' class="next paginate_button">'.$this->config['next'].'</a>' : '';

		// 首页尾页
		$firstPage = ($prev > 0) ? '<a href='.$this->_getUrl(1, $params).' class="previous paginate_button">'.$this->config['first'].'</a>' : '';
		$lastPage  = ($next <= $this->totalPages) ? '<a href='.$this->_getUrl($this->totalPages, $params).' class="next paginate_button">'.$this->config['last'].'</a>' : '';

		// 1 2 3 4 5
		$tabs = $this->_getTabs($this->nowPage, 4);


		// 输出
		$pageStr = str_replace(array('%first%', '%prev%', '%next%', '%last%', '%tabs%'), array($firstPage, $prevPage, $nextPage, $lastPage, $tabs), $this->theme);
		return $pageStr;
	}

	/**
	 * 分页风格
	 *
	 * @param int $style 风格
	 */
	private function _setStyle($style = 1) {
		switch ($style) {
			case 1:
				$firstRow = $this->firstRow + 1;               // 第1条记录 
				if(($this->firstRow + $this->listRows) > $this->totalRows){
					$lastRow = $this->totalRows;
				}else{
					$lastRow  = $this->firstRow + $this->listRows; // 最后一条记录 
				}
				$this->isRewrite = FALSE;
				$this->config = array('prev'=>'< 上一页', 'next'=>'下一页 >', 'first'=>'<< 首页', 'last'=>'尾页 >>');
				$this->theme  = $firstRow.'-'.$lastRow.'条 共'.$this->totalRows.'条 %first% %prev% %next% %last%';
				break;
			case 2:
				$firstRow = $this->firstRow + 1;               // 第1条记录 
				if(($this->firstRow + $this->listRows) > $this->totalRows){
					$lastRow = $this->totalRows;
				}else{
					$lastRow  = $this->firstRow + $this->listRows; // 最后一条记录 
				}
				$this->isRewrite = TRUE;
				$this->config = array('prev'=>'< 上一页', 'next'=>'下一页 >', 'first'=>'<< 首页', 'last'=>'尾页 >>');
				$this->theme  = '<ul><li>'.$firstRow.'-'.$lastRow.'条 共'.$this->totalRows.'条</li> %first% %prev% %next% %last%</ul>';
				break;
			case 3:
				$firstRow = $this->firstRow + 1;               // 第1条记录 
				if(($this->firstRow + $this->listRows) > $this->totalRows){
					$lastRow = $this->totalRows;
				}else{
					$lastRow  = $this->firstRow + $this->listRows; // 最后一条记录 
				}
				$this->isRewrite = FALSE;
				$this->config = array('prev'=>'<', 'next'=>'>', 'first'=>'<<', 'last'=>'>>');
				$this->theme  = '<div class="pagination">%first%%prev%%tabs%%next%%last%</div>';
				break;
			default:
				$this->config = array();
				$this->theme  = '';
		}
	}

	/**
	 * 获取url
	 *
	 * 分页url例子： index.html?p=1  index.php?c=test&a=index  index.php?c=test&a=index&p=2
	 * modify at 2013-09-12 by liuhe
	 * @param int $page    需要替换的页数
	 * @return string url
	 */
	private function _getUrl($page, $params = '')
	{
		if (!empty($params) && is_array($params)) {
			$params = http_build_query($params).'&';
		}
		else {
			$params = '';
		}
		if(strpos($_SERVER['REQUEST_URI'], '?') == false){
			$url = $_SERVER['REQUEST_URI'].'?'.$params.$this->varPage.'='.$page;
		}else{
			if(strpos($_SERVER['REQUEST_URI'], $this->varPage.'=') == false){
				$url = $_SERVER['REQUEST_URI'].'&'.$params.$this->varPage.'='.$page;
			}else{
				$url = str_replace($this->varPage."=".$this->nowPage, $this->varPage."=".$page, $_SERVER['REQUEST_URI']);
			}
		}
		return $url;
	}

	/**
	 * 获取页签
	 *
	 * @param int $now    当前页签
	 * @param int $length 显示长度
	 * @return string html
	 */
	private function _getTabs($now, $length = 3)
	{
		$html = '';
		if (empty($now)) return FALSE;
		for($i = $now - $length; $i < $now; $i++)
		{
			if ($i > 0) $html .= '<li><a href="'.$this->_getUrl($i).'">'.$i.'</a></li>';
		}
		$html .= '<li><a href="'.$this->_getUrl($now).'" class="current">'.$now.'</a><li>';
		for($i = $now + 1; $i <= $now + $length; $i++)
		{
			if ($i <= $this->totalPages) $html .= '<li><a href="'.$this->_getUrl($i).'">'.$i.'</a><li>';
		}
		return $html;
	}
}

/* End */
