<?php
/**
 * 所有CMS网站controller基类
 * @package common.controllers
 */
class CMSController extends CController {
	private $_pageTitle;
	private $_pageDescription;
	private $_pageKeywords;
	private $_site;
	private $_categoryStruct;
	private $_pageTitleSuffix;
	
	/**
	 * @return string the page title. 
	 */
	public function getPageTitle()
	{
		if($this->_pageTitle===null){
			$site = $this->site;
			$this->_pageTitle = $site['page_title'];
		}
		return $this->_pageTitle.$this->pageTitleSuffix;
	}
	
	/**
	 * @return string the page title. 
	 */
	public function getPageTitleSuffix()
	{
		if($this->_pageTitleSuffix!==null)
			return $this->_pageTitleSuffix;
		else
		{
			$site = $this->site;
			return $this->_pageTitleSuffix = $site['page_title_suffix'];
		}
	}

	/**
	 * @param string $value the page title.
	 */
	public function setPageTitle($value)
	{
		$this->_pageTitle=$value;
	}
	
	/**
	 * @return string the page Description.
	 */
	public function getPageDescription()
	{
		if($this->_pageDescription!==null)
			return $this->_pageDescription;
		else
		{
			$site = $this->site;
			return $this->_pageDescription = $site['page_description'];
		}
	}

	/**
	 * @param string $value the page title.
	 */
	public function setPageDescription($value)
	{
		$this->_pageDescription=$value;
	}
	
	/**
	 * @return string the page Keywords.
	 */
	public function getPageKeywords()
	{
		if($this->_pageKeywords!==null)
			return $this->_pageKeywords;
		else
		{
			$site = $this->site;
			return $this->_pageKeywords = $site['page_keywords'];
		}
	}

	/**
	 * @param string $value the page title.
	 */
	public function setPageKeywords($value)
	{
		$this->_pageKeywords=$value;
	}
	
	/**
	 * @return array 网站信息
	 */
	public function getSite()
	{
		if($this->_site!==null)
			return $this->_site;
		else
		{
			return $this->_site = Yii::app()->CMSApi->siteInfo();
		}
	}
	
	/**
	 * 网站的版面结构
	 *
	 * @return array
	 */
	public function getCategoryStruct(){
		if($this->_categoryStruct!==null){
			return $this->_categoryStruct;
		}else {
			return $this->_categoryStruct = Yii::app()->CMSApi->categoryStruct();
		}
	}
	
	/**
	 * 静态资源文件基础url
	 *
	 * @return unknown
	 */
	public function getStaticBaseUrl(){
		$site = $this->site;
		return $site['static_base_url'];
	}
}