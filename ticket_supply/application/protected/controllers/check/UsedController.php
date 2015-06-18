<?php

class UsedController extends Controller {

    public function actionIndex() {
        //如果是景区用户，则直接跳转
        if (Yii::app()->user->lan_id && empty($_GET['landscape_id'])) {
            Yii::app()->runController('/check/used/index/landscape_id/' . Yii::app()->user->lan_id);
        }

        //得到可使用订单列表
        $lists = array();
        $error = '该订单没有可以使用的门票';
        if (!empty($_GET['id'])) {
            //Verification::api()->debug = true;
            $param = $_GET;
            $rs = Verification::api()->lists($param, 0);
            if (ApiModel::isSucc($rs)) {
                $lists = ApiModel::getData($rs);
            }else{
                $lists = ApiModel::getData($rs);
                $error = $rs['message'];
            }
        }
        $this->render('index', compact('lists', 'error'));
    }

    //使用票
    public function actionUsed() {
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            $param['uid'] = Yii::app()->user->id;
            $param['user_name'] = Yii::app()->user->display_name;
            if (Yii::app()->user->org_id) {
                $param['or_id'] = Yii::app()->user->org_id;
            }
            $param['view_point'] = 0;

            //去零
            $param['datas'] = array();
            foreach ($_POST['datas'] as $k => $v) {
                if (intval($v) < 0) {
                    $this->_end(1, "订单号{$k}验证的票不能为负");
                }

                if ($v) {
                    $param['datas'][$k] = $v*$param['ticketSum'][$k];
                }
            }
            if (!$param['datas']) {
                $this->_end(1, '请选择要验证的票');
            }
            $param['data'] = json_encode($param['datas']);
            unset($param['datas']);
            //Verification::api()->debug = true;
            $rs = Verification::api()->update($param);
            if (ApiModel::isSucc($rs)) {//
                $printInfo = array();
                foreach ($_POST['datas'] as $orderId => $num) {
                    if (!$num) { #如果是零张则不打印小票
                        continue;
                    }
                    //todo optimize
                    $_rs = Order::api()->detail(array('id' => $orderId, 'show_order_items' => 1));
                    $detail = ApiModel::getData($_rs);
                    $orderItem = current($detail['order_items']);
                    $printInfo[$orderId]['order_id'] = $orderId;
                    $printInfo[$orderId]['ticket_name'] = $orderItem['name'];
                    $printInfo[$orderId]['num'] = $num;
                    $printInfo[$orderId]['date'] = date('Y/m/d H:i:s');
                    $printInfo[$orderId]['op_name'] = Yii::app()->user->display_name;
                    $printInfo[$orderId]['owner_name'] = $detail['owner_name'];
                    $printInfo[$orderId]['owner_mobile'] = $detail['owner_mobile'];
                    $printInfo[$orderId]['remark'] = $detail['remark'];
                }
                //LogCollect::add('验证小票成功');
                $this->_end(0, $rs['message'], $printInfo);
            } else {
                //LogCollect::add('验证小票失败');
                $this->_end(1, $rs['message']);
            }
        }
    }

}
