<?php

/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/25/14
 * Time: 8:33 PM
 */
class PaymentsController extends Controller {

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('api'),
                'users' => array('*'),
            ),
        );
    }

    public function filters() {
        return array('accessControl',);
    }

    public function actionMethod() {

        $order_ids = Yii::app()->request->getParam('combine');
        if (is_scalar($order_ids)) {
            $order_ids = explode(',', $order_ids);
        }
        if (empty($order_ids)) {
            $pid = Yii::app()->request->getParam('pid');
            if ((int) $pid > 0) {
                $result = Payment::api()->lists(array(
                    'id' => $pid,
                ), 0);
                if ($result['code'] == 'succ') {
                    $order_ids = $result['body']['data'][0]['order_ids'];
                    if ($result['body']['data'][0]['status'] == 'paid' || $result['body']['data'][0]['status'] == 'succ') {
                        $this->redirect('/order/payments/completed/id/' . $pid);
                    }
                    if ($result['body']['data'][0]['status'] != 'ready') {
                        $this->redirect('/order/history/');
                    }
                    //45分钟失效
                    if ((time() - $result['body']['data'][0]['created_at']) > 60 * 45) {
                        Payment::api()->update(array(
                            'id' => $pid,
                            'user_id' => Yii::app()->user->uid,
                            'user_name' => Yii::app()->user->account,
                            'status' => 'timeout',
                            'distributor_id' => Yii::app()->user->org_id
                        ));
                    }
                }
            }
        } else {
            $order_ids = implode(',', $order_ids);
        }
        if ($order_ids == '') {
            $this->fail('订单不能为空', '/order/history/', '下一步：跳转到订单管理页面');
        }


        $result = Order::api()->lists( array(
            'with_store' => 1,
            'items' => 50,
            'distributor_id' => Yii::app()->user->org_id,
            'ids' => $order_ids,
            'fields' => 'id,name,product_payment,product_id,type,nums,amount,use_day,supplier_id,status,payment_id,nums,price_type,distributor_id',
        )
    );
        $data['only_one_supplier'] = false;
        $e_order_ids = array();

        if ($result['code'] == 'succ') {
            $orders = $result['body']['data'];
            if (!$orders) {
                $this->redirect('/order/history/');
            } elseif (count($orders) == 1 && $orders[0]['status'] == 'paid') {
                $this->redirect('/order/payments/completed/id/' . $orders[0]['payment_id']);
            } else {
                $paid = 0;
                foreach ($orders as $order) {
                    $paid += $orders[0]['status'] == 'paid' ? 1 : 0;
                }
                unset($order);
                if (count($orders) == $paid) {
                    $this->redirect('/order/payments/completed/id/' . $orders[0]['payment_id']);
                }
            }
            $data['storage'] = $result['body']['with_store'];
            $supplier_ids = array();
            $data['paymenttype'] = '';
            $data['orders'] = $data['renwus'] =$arr_payment = array();

            foreach ($orders as $k => $order) {
                //合并支付时，支付方式应为同一支付方式 array_intersect()
                if($k == 0){
                    $arr_payment = explode(",",$order['product_payment']);
                }else{
                    $arr_payment = array_intersect($arr_payment,explode(",",$order['product_payment']));
                }
                $data['paymenttype'] = implode(",",$arr_payment);
                if ($order['type'] == 1) {
                    $data['renwus'][] = $order;
                } else {
                    $data['orders'][] = $order;
                    $e_order_ids[] = $order['id'];
                    $supplier_ids[$order['supplier_id']] = $order['supplier_id'];
                }
            }
            //判断订单中的支付方式
            $data['paytype'] = array_filter(array_unique(explode(',',$data['paymenttype'])));
            //合并支付，订单源于同一家供应商时方可信用偖值支付
            $data['only_one_supplier'] = count($supplier_ids) === 1;
        } else {
            $this->fail($result['message'], '/order/history/', '下一步：跳转到订单管理页面');
        }

        $data['order_ids'] = $order_ids;
        $data['e_order_ids'] = implode(',', $e_order_ids);
        if ($data['only_one_supplier']) {
            $result = Credit::api()->getMoney(array(
                'distributor_id' => Yii::app()->user->org_id,
                'supplier_id' => reset($supplier_ids)
                ), 0);
            if ($result['code'] == 'succ') {
                $data = array_merge($data, $result['body']);
            }
        }

        /* 平台金额  抵用券金额*/
        $data['unionmoney'] = 0;
        $data['activity'] = 0;
        $org_id = Yii::app()->user->org_id;
        $rs = Unionmoney::api()->total(array('org_ids'=>$org_id));
        if (isset($rs['code']) && $rs['code'] == 'succ') {
            $data['unionmoney'] = $rs['body']['total_union_money'];
            $data['activity']   = $rs['body']['total_activity_money'];
        }
        $this->render('method', $data);
    }

    /**
     * 生成支付流水单，并跳转到支付方式页面
     * /order/payments/prepay/combine/
     * @author grg
     */
    public function actionPrepay() {
        Yii::import('application.extensions.Payments.*');
        $order_ids = Yii::app()->request->getParam('combine');
        if (is_array($order_ids)) {
            $order_ids = implode(',', $order_ids);
        }
        if ($order_ids == '') {
            exit;
        }
        $method = Yii::app()->request->getParam('method');
        if ($method == '' || !in_array($method, array('alipay','kuaiqian', 'credit_0', 'credit_1', 'union_4'))) {
            $this->redirect('/order/payments/method/combine/' . $order_ids);
        }
        $ext = null;
        if (strpos($method, '_')) {
            list($method, $ext) = explode('_', $method);
        }
        $activity_money = 0;
        if(isset($_REQUEST['is_activity']) && $_REQUEST['is_activity'] == 1){
            $activity_money = isset($_REQUEST['activity_paid'])?$_REQUEST['activity_paid']:'0';
        }
        $condition = array(
            'payment' => isset($ext) && $ext == 1 ? 'advance' : $method,
            'order_ids' => $order_ids,
            'activity_paid' => $activity_money,
            'user_id' => Yii::app()->user->uid,
            'user_name' => Yii::app()->user->account,
            'distributor_id' => Yii::app()->user->org_id
        );

        $result = Payment::api()->add($condition, 0);
        if ($result['code'] == 'succ' && $result['body']['status'] == 'ready') {
            $p_method = isset($ext) && $ext == 1 ? 'advance' : $method;
            if ($p_method != $result['body']['payment']  ||  $activity_money > 0) {
                Payment::api()->update(array(
                    'id' => $result['body']['id'],
                    'distributor_id' => Yii::app()->user->org_id,
                    'payment' => $p_method,
                    'activity_paid' => $activity_money,
                    'user_id' => Yii::app()->user->uid,
                    'user_name' => Yii::app()->user->account
                    ), 0);
            }
            $params = $result['body'];
            $params['amount'] = intval(100 * $params['amount'] - 100 * $activity_money) / 100;
            $params['payment'] = $p_method;
            $result = Order::api()->lists(array(
                'with_store' => 1,
                'items' => 50,
                'distributor_id' => Yii::app()->user->org_id,
                'ids' => $order_ids,
                'fields' => 'id,nums,name,amount,activity_paid,use_day,product_id,type,supplier_id,status,payment_id,price_type,distributor_id',
                'status' => 'unpaid'
                ), 0);
            if ($result['code'] == 'succ') {
                $orders = $result['body']['data'];
                $info = array(
                    'subject' => empty($orders[0]['name'])?'等':$orders[0]['name']. '等'
                );
                $is_limit = false; // 库存受限
                $store = $result['body']['with_store']; //var_dump($result['body']);exit;
                foreach ($orders as $order) {
                    $k = "{$order['product_id']}_{$order['use_day']}";
                    if (array_key_exists($k, $store) && isset($store[$k]['remain_reserve']) && !is_null($store[$k]['remain_reserve'])) {
                        $store[$k]['remain_reserve'] -= $order['nums'];
                        $is_limit |= $store[$k]['remain_reserve'] < 0;
                    }
                }
                unset($order);
                if ($is_limit) {
                    $this->fail('库存不足支付出错啦！', '/order/payments/method/combine/' . $order_ids, '下一步：跳转到支付页面');
                }
            } else {
                $this->fail('支付出错啦！', '/order/payments/method/combine/' . $order_ids, '下一步：跳转到支付页面');
            }
            $last_num = intval($params['amount'] * 100);
            if ($last_num === 0) {
                $rs = Payment::api()->update(array(
                    'id' => $params['id'],
                    'distributor_id' => Yii::app()->user->org_id,
                    'status' => 'succ',
                    'payment' => isset($ext) && $ext == 1 ? 'advance' : $method,
                    'user_id' => Yii::app()->user->uid,
                    'user_name' => Yii::app()->user->account
                ));
                if ($result['code'] != 'succ') {
                    $this->fail($result['message'], '/order/payments/method/combine/' . $order_ids, '下一步：跳转到支付页面');
                    Yii::app()->end();
                }
                $SmsHandler = new SMS();
                $SmsHandler->sendPaymentMsg($params['id']);
                $this->redirect('/order/payments/completed/id/' . $params['id']);
            }
            if ($params['amount'] < 0) {
                $params['amount'] = 0;
            }
            echo $class = 'Payments' . ucfirst($method);
            $payment = new $class();
            $re = $payment->doPay($params, $info, $ext);
            if ($re === false) {
                $msg =  property_exists($payment,'msg')&&$payment->msg ?  $payment->msg : '支付出错啦！' ;
                $this->fail($msg, '/order/payments/method/combine/' . $order_ids, '下一步：跳转到支付页面');
            }
        } else {
            $this->fail('支付出错啦！', '/order/payments/method/combine/' . $order_ids, '下一步：跳转到支付页面');
        }
    }

    /**
     * /order/payments/api/callback/sync/way/99bill
     * ?dealTime=20141029114515
     * &payAmount=1
     * &signType=4
     * &errCode=
     * &merchantAcctId=1002354435101
     * &orderTime=19700101080000
     * &dealId=1693744943
     * &version=v2.0
     * &bankId=CMB
     * &fee=1
     * &bankDealId=8681476314
     * &payResult=10
     * &ext1=order_id%3A166067867620122%3B
     * &ext2=
     * &orderAmount=1
     * &signMsg=I%2FAofHVAzU2KITYgh1BWlKse8knrwQTmqEgei1hFWxiEePzqiZzaIxkwVweGbJG7WKjjIby6BBUneYX4MWR892q%2BNfbdINug%2BMdQOrV02C2fJsSpzsF0hEZg068zTbqE5oeq99497vHON1jNva0rnVVzvNTfF6aHgX5GDfjxGCOCds3%2BuDpWvfa5IUD%2BnN1JP%2BYlDOPcawzoiAa1OYHXjJJuYZQJWoQuUU4xEjEd4U%2BVrj8mi7y53Dd3AajfxCfKP224E7C7UekVClyoKuDuZBt39j%2B9B4HfFL%2BX1vw2CIdd6dm%2F1Si16ScaSk9dZDcGtgcTYAP4Cat9BMhFlRAvbQ%3D%3D
     * &payType=10
     * &language=1
     * &orderId=201410261547549302
     * @author grg
     */
    public function actionApi() {
        Yii::import('application.extensions.Payments.*');

        $way = Yii::app()->request->getParam('way');
        if ($way == '99bill') {
            $type = Yii::app()->request->getParam('callback');
            $func = $type . 'Callback';
            $payment = new PaymentsKuaiqian();
            $result = $payment->$func();
            if ($result['result'] == 1) {//成功
                $SmsHandler = new SMS();
                $SmsHandler->sendPaymentMsg($result['orderId']);
            }
            if ($type == 'async' || Yii::app()->user->isGuest) {
                $url = 'http://' . $_SERVER['HTTP_HOST'] . '/order/payments/completed/id/' . $result['orderId'];
                echo "<result>{$result['result']}</result><redirecturl>$url</redirecturl>";
                exit();
            } else {
                $this->redirect('/order/payments/completed/id/' . $result['orderId']);
            }
        }else if ($way == 'alipaybill') {
            $type = Yii::app()->request->getParam('callback');
            $func = $type . 'Callback';
            $payment = new PaymentsAlipay();
            $result = $payment->$func();
            if ($result['result'] == 1) {//成功
                $SmsHandler = new SMS();
                $SmsHandler->sendPaymentMsg($result['id']);
            }
            if ($type == 'async' || Yii::app()->user->isGuest) {
                $url = 'http://' . $_SERVER['HTTP_HOST'] . '/order/payments/completed/id/' . $result['id'];
                echo "<result>{$result['result']}</result><redirecturl>$url</redirecturl>";
                exit();
            } else {
                $this->redirect('/order/payments/completed/id/' . $result['id']);
            }
        }
    }

    public function actionCancel() {
        $pid = Yii::app()->request->getParam('pid');
        if ((int) $pid > 0) {
            Payment::api()->update(array(
                'id' => $pid,
                'distributor_id' => Yii::app()->user->org_id,
                'status' => 'fail',
                'payment' => 'kuaiqian',
                'user_id' => Yii::app()->user->uid,
                'user_name' => Yii::app()->user->account
            ));
        }
        $this->redirect('/order/history/');
    }

    public function actionCompleted() {
        $pid = Yii::app()->request->getParam('id');
        if ((int) $pid > 0) {
            $result = Payment::api()->lists(array(
                'id' => $pid,
                ), 0);
            if ($result['code'] == 'succ') {
                $status = $result['body']['data'][0]['status'];
            } else {
                $this->redirect('/order/history/');
            }
        } else {
            $this->redirect('/order/history/');
        }
        $data['pid'] = $pid;
        $data['status_labels'] = array('succ' => '成功', 'fail' => '失败', 'cancel' => '已取消', 'error' => '出错啦', 'invalid' => '参数不正确', 'progress' => '处理中', 'timeout' => '已超时', 'ready' => '就绪');
        $data['status'] = $status;

        $this->render('completed', $data);
    }

    public function actionState() {
        $ids = Yii::app()->request->getParam('order');
        if (strlen($ids) < 15) {
            echo 0;
            Yii::app()->end();
        }
        $ids = explode(',', $ids);
        if (strlen($ids[0]) != 15) {
            echo 0;
            Yii::app()->end();
        }
        $result = Order::api()->detail(array(
            'id' => $ids[0],
            'distributor_id' => Yii::app()->user->org_id
            ), 0);
        if ($result['code'] == 'succ' && $result['body']['status'] == 'paid') {
            echo $result['body']['payment_id'];
        } else {
            echo 0;
        }
    }

}
