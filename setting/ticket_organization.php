<?php

// base
define('SET_DOMAIN', 'ticket-api-org.piaotai.com');
define('SET_URL', 'http://ticket-api-org.piaotai.com');

// xhprof
define('XHPROF_OPEN', 0);
define('XHPROF_ROOT', '/data/web/xhprof/');

// db
define('SET_DB', serialize(array(
	// 'itourism' => array('host' => '192.168.1.248', 'user' => 'dbaroot', 'password' => 'dbaroot20090606', 'database' => 'ticket_organization', 'port' => '3306'),
	// 'log' => array('host' => '192.168.1.248', 'user' => 'dbaroot', 'password' => 'dbaroot20090606', 'database' => 'ticket_log', 'port' => '3306'),
	'itourism' => array('host' => 'rdseeyivej2eaqa.mysql.rds.aliyuncs.com', 'user' => 'fx', 'password' => 'c3f9558d', 'database' => 'ticket_organization', 'port' => '3306'),
	'log' => array('host' => 'rdseeyivej2eaqa.mysql.rds.aliyuncs.com', 'user' => 'fx', 'password' => 'c3f9558d', 'database' => 'ticket_log', 'port' => '3306'),
)));

// redis
define('SET_REDIS', serialize(array('default' => array('host' => '10.160.51.166', 'port' => 6379, 'db' => 0), 'nosql' => array('host' => '10.160.51.166', 'port' => 6379, 'db' => 1), 'queue' => array('host' => '10.160.51.166', 'port' => 6379, 'db' => 2),)));

// memcache 相关设置
 define('SET_MEMCACHE', serialize(array('default' => array('host' => '10.160.51.166', 'port' => 11211), 'user' => array('host' => '10.160.51.166', 'port' => 13210))));
// define('SET_USE_MEMCACHED', true);
// define('SET_MEMCACHE', serialize(
//    array(
//        'default' => array(
//            'host' => 'a26f413742ff11e4.m.cnhzalicm10pub001.ocs.aliyuncs.com', 'port' => 11211,
//            'username'=>'a26f413742ff11e4', 'password'=>'hljj_2014S'
//        ),
//        'user' => array('host' => '127.0.0.1', 'port' => 13210, 'username'=>'', 'password'=>'')
//    )
// ));

// api
define('SET_API_URL', serialize(array(
	'ticket_organization' => 'http://ticket-api-org.piaotai.com',
	'ticket_info' => 'http://ticket-api-info.piaotai.com',
	'ticket_scenic' => 'http://ticket-api-scenic.piaotai.com',
	'ticket_order' => 'http://ticket-api-order.piaotai.com'
	)));

