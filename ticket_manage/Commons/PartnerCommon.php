<?php
/**
 *  合作伙伴
 * 
 * 2014-1-15
 *
 * @author  cyl
 * @version 1.0
 */
class PartnerCommon extends BaseCommon
{
	protected $_code = array(
		'-1'  => '{"errors":{"msg":["null post"]}}',
		'-2'  => '{"errors":{"msg":["不存在的合作信息"]}}',
		'-3'  => '{"errors":{"msg":["保存信用额度记录失败"]}}',
		'-4'  => '{"errors":{"msg":["错误的信用记录类型"]}}',
		'-5'  => '{"errors":{"msg":["错误的支付单信息"]}}',
		'-6'  => '{"errors":{"msg":["更新信用额度失败"]}}',
	);

	//保存设置 todo check
	public function savePartnerSetting($post)
	{
		if($post){
			$organizationPartnerModel = $this->load->model('organizationPartner');
			$oldData                  = $organizationPartnerModel->getID($post['id']);
			if(!$oldData){
				return $this->_getUserError(-2);
			}

			$postData                 = $this->_formatPartnerSetting($post, $oldData);
			$organizationPartnerModel->begin();
			$result                   = $organizationPartnerModel->update($postData, array('id'=>$post['id']));

			//更新失败 rollback
			$affectedRows = $organizationPartnerModel->affectedRows();
			if($affectedRows < 1 || !$result){
				$organizationPartnerModel->rollback();
				return $this->_getUserError(-2);
			}

			// 假如增加了信用额度，保存增加信用额度记录
			if($post['add_money'] != 0 && is_numeric($post['add_money'])){
				$saveOrganizationCreditLogResult = $this->partnerAddOrganizationCreditLog($post['add_money'], $oldData, $post['memo'], $msg);
				if(!$saveOrganizationCreditLogResult){
					$organizationPartnerModel->rollback();
					return $msg;
				}
			}

			$organizationPartnerModel->commit();
			return json_encode(array('data' => array($postData)));
		}else{
			return $this->_getUserError(-1);
		}

	}

	//将表单数据
	private function _formatPartnerSetting($post, $oldData)
	{
		$now        = date('Y-m-d H:i:s', time());
		$postData = array(
			'id'                 => $post['id'],
			'cur_money'          => $oldData['cur_money'] + $post['add_money'],
			'price_templates_id' => $post['price_templates_id'],
			'last_modify'        => time(),
			'account_cycle'      => $post['account_cycle'],
			'account_cycle_day'  => $post['account_cycle_day'],
		);

		return $postData;
	}

	//这里暂时只做后台的log，假如是支付或者退款的log另行写方法
	public function partnerAddOrganizationCreditLog($addMoney, $oldData, $memo, &$msg)
	{
		$formatData                 = $this->_formatPartnerAddCreditLog($addMoney, $oldData,  $memo);
		$organizationPartnerAjustLogModel = $this->load->model('organizationPartnerAjustLog');
		$result                     = $organizationPartnerAjustLogModel->add($formatData);
		$affectedRows               = $organizationPartnerAjustLogModel->affectedRows();
		if($affectedRows >= 1 && $result){
			return true;
		}else{
			$msg = $this->_getUserError(-3);
			return false;
		}
	}

	/**
	 * 信用额度数据格式
	 *
	 * @param int $addMoney 操作金额
	 * @param mixed $oldData 未修改前的合作机构信息
	 * @param string 备注
	 * @return bool
	 */
	private function _formatPartnerAddCreditLog($addMoney, $oldData, $memo)
	{
		$now        = time();
		$operator   = $_SESSION['backend_userinfo']['account'];
		if($addMoney > 0) {
			$money         = $addMoney;
			$operationType = 'import';
			$remark        = '合作机构管理员'.$operator.'增加'.$money;
		} else {
			$money         = abs($addMoney);
			$operationType = 'export';
			$remark        = '合作机构管理员'.$operator.'减少'.$money;
		}

		$postData = array(
			'organization_main_id'    => $oldData['organization_main_id'],
			'organization_partner_id' => $oldData['organization_partner_id'],
			'money'                   => $money,
			'remark'                  => $remark,
			'mtime'                   => $now,
			'bill_type'               => 'partner',
			'operator_id'             => $_SESSION['backend_userinfo']['id'],
			'operation_type'          => $operationType,
			'cur_money'               => $oldData['cur_money'] + $addMoney,
			'memo'                    => $memo,
		);
		return $postData;
	}

