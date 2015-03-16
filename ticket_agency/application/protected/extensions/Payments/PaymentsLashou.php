<?php
/**
 * 功能：实现快钱接口
 * 1）人民币网关支付API（V3.0.3）
 * 2）人民币网关订单退款API（V2.0.3）：
 * 3）人民币网关退款查询接口API（V2.0.8）：
 * @author shilei
 * Created at: 2014-05-15
 */
final class PaymentsLashou
{

    public $appKey       = 'lashou';
    public $appName      = '拉手';
    public $displayName  = '拉手';
    public $payType      = 'online';
    public $version      =  "v2.0";

    //支付成功
    const PAY_SUCC = '10';
    //退款成功
    const PAY_BACK_SUCC = '1';
}
/* End */