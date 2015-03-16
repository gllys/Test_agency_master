<?php

// base
define('SET_DOMAIN', 'open.api.piaotai.com');
define('SET_URL', 'http://open.api.piaotai.com');

// xhprof
define('XHPROF_OPEN', 0);
define('XHPROF_ROOT', '/data/web/xhprof/');

// db
define('SET_DB', serialize(array(
	'itourism' => array('host' => '192.168.1.100', 'user' => 'root', 'password' => 'dbaroot20090606', 'database' => 'openapi_ticket', 'port' => '3306'),
	'log' => array('host' => '192.168.1.100', 'user' => 'root', 'password' => 'dbaroot20090606', 'database' => 'openapi_ticket', 'port' => '3306'),
	// 'itourism' => array('host' => 'localhost', 'user' => 'root', 'password' => '111111', 'database' => 'ticket_order', 'port' => '3306'),
	// 'log' => array('host' => 'localhost', 'user' => 'root', 'password' => '111111', 'database' => 'ticket_log', 'port' => '3306'),
	// 'itourism' => array('host' => 'rdsna2yuazjavf2.mysql.rds.aliyuncs.com', 'user' => 'fx', 'password' => 'c3f9558d', 'database' => 'ticket_order', 'port' => '3306'),
	// 'log' => array('host' => 'rdsna2yuazjavf2.mysql.rds.aliyuncs.com', 'user' => 'fx', 'password' => 'c3f9558d', 'database' => 'ticket_log', 'port' => '3306'),
)));

// redis
define('SET_REDIS', serialize(array('default' => array('host' => '127.0.0.1', 'port' => 6379, 'db' => 0), 'nosql' => array('host' => '127.0.0.1', 'port' => 6379, 'db' => 1), 'queue' => array('host' => '127.0.0.1', 'port' => 6379, 'db' => 2),)));

// memcache 相关设置
define('SET_MEMCACHE', serialize(array('default' => array('host' => '127.0.0.1', 'port' => 11211), 'user' => array('host' => '127.0.0.1', 'port' => 13210))));
define('SET_USE_MEMCACHED', true);

// api
define('SET_API_URL', serialize(array(
	// 'ticket_organization' => 'http://bt-ticket-api-org.demo.org.cn',
	// 'ticket_info' => 'http://bt-ticket-api-info.demo.org.cn',
	// 'ticket_scenic' => 'http://bt-ticket-api-scenic.demo.org.cn',
	// 'ticket_order' => 'http://bt-ticket-api-order.demo.org.cn'
	'ticket_organization' => 'http://ticket-org.com',
	'ticket_info' => 'http://ticket-info.com',
	'ticket_scenic' => 'http://ticket-scenic.com',
	'ticket_order' => 'http://ticket-order.com',
	)));

define('IS_MASTER', true);