	/**
	 * 获取机构信用余额
	 * @param int $mainId 主机构id，即发信用额度机构
	 * @param int $partnerId 合作者机构id
	 * @param bool $isNormal 是否要查找通过审核的合作
	 * @return float
	 */
	public function getPartnerCredit($mainId, $partnerId, $isNormal = true)
	{
		$organizationPartnerModel = $this->load->model('organizationPartner');
		$filter                   = array(
			'organization_main_id'    => $mainId,
			'organization_partner_id' => $partnerId,
			'status'                  => 'normal',
		);

		$partnerInfo              = $organizationPartnerModel->getOne($filter, '', 'cur_money');
		$curMoney                 = $partnerInfo['cur_money'] ? $partnerInfo['cur_money'] : 0;
		return $curMoney;
	}

	//保存支付单或者退款单的信用支付日志
	public function savePartnerCreditLog($billInfo, $type, &$msg = '')
	{
		if(!in_array($type, array('payments', 'refunds'))){
			$msg = $this->_getUserError('-4');
			return false;
		}

		$saveData                   = $this->_formatPartnerCreditLog($billInfo, $type);
		$organizationPartnerAjustLogModel = $this->load->model('organizationPartnerAjustLog');
		$result                     = $organizationPartnerAjustLogModel->add($saveData);
		$affectedRows               = $organizationPartnerAjustLogModel->affectedRows();
		if($affectedRows >= 1 && $result){
			return true;
		}else{
			$msg = $this->_getUserError(-3);
			return false;
		}
	}

	public function _formatPartnerCreditLog($billInfo, $type)
	{
		$orderModel               = $this->load->model('orders');
		$orderInfo                = $orderModel->getOne('id='.$billInfo['order_id']);
		$now                      = time();
		$operator                 = $_SESSION['backend_userinfo']['account'];
		$organizationPartnerModel = $this->load->model('organizationPartner');
		$oldData                  = $organizationPartnerModel->getOne(array('organization_main_id'=> $orderInfo['seller_organization_id'],'organization_partner_id'=>$orderInfo['buyer_organization_id']));

		if($type == 'payments'){
			$remark        = '订单'.$billInfo['order_id'].',支付金额`'.$billInfo['money'].'`,支付单为'.$billInfo['id'];
			$operationType = 'export';
			$curMoney      = $oldData['cur_money'] - $billInfo['money'];
		}else{
			$remark        = '订单'.$billInfo['order_id'].',退款金额`'.$billInfo['money'].'`,退款单为'.$billInfo['id'];
			$operationType = 'import';
			$curMoney      = $oldData['cur_money'] + $billInfo['money'];
		}

		$postData = array(
			'organization_main_id'    => $orderInfo['seller_organization_id'],
			'organization_partner_id' => $orderInfo['buyer_organization_id'],
			'money'                   => $billInfo['money'],
			'remark'                  => $remark,
			'mtime'                   => $now,
			'bill_type'               => $type,
			'operator_id'             => $_SESSION['backend_userinfo']['id'],
			'operation_type'          => $operationType,
			'cur_money'               => $curMoney,
			'bill_id'                 => $billInfo['id'],
			'order_id'                => $billInfo['order_id'],
		);
		return $postData;
	}

