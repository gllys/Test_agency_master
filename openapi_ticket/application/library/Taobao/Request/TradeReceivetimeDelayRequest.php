<?php
/**
 * TOP API: taobao.trade.receivetime.delay request
 * 
 * @author auto create
 * @since 1.0, 2014-12-17 15:38:42
 */
class Taobao_Request_TradeReceivetimeDelayRequest
{
	/** 
	 * 延长收货的天数，可选值为：3, 5, 7, 10。<br /> 支持最大值为：10<br /> 支持最小值为：3
	 **/
	private $days;
	
	/** 
	 * 主订单号
	 **/
	private $tid;
	
	private $apiParas = array();
	
	public function setDays($days)
	{
		$this->days = $days;
		$this->apiParas["days"] = $days;
	}

	public function getDays()
	{
		return $this->days;
	}

	public function setTid($tid)
	{
		$this->tid = $tid;
		$this->apiParas["tid"] = $tid;
	}

	public function getTid()
	{
		return $this->tid;
	}

	public function getApiMethodName()
	{
		return "taobao.trade.receivetime.delay";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		Taobao_RequestCheckUtil::checkNotNull($this->days,"days");
		Taobao_RequestCheckUtil::checkMaxValue($this->days,10,"days");
		Taobao_RequestCheckUtil::checkMinValue($this->days,3,"days");
		Taobao_RequestCheckUtil::checkNotNull($this->tid,"tid");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
