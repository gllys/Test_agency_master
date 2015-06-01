<?php
include('api_db_host.php');
// base
define('SET_DOMAIN', 'ticket-api-order.demo.org.cn');
define('SET_URL', 'http://ticket-api-order.demo.org.cn');


// db
define('SET_DB', serialize(array(
	'itourism' => array('host' => API_DB_HOST, 'user' => 'dbaroot', 'password' => 'dbaroot20090606', 'database' => 'openapi_ticket', 'port' => '3306'),
	'log' => array('host' => API_DB_HOST, 'user' => 'dbaroot', 'password' => 'dbaroot20090606', 'database' => 'openapi_log', 'port' => '3306'),
)));

// redis
//define('SET_REDIS', serialize(array('cache' => array('host' => '192.168.10.193', 'port' => 6379, 'db' => 0), 'nosql' => array('host' => '192.168.10.193', 'port' => 6379, 'db' => 1), 'queue' => array('host' => '192.168.10.193', 'port' => 6379, 'db' => 2),)));
define('SET_REDIS', serialize(array('cache' => array('host' => '192.168.1.15', 'port' => 6379, 'db' => 8), 'default' => array('host' => '192.168.1.15', 'port' => 6379, 'db' => 0), 'nosql' => array('host' => '192.168.10.193', 'port' => 6379, 'db' => 1), 'queue' => array('host' => '192.168.1.15', 'port' => 6379, 'db' => 2),)));


// memcache 相关设置
define('SET_MEMCACHE', serialize(array(
  'default' => array('host' => '192.168.1.15', 'port' => 11211),
  //'default' => array('host' => '192.168.1.15', 'port' => 11211, 'username'=>'aaa', 'password'=>'bbb'),
  'user' => array('host' => '192.168.1.15', 'port' => 13210)
)));

define('SET_USE_MEMCACHED', true);


// xhprof
define('XHPROF_OPEN', 0);
define('XHPROF_ROOT', '/Users/zqf/www/xhprof/');
define('SET_CREATE_OTA', true);

// api
define('SET_API_URL', serialize(array(
	'combo_info' => 'http://zyx.demo.org.cn/api',
)));


// 日志支持的模块
include 'openapi_log_tables.php';