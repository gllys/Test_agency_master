<?php
include('api_db_host.php');
// base
define('SET_DOMAIN', 'ticket-info.com');

// db
define('SET_DB', serialize(array(
	'itourism' => array('host' => API_DB_HOST, 'user' => 'dbaroot', 'password' => 'dbaroot20090606', 'database' => 'ticket_info', 'port' => '3306'),
	'log' => array('host' => API_DB_HOST, 'user' => 'dbaroot', 'password' => 'dbaroot20090606', 'database' => 'ticket_log', 'port' => '3306'),
)));

// redis
define('SET_REDIS', serialize(array('cache' => array('host' => '127.0.0.1', 'port' => 6379, 'db' => 0), 'nosql' => array('host' => '127.0.0.1', 'port' => 6379, 'db' => 1), 'queue' => array('host' => '127.0.0.1', 'port' => 6379, 'db' => 2),)));

// memcache 相关设置
define('SET_MEMCACHE', serialize(
    array(
        'default' => array('host' => '127.0.0.1', 'port' => 11211, 'username'=>'', 'password'=>''),
        'master' => array(
            'host' => 'a26f413742ff11e4.m.cnhzalicm10pub001.ocs.aliyuncs.com', 'port' => 11211,
            'username'=>'a26f413742ff11e4', 'password'=>'hljj_2014S'
        ),
        'user' => array('host' => '127.0.0.1', 'port' => 13210, 'username'=>'', 'password'=>'')
    )
));
define('SET_USE_MEMCACHED', true);


// xhprof
define('XHPROF_OPEN', 0);
define('XHPROF_ROOT', '/Users/zqf/www/xhprof/');

// api
define('SET_API_URL', serialize(array(
	'ticket_organization' => 'http://ticket-organization.com',
	'ticket_info' => 'http://ticket-info.com',
	'ticket_scenic' => 'http://ticket-scenic.com',
	'ticket_order' => 'http://ticket-order.com'
	)));
