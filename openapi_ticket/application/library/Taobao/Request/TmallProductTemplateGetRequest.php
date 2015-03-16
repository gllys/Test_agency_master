<?php
/**
 * TOP API: tmall.product.template.get request
 * 
 * @author auto create
 * @since 1.0, 2014-12-17 15:38:42
 */
class Taobao_Request_TmallProductTemplateGetRequest
{
	/** 
	 * 类目ID
	 **/
	private $cid;
	
	private $apiParas = array();
	
	public function setCid($cid)
	{
		$this->cid = $cid;
		$this->apiParas["cid"] = $cid;
	}

	public function getCid()
	{
		return $this->cid;
	}

	public function getApiMethodName()
	{
		return "tmall.product.template.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		Taobao_RequestCheckUtil::checkNotNull($this->cid,"cid");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
