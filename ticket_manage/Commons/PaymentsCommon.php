<?php
/**
 *  
 * 
 * 2013-12-09
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class PaymentsCommon extends BaseCommon
{
	public function __construct()
	{
		parent::__construct();
		require_once(PI_APP_ROOT.'Configs/PaymentsMapping.php');
	}

	static public $paymentsStatus = array(
		'succ'     => '支付成功',
		'fail'     => '支付失败',
		'cancel'   => '未支付',
		'error'    => '处理异常',
		'invalid'  => '非法参数',
		'progress' => '已付款至担保方',
		'timeout'  => '超时',
		'ready'    => '准备中',
	);

	protected $_code = array(
		'-1'  => '{"errors":{"msg":["null post"]}}',
		'-2'  => '{"errors":{"msg":["您的信用账户余额不足"]}}',
		'-3'  => '{"errors":{"msg":["支付失败"]}}',
	);

	/**
	 * 获取所有的支付方式列表
	 * @return array
	 */
	public function getPaymentsList()
	{
		$paymetnsMapping = unserialize(PI_PAYMENTS_MAPPING);
		$paymentsList    = array();
		foreach($paymetnsMapping as $value){
			$className            = 'Payments'.ucfirst($value);
			$paymentsList[$value] = new $className;
		}
		return $paymentsList;
	}

	/**
	 * 获取票可用的支付方式
	 * @param int $ticketId 票id
	 * @param int $organizationId 购买者机构id
	 * @return array
	 */
	public function getUseAblePayments($ticketId, $organizationId)
	{
		$paymentsList         = $this->getPaymentsList();
		$ticketTemplatesModel = $this->load->model('ticketTemplates');
		$ticketInfo           = $ticketTemplatesModel->getID($ticketId);

		//票是否能使用信用支付
		$orderCommon          = $this->load->common('order');
		$saleprice            = $orderCommon->getSalePrice($ticketId, $organizationId, $isPartnerPrice, $allowCredit);
		$useAblePayments      = array();
		foreach($paymentsList as $key => $value){
			if($key == 'credit'){
				if($allowCredit == 'yes'){
					$useAblePayments[$key] = array(
						'appKey'      => $value->appKey,
						'appName'     => $value->appName,
						'displayName' => $value->displayName,
						'payType'     => $value->payType,
					);
				}
			}else{
				if($ticketInfo['payment'] == 'offline'){
					if($key == 'offline'){
						$useAblePayments[$key] = array(
							'appKey'      => $value->appKey,
							'appName'     => $value->appName,
							'displayName' => $value->displayName,
							'payType'     => $value->payType,
						);
					}
				}else{
					if($key != 'offline'){
						$useAblePayments[$key] = array(
							'appKey'      => $value->appKey,
							'appName'     => $value->appName,
							'displayName' => $value->displayName,
							'payType'     => $value->payType,
						);
					}
				}
			}
		}

		return $useAblePayments;
	}

	/**
	 * 检查使用信用支付的订单的总金额是否低于当前机构的信用额度
	 * @param int $mainId 发票的机构id
	 * @param int $partnerId 购买者机构id
	 * @param float $orderAmount 订单总价格
	 * @return bool
	 */
	public function checkCreditAndOrderAmount($mainId, $partnerId, $orderAmount, &$msg = '')
	{
		$curMoney = $this->load->common('partner')->getPartnerCredit($mainId, $partnerId);
		if($curMoney >= $orderAmount){
			return true;
		}else{
			$msg = $this->_getUserError(-2);
			return false;
		}
	}

	/**
	 * 获取支付方式的详细信息
	 * @params string $payment 支付方式key在Configs/PaymentsMapping.php中的值
	 * @return array
	 */
	public function getPaymentInfo($payment = '')
	{
		if(!$payment){
			return array(
				'app_name'         => '无支付方式',
				'app_staus'        => '关闭',
				'app_version'      => '1.0',
				'app_id'           => '无支付方式',
				'app_class'        => 'No Class',
				'app_description'  => '',
				'app_pay_type'     => 'online',
				'app_display_name' => '无支付方式',
				'app_info'         => '',
			);
		}

		$className    = '';
		$paymentsList = $this->getPaymentsList();
		foreach($paymentsList as $appKey => $appObj){
			$appClassName = get_class($appObj);
			if($appKey == $payment){
				$className  = $appClassName;
				$objPayment = $appObj;
				break;
			}
		}

		if($payment == 'offline'){
			$payType = 'offline';
		}elseif($payment == 'credit'){
			$payType = 'credit';
		}else{
			$payType = 'online';
		}

		if(!class_exists($className)){
			return array(
				'app_name'         =>$payment,
				'app_staus'        => '开启',
				'app_version'      => '1.0',
				'app_id'           => $payment,
				'app_class'        => $className,
				'app_description'  => '',
				'app_pay_type'     => $payType,
				'app_display_name' => $payment,
				'app_info'         => '',
			);
		}else{
			return array(
				'app_name'         => $objPayment->appName,
				'app_staus'        => '开启',
				'app_version'      => $objPayment->version,
				'app_id'           => $objPayment->appKey,
				'app_class'        => $className,
				'app_description'  => '',
				'app_pay_type'     => $payType,
				'app_display_name' => $objPayment->displayName,
				'app_info'         => '',
			);
		}
	}

	/**
	 * 修改支付单的状态
	 * @params array $payment 支付单的信息
	 * @params string $msg 错误信息
	 * @return bool
	 */
	public function updatePaymentStatus($payment, &$msg)
	{
		$updateArray = array(
			'status'      => $payment['status'],
			'last_modify' => time(),
		);

		$paymentsModel = $this->load->model('payments');
		$result        = $paymentsModel->update($updateArray, array('id'=> $payment['id']));
		$affectedRows  = $paymentsModel->affectedRows();
		if($affectedRows >= 1 && $result){
			return true;
		}else{
			$msg = $this->_getUserError(-3);
			return false;
		}
	}

	//获取支付方式名称
	public function getPaymentName($payment = '')
	{
		$paymentInfo = $this->getPaymentInfo($payment);
		return $paymentInfo['app_display_name'];
	}

	//获取支付单状态
	static public function getPaymentsStatus($status)
	{
		if($status){
			return self::$paymentsStatus[$status];
		}else{
			return self::$paymentsStatus;
		}
	}
}