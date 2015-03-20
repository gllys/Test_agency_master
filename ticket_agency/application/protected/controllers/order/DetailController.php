<?php
class DetailController extends Controller {

	public function actionIndex() {
		$data['status_labels'] = array(
			'unaudited' => '待确认',
			'reject' => '驳回',
			'unpaid' => '未支付',
			'cancel' => '已取消',
			'paid' => '已付款',
			'finish' => '已结束',
			'billed' => '已结款'
		);
		$data['paid_type'] = array(
			'cash' => '现金',
			'offline' => '线下',
			'credit' => '信用支付',
			'advance' => '储值支付',
			'union' => '平台支付',
			'alipay' => '支付宝',
			'kuaiqian' => '快钱'
		);
		$detail = Order::api()->detail(array(
			'show_order_items' => 1,
			'id' => $_GET['id'],
			'distributor_id' => Yii::app()->user->org_id
		), 0);
		if($detail['code'] == 'succ') {
			$data['detail'] = $detail['body'];
			$data['ticket'] = $detail['body']['order_items'];
			$param['order_id'] = $detail['body']['id'];
			$param['allow_status'] = 3;
			$result = Refund::api()->apply_list($param);
			if($result['code'] == 'succ' && ! empty($result['body']['data'])) {
				$sum = 0;
				$rs = $result['body']['data'];
				foreach( $rs as $value ) {
					$sum += $value['nums'];
				}
				$data['refund_num'] = $sum;
			} else {
				$data['refund_num'] = 0;
			}
		}
		$this->render('index', $data);
	}

	public function actionApply() {
		if(Yii::app()->request->isPostRequest) {
			$param = $_POST;
			$param['user_id'] = Yii::app()->user->org_id;
			$data = Refund::api()->apply($param, 0);
			
			if(ApiModel::isSucc($data)) {
				$this->_end(0, $data['message']);
			} else {
				$this->_end(1, $data['message']);
			}
		}
	}

	public function actionAgainSms() {
		if(Yii::app()->request->isPostRequest) {
			$rs = Order::api()->lists(array(
				'items' => 1,
				'distributor_id' => Yii::app()->user->org_id,
				'ids' => $_POST['id'],
				'fields' => 'id,name,nums,used_nums,use_day,refunding_nums,refunded_nums,distributor_id,distributor_name,owner_mobile,owner_name,send_sms_nums,landscape_ids,supplier_id'
			), 0);
			
			if($rs['code'] == 'succ') {
				$orderInfo = $rs['body']['data'][0];
				$orderInfo['nums'] = $orderInfo['nums'] - $orderInfo['used_nums'] - $orderInfo['refunding_nums'] - $orderInfo['refunded_nums'];
				$sms = new SMS();
				$orderInfo['host'] = Yii::app()->getRequest()->getHostInfo();
				$content = $sms->_getCreateOrderContent($orderInfo);
				$result = $sms->sendSMS($_POST['mobile'], $content,1,$orderInfo['id']);
				if($result) {
					echo json_encode(array(
						'errors' => '短信发送成功！！！'
					));
					exit();
				}
			}
			echo json_encode(array(
				'errors' => '短信发送失败！'
			));
		}
	}

	/**
	 * 订单在如下状态中，不能取消：
	 * cancel已取消，paid已支付,finish已结束,billed已支付
	 */
	public function actionCancel() {
		$param['id'] = $_POST['id'];	
		$data = Order::api()->detail(array(
			'id' => $param['id']
		));
		
		if($data['code'] != 'succ') {
			$this->_end(1, 'error');
		}

		// 以下状态中不能取消订单
		if(in_array($data['body']['status'], array('cancel', 'paid', 'finish', 'billed'))) {
			$this->_end(1, '订单已支付/关闭，无法取消');
		} else {
			$param['distributor_id'] = Yii::app()->user->org_id;
			$param['user_id'] = Yii::app()->user->uid;
			$param['user_name'] = Yii::app()->user->account;
			$param['status'] = 'cancel';
			$data = Order::api()->update($param);
			if($data['code'] == 'succ') {
				$this->_end(0, $data['message']);
			} else {
				$this->_end(1, $data['message']);
			}
		}
	}

	/**
	 * 重新提交订单
	 */
	public function actionRepost() {
		$param['id'] = $_POST['id'];
		$param['remark'] = $_POST['remark'];
		$param['distributor_id'] = Yii::app()->user->org_id;
		$param['user_id'] = Yii::app()->user->uid;
		$param['user_name'] = Yii::app()->user->account;
		$receiver_organization = $_POST['receiver_organization'];
		// $param['status'] = 'unaudited';不用提交状态，由服务端判断
		$data = Order::api()->update($param);
		if($data['code'] == 'succ') {
			$this->actionOrderMessage(array(
				'order_id' => $_POST['id'],
				'receiver_organization' => $receiver_organization
			));
			$this->_end(0, $data['message']);
		} else {
			$this->_end(1, $data['message']);
		}
	}
	
	/*
	 * 回传订单
	 * array $params 需要传输的数组
	 * receiver_organization 分销商id
	 * order_id 回传订单id
	 */
	public function actionOrderMessage($params = array()) {
		// 条件组织
		$order_id = $params['order_id'];
		$receiver_organization_id = $params['receiver_organization'];
		
		// 获取分销商名称
		$org_id = Yii::app()->user->org_id;
		$orgRs = Organizations::api()->show(array(
			'id' => $org_id
		));
		if($orgRs['code'] == 'succ') {
			$org_name = $orgRs['body']['name'];
		}
		
		// 组织内容语句
		
		$content = '分销商：' . $org_name . ',在' . date('Y-m-d H:i', time()) . '下回传订单，订单号：';
		$content .= '<a href="/order/detail/?id=' . $order_id . '">' . $order_id . '</a>' . '，请确认';
		
		$param = array(
			'sys_type' => 6,
			'send_source' => 1,
			'send_status' => 1,
			'sms_type' => 1,
			'content' => $content, // 内容
			'send_organization' => $org_id,
			'receiver_organization' => $receiver_organization_id
		); // 供应商ID

		
		Message::api()->add($param);
	}
}
