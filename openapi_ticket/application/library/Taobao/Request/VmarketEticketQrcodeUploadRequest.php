<?php
/**
 * TOP API: taobao.vmarket.eticket.qrcode.upload request
 * 
 * @author auto create
 * @since 1.0, 2014-12-22 10:36:19
 */
class Taobao_Request_VmarketEticketQrcodeUploadRequest
{
	/** 
	 * 码商ID<br /> 支持的最大列表长度为：32
	 **/
	private $codeMerchantId;
	
	/** 
	 * 上传的图片byte[]  小于15K，图片尺寸400*400以内<br /> 支持的最大列表长度为：15360
	 **/
	private $imgBytes;
	
	private $apiParas = array();
	
	public function setCodeMerchantId($codeMerchantId)
	{
		$this->codeMerchantId = $codeMerchantId;
		$this->apiParas["code_merchant_id"] = $codeMerchantId;
	}

	public function getCodeMerchantId()
	{
		return $this->codeMerchantId;
	}

	public function setImgBytes($imgBytes)
	{
		$this->imgBytes = $imgBytes;
		$this->apiParas["img_bytes"] = $imgBytes;
	}

	public function getImgBytes()
	{
		return $this->imgBytes;
	}

	public function getApiMethodName()
	{
		return "taobao.vmarket.eticket.qrcode.upload";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		Taobao_RequestCheckUtil::checkNotNull($this->codeMerchantId,"codeMerchantId");
		Taobao_RequestCheckUtil::checkNotNull($this->imgBytes,"imgBytes");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
