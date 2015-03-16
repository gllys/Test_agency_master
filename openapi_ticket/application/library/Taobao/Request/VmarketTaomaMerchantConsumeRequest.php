<?php
/**
 * TOP API: taobao.vmarket.taoma.merchant.consume request
 * 
 * @author auto create
 * @since 1.0, 2014-12-22 10:36:19
 */
class Taobao_Request_VmarketTaomaMerchantConsumeRequest
{
	/** 
	 * 核销份数
	 **/
	private $consumeNum;
	
	/** 
	 * 核销操作人员
	 **/
	private $operator;
	
	/** 
	 * 核销流水号
	 **/
	private $serialNum;
	
	/** 
	 * 核销码
	 **/
	private $verifyCode;
	
	private $apiParas = array();
	
	public function setConsumeNum($consumeNum)
	{
		$this->consumeNum = $consumeNum;
		$this->apiParas["consume_num"] = $consumeNum;
	}

	public function getConsumeNum()
	{
		return $this->consumeNum;
	}

	public function setOperator($operator)
	{
		$this->operator = $operator;
		$this->apiParas["operator"] = $operator;
	}

	public function getOperator()
	{
		return $this->operator;
	}

	public function setSerialNum($serialNum)
	{
		$this->serialNum = $serialNum;
		$this->apiParas["serial_num"] = $serialNum;
	}

	public function getSerialNum()
	{
		return $this->serialNum;
	}

	public function setVerifyCode($verifyCode)
	{
		$this->verifyCode = $verifyCode;
		$this->apiParas["verify_code"] = $verifyCode;
	}

	public function getVerifyCode()
	{
		return $this->verifyCode;
	}

	public function getApiMethodName()
	{
		return "taobao.vmarket.taoma.merchant.consume";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		Taobao_RequestCheckUtil::checkNotNull($this->consumeNum,"consumeNum");
		Taobao_RequestCheckUtil::checkNotNull($this->operator,"operator");
		Taobao_RequestCheckUtil::checkNotNull($this->serialNum,"serialNum");
		Taobao_RequestCheckUtil::checkNotNull($this->verifyCode,"verifyCode");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
