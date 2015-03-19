<?php
/**
 * TOP API: tmall.product.match.schema.get request
 * 
 * @author auto create
 * @since 1.0, 2014-12-17 15:38:42
 */
class Taobao_Request_TmallProductMatchSchemaGetRequest
{
	/** 
	 * 商品发布的目标类目，必须是叶子类目
	 **/
	private $categoryId;
	
	private $apiParas = array();
	
	public function setCategoryId($categoryId)
	{
		$this->categoryId = $categoryId;
		$this->apiParas["category_id"] = $categoryId;
	}

	public function getCategoryId()
	{
		return $this->categoryId;
	}

	public function getApiMethodName()
	{
		return "tmall.product.match.schema.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		Taobao_RequestCheckUtil::checkNotNull($this->categoryId,"categoryId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}