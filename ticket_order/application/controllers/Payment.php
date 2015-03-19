<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class PaymentController extends Base_Controller_Abstract
{
    public function indexAction() {

        $params['callback'] = 'Payment_Kuaiqian::test';
        $params['orderId'] = $this->getParam('orderId');
        $params['orderAmount'] = 0.01;
        $params['orderTime'] = time();
        $params['productName'] = 'productName';
        $params['productDesc'] = 'productDesc';
        $params['productNum'] = 1;
        $params['productId'] = 1;
        $payment = new Payment_Kuaiqian();
        $payment->pay($params);
        exit();
    }

    public function synccallAction() {
        Payment_Kuaiqian::synccall($this->getParams());
        //[dealTime] => 20141026152846 
        //[payAmount] => 1 
        //[signType] => 4 
        //[errCode] => 
        //[merchantAcctId] => 1002354435101 
        //[orderTime] => 20141026072812 
        //[dealId] => 1690470155 
        //[version] => v2.0 
        //[bankId] => CMB 
        //[fee] => 1 
        //[bankDealId] => 8681395024 
        //[payResult] => 10 
        //[ext1] => Payment_Kuaiqian::test 
        //[ext2] => 
        //[orderAmount] => 1 
        //[signMsg] => Fi1St4vVzLsVqCRZnm1IsglXKWpoH8yeYqiEw1/amfrC9Gd9ZYZEHM8emwlB9L1fA65DihaE7QQQcAj/sOoO2gSWTI7pZerre0ES9dPB5hSetr6kicsy5q2MfBsd4Uyn5jz026+9F0PNVbg++U0cri77sxS+W9KhElan12dSnko2+clJNeDQic1mEEmmp+cVtNS26KRWwIZ0vNL8loHLTPTgJpvYADPrql9BX9E6bogd7o7TDQX6DkmJxgRp/JR9LmdLugFkpvi7Y/CLShGOywVdgbLRoOhPrnyirl3+WCr8w4RRmphJg6t8eSFylSxx9Tpd00jujqwbIn4W2ULG/g== 
        //[payType] => 10 
        //[language] => 1 
        //[orderId] => 2 
    }

    public function asynccallAction() {
        Payment_Kuaiqian::asynccall($this->getParams());
    }

}
