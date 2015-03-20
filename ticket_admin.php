<?php

define('PW_DB', serialize(array(
    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=ticket_admin',
            'username' => 'root',
            'password' => '',
        )
    )
)));
define('PW_CACHE', serialize(array(
    'components' => array(
        'cache' => array(
            'host' => '127.0.0.1',
            'port' => '6379',
        )
    )
)));
define('PW_REDIS', serialize(array(
    'components' => array(
        'redis' => array(
            'host' => '127.0.0.1',
            'port' => '6379',
        )
    )
)));
define('EXT', serialize(array(
)));

//地址参数
define('PARAMS', serialize(array('params' => array(
    	'agency-url'=>array('url' => 'http://ticket_agency.demo.org.cn', 'sign' => 'huilian123'),
    	'supply-url'=>array('url' => 'http://ticket_supply.demo.org.cn', 'sign' => 'huilian123'),
    	'ticket-url'=>array('url' => 'http://ticket.demo.org.cn', 'sign' => 'huilian123'),
        'ticket-api-info' => array('url' => 'http://ticket-api-info.demo.org.cn/v1/', 'sign' => 'huilian123'),
        'ticket-api-organization' => array('url' => 'http://ticket-api-organization.demo.org.cn/v1/', 'sign' => 'huilian123'),
        'ticket-api-scenic' => array('url' => 'http://ticket-api-scenic.demo.org.cn/v1/', 'sign' => 'huilian123'),
		'ticket-api-order' => array('url' => 'http://ticket-api-order.demo.org.cn/v1/', 'sign' => 'huilian123'),
))));