	/**
	 * 支付扣除信用额度
	 *
	 * @param mixed $paymentInfo 支付单的信息
	 * @param string $msg 返回错误信息
	 * @return bool
	 */
	public function deductPartnerCredit($paymentInfo, &$msg = '')
	{
		$orderModel = $this->load->model('orders');
		$orderInfo  = $orderModel->getID($paymentInfo['order_id']);
		if(!$orderInfo){
			$msg = $this->_getUserError(-4);
			return false;
		}

		$paymentsCommon = $this->load->common('payments');
		$enough         = $paymentsCommon->checkCreditAndOrderAmount($orderInfo['seller_organization_id'], $orderInfo['buyer_organization_id'], $paymentInfo['money'], $msg);
		if($enough){
			$organizationPartnerModel = $this->load->model('organizationPartner');
			$partnerInfo              = $organizationPartnerModel->isOrganizationPartner($orderInfo['seller_organization_id'], $orderInfo['buyer_organization_id']);
			if($partnerInfo){
				//保存日志
				$saveLogResult = $this->savePartnerCreditLog($paymentInfo, 'payments', $msg);
				if($saveLogResult){
					//扣除信用额度
					$curMoney     = $partnerInfo['cur_money'] - $paymentInfo['money'];
					$result       = $organizationPartnerModel->update(array('last_modify'=>time(), 'cur_money'=>$curMoney), array('id'=> $partnerInfo['id']));
					$affectedRows = $organizationPartnerModel->affectedRows();
					if($result && $affectedRows >= 1){
						return true;
					}else{
						$msg = $this->_getUserError(-6);
						return false;
					}
				}else{
					return false;
				}
			}else{
				$msg = $this->_getUserError(-2);
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * 退款增加信用额度
	 *
	 * @param mixed $refundInfo 退款单的信息
	 * @param string $msg 返回错误信息
	 * @return bool
	 */
	public function increasePartnerCredit($refundInfo, &$msg = '')
	{
		$orderModel = $this->load->model('orders');
		$orderInfo  = $orderModel->getID($refundInfo['order_id']);
		if(!$orderInfo){
			$msg = $this->_getUserError(-4);
			return false;
		}

		$organizationPartnerModel = $this->load->model('organizationPartner');
		$partnerInfo              = $organizationPartnerModel->getPartnerInfo($orderInfo['seller_organization_id'], $orderInfo['buyer_organization_id']);
		$curMoney                 = $this->getPartnerCredit($orderInfo['seller_organization_id'], $orderInfo['buyer_organization_id'], false);
		if($partnerInfo){
			//保存日志
			$saveLogResult = $this->savePartnerCreditLog($refundInfo, 'refunds', $msg);
			if($saveLogResult){
				//增加信用额度
				$curMoney     = $partnerInfo['cur_money'] + $refundInfo['money'];
				$result       = $organizationPartnerModel->update(array('cur_money'=>$curMoney), array('id'=> $partnerInfo['id']));
				$affectedRows = $organizationPartnerModel->affectedRows();
				if($result && $affectedRows >= 1){
					return true;
				}else{
					$msg = $this->_getUserError(-6);
					return false;
				}
			}else{
				return false;
			}
		}else{
			$msg = $this->_getUserError(-2);
			return false;
		}
	}

	/**
	 * 退款增加信用额度
	 *
	 * @param mixed $refundInfo 退款单的信息
	 * @param string $msg 返回错误信息
	 * @return bool
	 */
	public function increasePartnerMoney($refundInfo, $type, &$msg = '')
	{
	    //信用支付或者储值支付
	    if(!in_array($type, array('credit', 'advance'))) {
	        $msg = $this->_getUserError(-11);
	        return false;
	    }
	
	    $orderModel = $this->load->model('orders');
	    $orderInfo  = $orderModel->getID($refundInfo['order_id']);
	    if(!$orderInfo){
	        $msg = $this->_getUserError(-4);
	        return false;
	    }
	
	    $organizationPartnerModel = $this->load->model('organizationPartner');
	    $partnerInfo              = $organizationPartnerModel->getPartnerInfo($orderInfo['seller_organization_id'], $orderInfo['buyer_organization_id']);
	    $curMoney                 = $this->getPartnerMoney($orderInfo['seller_organization_id'], $orderInfo['buyer_organization_id'], $type, false);
	    if($partnerInfo){
	        //保存日志
	        $saveLogResult = $this->savePartnerAjustLog($refundInfo, $type, 'refunds', $msg);
	        if($saveLogResult){
	            //增加信用额度或储值
	            if($type == 'credit') {
	                $updateCols   = 'cur_money';
	            } elseif($type == 'advance') {
	                $updateCols   = 'cur_advance';
	            }
	            $curMoney     = $partnerInfo[$updateCols] + $refundInfo['money'];
	            $result       = $organizationPartnerModel->update(array($updateCols => $curMoney), array('id'=> $partnerInfo['id']));
	            $affectedRows = $organizationPartnerModel->affectedRows();
	            if($result && $affectedRows >= 1){
	                return true;
	            }else{
	                $msg = $this->_getUserError(-6);
	                return false;
	            }
	        }else{
	            return false;
	        }
	    }else{
	        $msg = $this->_getUserError(-2);
	        return false;
	    }
	}
	/**
	 * 获取机构信用余额
	 * @param int $mainId 主机构id，即发信用额度机构
	 * @param int $partnerId 合作者机构id
	 * @param string $type 金额类型
	 * @param bool $normalOnly 是否只查找通过审核的合作
	 * @return float
	 */
	public function getPartnerMoney($mainId, $partnerId, $type, $normalOnly = true)
	{
	    $organizationPartnerModel = $this->load->model('organizationPartner');
	    $filter                   = array(
	            'organization_main_id'    => $mainId,
	            'organization_partner_id' => $partnerId,
	            	
	    );
	
	    //只查找通过申请的
	    if($normalOnly === true) {
	        $filter['status'] = 'normal';
	    }
	
	    if($type == 'credit') {
	        $cols = 'cur_money';
	    } else if($type == 'advance') {
	        $cols = 'cur_advance';
	    } else {
	        return 0;
	    }
	    $partnerInfo              = $organizationPartnerModel->getOne($filter, '', $cols);
	    $curMoney                 = $partnerInfo[$cols] ? $partnerInfo[$cols] : 0;
	    return $curMoney;
	}

	//保存支付单或者退款单的信用支付日志
	public function savePartnerAjustLog($billInfo, $payment, $type, &$msg = '')
	{
	    if(!in_array($payment, array('credit', 'advance'))){
	        $msg = $this->_getUserError('-11');
	        return false;
	    }
	
	    if(!in_array($type, array('payments', 'refunds'))){
	        $msg = $this->_getUserError('-4');
	        return false;
	    }
	
	    $saveData                   = $this->_formatPartnerBillAjustLog($billInfo, $payment, $type);
	    $organizationPartnerAjustLogModel = $this->load->model('organizationPartnerAjustLog');
	    $result                     = $organizationPartnerAjustLogModel->add($saveData);
	    $affectedRows               = $organizationPartnerAjustLogModel->affectedRows();
	    if($affectedRows >= 1 && $result){
	        return true;
	    }else{
	        $msg = $this->_getUserError(-3);
	        return false;
	    }
	}
	public function _formatPartnerBillAjustLog($billInfo, $payment, $type)
	{
	    $orderModel               = $this->load->model('orders');
	    $orderInfo                = $orderModel->getID($billInfo['order_id']);
	    $now                      = date('Y-m-d H:i:s');
	    $operator                 = $_SESSION['backend_userinfo']['account'];
	    $organizationPartnerModel = $this->load->model('organizationPartner');
	    $oldData                  = $organizationPartnerModel->getOne(array('organization_main_id'=> $orderInfo['seller_organization_id'],'organization_partner_id'=>$orderInfo['buyer_organization_id']));
	
	    if($type == 'payments'){
	        $remark        = '订单'.$billInfo['order_id'].',支付金额`'.$billInfo['money'].'`,支付单为'.$billInfo['id'];
	        $operationType = 'export';
	        if($payment == 'credit') {
	            $curMoney      = $oldData['cur_money'] - $billInfo['money'];
	        } elseif($payment == 'advance') {
	            $curMoney      = $oldData['cur_advance'] - $billInfo['money'];
	        }
	    }else{
	        $remark        = '订单'.$billInfo['order_id'].',退款金额`'.$billInfo['money'].'`,退款单为'.$billInfo['id'];
	        $operationType = 'import';
	
	        if($payment == 'credit') {
	            $curMoney      = $oldData['cur_money'] + $billInfo['money'];
	        } elseif($payment == 'advance'){
	            $curMoney      = $oldData['cur_advance'] + $billInfo['money'];
	        }
	    }
	
	    $postData = array(
	            'organization_main_id'    => $orderInfo['seller_organization_id'],
	            'organization_partner_id' => $orderInfo['buyer_organization_id'],
	            'money'                   => $billInfo['money'],
	            'remark'                  => $remark,
	            'mtime'                   => $now,
	            'bill_type'               => $type,
	            'operator_id'             => $_SESSION['backend_userinfo']['account'],
	            'operation_type'          => $operationType,
	            'cur_money'               => $curMoney,
	            'bill_id'                 => $billInfo['id'],
	            'order_id'                => $billInfo['order_id'],
	    );
	    return $postData;
	}
}