<?php 
/**
 * 接口相关的列表
 * 2014-01-02
 * @author liuhe(liuhe009@gmail.com)
 * @version 1.0
 *
 **/

$apiConf = array(
	'alipay' => array(
		'class_name' => 'PaymentsAlipay',
		'methods'    => array(
			'sync_callback'         => 'syncCallback',
			'async_callback'        => 'asyncCallback',
			'async_refund_callback' => 'asyncRefundCallback',
		),
	),
    'kuaiqian' => array(
        'class_name' => 'PaymentsKuaiqian',
        'methods'    => array(
            'sync_callback'         => 'syncCallback',
            'async_callback'        => 'asyncCallback',
            'async_refund_callback' => 'asyncRefundCallback',
        ),
    ),
);

// 定义常量
define('PI_API_MAPPING', serialize($apiConf));
unset($apiConf);

//数据中心的接口
$itourismApiConf = array(
	//获取景点信息
	'getLandscapes' => array(
		'targetUrlPrefix' => PI_ITOURISM_API_BASE_URL.'advanced/landscapes',
		'method'          => 'GET',
	),
	//获取票信息
	'getTickets' => array(
		'targetUrlPrefix' => PI_ITOURISM_API_BASE_URL.'advanced/tickets',
		'method'          => 'GET',
	),
	//增加票
	'addTickets' => array(
		'targetUrlPrefix' => PI_ITOURISM_API_BASE_URL.'advanced/tickets',
		'method'          => 'POST',
	),
	//支付票
	'payTickets' => array(
		'targetUrlPrefix' => PI_ITOURISM_API_BASE_URL.'advanced/tickets/<arg1>/paid',
		'method'          => 'PUT',
	),
	//使用票
	'usedTickets' => array(
		'targetUrlPrefix' => PI_ITOURISM_API_BASE_URL.'advanced/tickets/<arg1>/location/<arg2>/used',
		'method'          => 'PUT',
	),
	//申请退款票
	'refundingTickets' => array(
		'targetUrlPrefix' => PI_ITOURISM_API_BASE_URL.'advanced/tickets/<arg1>/refund',
		'method'          => 'PUT',
	),
	//完成退款的票
	'refundedTickets' => array(
		'targetUrlPrefix' => PI_ITOURISM_API_BASE_URL.'advanced/tickets/<arg1>/refunded',
		'method'          => 'PUT',
	),
	//作废票
	'uselessTickets' => array(
		'targetUrlPrefix' => PI_ITOURISM_API_BASE_URL.'advanced/tickets/<arg1>/delete',
		'method'          => 'PUT',
	),
	//更新票
	'updateTickets' => array(
		'targetUrlPrefix' => PI_ITOURISM_API_BASE_URL.'advanced/tickets/<arg1>',
		'method'          => 'PUT',
	),
);

// 定义常量 数据中心的接口
define('PI_ITOURISM_API_MAPPING', serialize($itourismApiConf));
unset($itourismApiConf);