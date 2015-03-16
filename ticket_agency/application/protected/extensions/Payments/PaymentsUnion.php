<?php

final class PaymentsUnion {

    public $appKey = 'union';
    public $appName = '平台支付';
    public $displayName = '平台支付';
    public $payType = 'union';
    public $version = '1.0';
    public $msg = '' ;
    public function __construct() {
        //$this->load  = new Load();
    }

    public function doPay($payment, $orderInfo, $ext = null) {
        if ((int) $payment['id'] > 0 && !is_null($ext)) {
            $result = Payment::api()->lists(array(
                'id' => $payment['id'],
            ));
            if ($result['code'] == 'succ') {
                $order_ids = $result['body']['data'][0]['order_ids'];
                if ($result['body']['data'][0]['status'] != 'ready') {
                    Yii::log('{"errors":{"msg":["错误的支付单状态"]}}');
                    $this->msg = $result['message'];
                    return false;
                }
            } else {
                Yii::log('{"errors":{"msg":["错误的支付单号"]}}');
                $this->msg = $result['message'];
                return false;
            }
        }
        $order_ids = explode(',', $order_ids);

        $result = Order::api()->lists(array(
            'items' => 50,
            'distributor_id' => Yii::app()->user->org_id,
            'ids' => $order_ids,
            'fields' => 'id,nums,amount,use_day,supplier_id',
            'status' => 'unpaid'
            ), 0);

        if ($result['code'] == 'succ') {
            $data['orders'] = $result['body']['data'];
            if (!$data['orders']) {
                $this->redirect('/order/history/');
            }
            $supplier_ids = array();
            foreach ($data['orders'] as $order) {
                $supplier_ids[$order['supplier_id']] = $order['supplier_id'];
            }
        }



        $result = Unionmoney::api()->inout(array(
            'org_id' => Yii::app()->user->org_id,
            'user_id' => Yii::app()->user->uid,
            'user_name' => Yii::app()->user->account,
            'money' => $payment['amount'],
            'trade_type' => 1,
            'in_out' => 0,
            'remark' => $payment['id'],
        ));

        if ($result['code'] == 'succ') {
            Yii::log('{"info":{"msg":["支付单' . $payment['id'] . '平台支付成功"]}}');
            $SmsHandler = new SMS();
            $SmsHandler->sendPaymentMsg($payment['id']);
            $result = Payment::api()->update(array(
                'id' => $payment['id'],
                'user_id' => Yii::app()->user->uid,
                'user_name' => Yii::app()->user->account,
                'status' => 'succ',
                'payment' => $this->payType,
                'distributor_id' => Yii::app()->user->org_id
            ));
            if ($result['code'] == 'fail') {
                Yii::log('{"errors":{"msg":["支付单' . $payment['id'] . '平台支付成功，更新支付状态失败"]}}');
                $this->msg = $result['message'];
                return false;
            } else {
                header('Location:/order/payments/completed/id/' . $payment['id']);
            }
        } else {
            $this->msg = $result['message'];
            return false;
        }
    }

    public function doPayList($payment, &$msg) {
        $orderIds = unserialize($payment['order_list']);
        if (!$payment['organization_id']) {
            return false;
        }
        if (!$paymentsCommon->checkUnionOrderAmount($payment['organization_id'], $payment['money'], $msg)) {
            return false;
        }
        foreach ($orderIds as $orderId) {
            
        }

        $orgInfo = $this->load->model('organizations')->getOne(array('id' => $payment['organization_id']));
        $userInfo = $this->load->model('users')->getOne(array('id' => $payment['u_id']));

        $payment['status'] = 'succ';

        $paymentsModel = $this->load->model('payments');
        $paymentsModel->begin();
        //更新支付单状态
        $result = $paymentsModel->update(array('status' => $payment['status']), array('id' => $payment['id']));
        if ($result) {
            //支付完成，更新订单状态，信用支付扣除信用额度
            $result = $this->load->common('order')->paymentFinish($payment, true);
            if ($result) {
                //日志
                $this->load->model('moneyPay')->add(array(
                    'organization_id' => $orgInfo['id'],
                    'type' => 3,
                    'num' => $payment['money'],
                    'before_num' => $orgInfo['money'],
                    'after_num' => $orgInfo['money'] - $payment['money'],
                    'state' => 1,
                    'user_id' => $userInfo['id'],
                    'user_account' => $userInfo['account'],
                ));

                $paymentsModel->commit();
                return true;
            } else {
                $paymentsModel->rollback();
                return false;
            }
        } else {
            $paymentsModel->rollback();
            return false;
        }
    }

}
