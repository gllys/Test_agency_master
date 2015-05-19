<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/24/14
 * Time: 3:45 PM
 */

class Order extends ApiModel
{
    public static $status = array(
        'pay_status' => array('unpaid' => 0, 'paying' => 1, 'paid' => 2),
        'bill_status' => array('unbill' => 0, 'billed' => 1),
        'refund_status' => array('unrefuned' => 0, 'refunding' => 1, 'refunded' => 2),
        'use_status' => array('unused' => 0, 'used' => 1),
        'audit_status' => array('auditing', 'audited', 'reject'),
        'cancel_status' => array('uncancel' => 0, 'canceled' => 1)
    );


    public static $label  = array(
        'pay_status' => '支付状态',
        'bill_status' => '结款状态',
        'refund_status' => '退款状态',
        'use_status' => '使用状态',
        'audit_status' => '审核状态',
        'cancel_status' => '取消状态',
        'unpaid' => '未支付',
        'paying' => '使用中',
        'paid' => '已支付',
        'unbill' => '未结款',
        'billed' => '已结款',
        'unrefuned' => '未退款',
        'refunding' => '退款中',
        'refunded' => '已退款',
        'unused' => '未使用',
        'used' => '已使用',
        'auditing' => '审核中',
        'audited' => '已审核',
        'reject' => '已驳回',
        'uncancel' => '未取消',
        'canceled' => '已取消'
    );
    
     //支付状态
    public static $payStatus = array(
        '0'=>'未支付',
        //'1'=>'支付中',
        '2'=>'已支付',
    );
    
    //结算状态
    public static $billStatus = array(
        '0'=>'未结算',
        '1'=>'已结算',
    );
    
    //退款状态
    public static $refundStatus = array(
        '0'=>'未退款',
        '1'=>'退款中',
        '2'=>'已退款',
    );
    
    //使用状态
    public static $useStatus = array(
        '0'=>'未使用',
        '1'=>'已使用',
    );
   
    //审核状态
    public static $auditStatus = array(
        '0'=>'审核中',
        '1'=>'已审核',
        '2'=>'已驳回',
    );
    
    //取消状态
    public static $cancelStatus = array(
        '0'=>'未取消',
        '1'=>'已取消',
    );

    
    //支付状态样式
    public static $payStatusStyle = array(
        '0'=>'danger',
        '1'=>'success',
        '2'=>'success',
    );
    
    //结算状态样式
    public static $billStatusStyle = array(
        '0'=>'danger',
        '1'=>'success',
    );
    
    //退款状态样式
    public static $refundStatusStyle = array(
        '0'=>'danger',
        '1'=>'success',
        '2'=>'success',
    );
    
    //使用状态样式
    public static $useStatusStyle = array(
        '0'=>'danger',
        '1'=>'success',
    );
   
    //审核状态样式
    public static $auditStatusStyle = array(
        '0'=>'success',
        '1'=>'success',
        '2'=>'danger',
    );
    
    //取消状态样式
    public static $cancelStatusStyle = array(
        '0'=>'danger',
        '1'=>'success',
    );
    
    //订单状态
    public static $orderStatus = array(
        'unaudited'=>'待审核',
        'reject'=>'已驳回',
        'unused'=>'未使用',
        'used'=>'已使用',
    );
    
    protected $param_key = 'ticket-api-order'; #请求api地址，对应config main里面的 key
} 
