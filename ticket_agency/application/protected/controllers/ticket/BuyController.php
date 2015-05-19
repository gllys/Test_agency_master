<?php

class BuyController extends Controller {

    public function actionIndex() {
        $param['ticket_id'] = $_GET['id'];
        $param['price_type'] = $_GET['price_type'];
        $param['distributor_id'] = Yii::app()->user->org_id;
        $rs = TicketTemplate::api()->ticketinfo($param, false);
        $info = ApiModel::getData($rs);
        $this->renderPartial('index', compact('info'));
    }

    public function actionPlaceOrder() {
        if (Yii::app()->request->isPostRequest) {
            $data = array();
            //$data['use_day'] = date("Y-m-d", strtotime($_POST['use_day']));
            $data['distributor_id'] = Yii::app()->user->org_id;
            $data['user_id'] = Yii::app()->user->uid;
            $data['user_name'] = Yii::app()->user->account;
            $data['cartTicketList'] = array();
            for ($i = 0; $i < count($_POST['owner_name']); $i++) {
                $data['cartTicketList'][] = array(
                    'ticket_template_id' => $_POST['ticket_template_id'],
                    'use_day' => $_POST['use_day'],
                    'price_type' => $_POST['price_type'],
                    'nums' => $_POST['nums'][$i],
                    'owner_name' => $_POST['owner_name'][$i],
                    'owner_mobile' => $_POST['owner_mobile'][$i],
                    'owner_card' => $_POST['owner_card'][$i],
                    'remark' => $_POST['note'],
                );

            }
            $rs = Order::api()->addbatch($data);
            if (ApiModel::isSucc($rs)) {
                $_data = ApiModel::getData($rs);
                $order_ids = PublicFunHelper::arrayKey($_data, 'id');
                $this->_end(0, $rs['message'], $order_ids);
            } else {
                $this->_end(1, $rs['message']);
            }
        }
    }

    public function actionAddCart() {
        if (Yii::app()->request->isPostRequest) {
            $param['ticket_id'] = $_POST['ticket_template_id'];
            $param['ticket_name'] = $_POST['ticket_name'];
            $param['date'] = strtotime($_POST['use_day']);
            $param['price_type'] = $_POST['price_type'];
            $param['price'] = $_POST['price'];
            $param['user_id'] = Yii::app()->user->uid;
            $param['remark'] = $_POST['note'];
            $param['type'] = $_POST['type'];
            $arr = array();
            foreach ($_POST['owner_name'] as $key => $value) {
                $arr[] = array(
                    'name' => $_POST['owner_name'][$key],
                    'phone' => $_POST['owner_mobile'][$key],
                    'num' => $_POST['nums'][$key],
                    'card' => $_POST['owner_card'][$key],
                );
            }
            $param['list'] = json_encode($arr);
            //print_r($param);exit;
            $result = Cart::api()->add($param);
            if (Cart::isSucc($result)) {
                $this->_end(0, $result['message']);
            } else {
                $this->_end(1, $result['message']);
            }
        } else {
            throw new CHttpException(404, "找不到请求的页面!");
        }
    }

    public function actionDayPrice() {
        $param['ticket_template_id'] = $_GET['id'];
        $param['date'] = $_GET['date'];
        $rs = Ticketdprice::api()->lists($param);
        if (ApiModel::isSucc($rs)) {
            $this->_end(0, $rs['message'], ApiModel::getData($rs));
        } else {
            $this->_end(1, $rs['message']);
        }
    }

    public function actionTicketInfo() {
    	$param = $_POST;
    	$param['distributor_id'] = Yii::app()->user->org_id;
    	$result = TicketTemplate::api()->ticketinfo($param);
    	if($result['code'] = 'succ'){
    		$this->_end(0,$result['message'],ApiModel::getData($result));
    	}else{
    		$this->_end(1,$result['message']);
    	}
    }

}
