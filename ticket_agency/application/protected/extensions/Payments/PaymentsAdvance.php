<?php
final class PaymentsAdvance
{
	public $appKey      = 'advance';
	public $appName     = '储值支付';
	public $displayName = '储值支付';
	public $payType     = 'advance';
	public $version     = '1.0';

	public function __construct()
	{
		$this->load  = new Load();
	}

	public function doPay($payment, $orderInfo, &$msg)
	{
		$paymentsCommon = $this->load->common('payments');
		if($payment['order_id']){
			if(!$paymentsCommon->checkRemainAndOrderAmount($orderInfo['seller_organization_id'], $orderInfo['buyer_organization_id'], $payment['money'], 'advance', $msg)){
				return false;
			}
		}else{
			$msg = '{"errors":{"msg":["错误的订单号"]}}';
			return false;
		}
		$payment['status'] = 'succ';

		$paymentsModel = $this->load->model('payments');
		$paymentsModel->begin();
		//更新支付单状态
		$result            = $paymentsCommon->updatePaymentStatus($payment, $msg);
		if($result){
			//支付完成，更新订单状态，信用支付扣除信用额度
			$orderCommon = $this->load->common('order');
			$payFinish   = $orderCommon->payFinish($payment['order_id'], $payment, $payment['money'], $msg);
			if($payFinish){
				$paymentsModel->commit();
				return true;
			}else{
				$paymentsModel->rollback();
				return false;
			}
		}else{
			$paymentsModel->rollback();
			return false;
		}
	}
}
