<?php
final class PaymentsOffline
{
	public $appKey       = 'offline';
	public $appName      = '景区支付';
	public $displayName  = '景区支付';
	public $payType      = 'offline';
	public $version      = '1.0';

	public function __construct()
	{
		$this->load  = new Load();
	}
}