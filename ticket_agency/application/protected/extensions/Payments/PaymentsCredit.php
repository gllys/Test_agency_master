<?php

final class PaymentsCredit {

    public $appKey = 'credit';
    public $appName = '信用支付';
    public $displayName = '信用支付';
    public $payType = 'credit';
    public $version = '1.0';
    public $msg = '';

    public function __construct() {
        
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
                    $this->msg = '错误的支付单状态';
                    return false;
                }
            } else {
                $this->msg = $result['message'];
                Yii::log('{"errors":{"msg":["错误的支付单号"]}}');
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
        $data['only_one_supplier'] = false;
        if ($result['code'] == 'succ') {
            $data['orders'] = $result['body']['data'];
            if (!$data['orders']) {
                $this->redirect('/order/history/');
            }
            $supplier_ids = array();
            foreach ($data['orders'] as $order) {
                $supplier_ids[$order['supplier_id']] = $order['supplier_id'];
            }
            //合并支付，订单源于同一家供应商时方可信用偖值支付
            $data['only_one_supplier'] = count($supplier_ids) === 1;
        } else {
            $this->msg = $result['message'];
            return false;
        }

        if ($data['only_one_supplier']) {
            $supplier_id = reset($supplier_ids);
        } else {
            Yii::log('{"errors":{"msg":["错误的支付单号，不可用信用、储值支付"]}}');
            $this->msg = '错误的支付单号，不可用信用、储值支付';
            return false;
        }


        $result = Credit::api()->pay(array(
            'type' => $ext,
            'serial_id' => $payment['id'],
            'distributor_id' => Yii::app()->user->org_id,
            'supplier_id' => $supplier_id,
            'money' => $payment['amount']
        ));
        if ($result['code'] == 'succ') {
            Yii::log('{"info":{"msg":["支付单' . $payment['id'] . '信用、储值支付成功"]}}');
            $SmsHandler = new SMS();
            $SmsHandler->sendPaymentMsg($payment['id']);
            $result = Payment::api()->update(array(
                'id' => $payment['id'],
                'user_id' => Yii::app()->user->uid,
                'user_name' => Yii::app()->user->account,
                'status' => 'succ',
                'payment' => $payment['payment'],
                'distributor_id' => Yii::app()->user->org_id
            ));
            if ($result['code'] == 'fail') {
                Yii::log('{"errors":{"msg":["支付单' . $payment['id'] . '信用、储值支付成功，更新支付状态失败"]}}');
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

}
