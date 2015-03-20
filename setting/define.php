<?php

// base
define('SET_DOMAIN', 'piaotai.com');

// db
define('SET_DB', serialize(array(
	//'itourism' => array('host' => '192.168.1.248', 'user' => 'dbaroot', 'password' => 'dbaroot20090606', 'database' => 'fx', 'port' => '3306'),
	 //'itourism' => array('host' => 'localhost', 'user' => 'dbaroot', 'password' => 'dbaroot20090606', 'database' => 'fx', 'port' => '3306'),
	 'itourism' => array('host' => 'rdseeyivej2eaqa.mysql.rds.aliyuncs.com', 'user' => 'fx', 'password' => 'c3f9558d', 'database' => 'fx', 'port' => '3306'),
)));

// redis
define('SET_REDIS', serialize(array('default' => array('host' => '10.160.51.166', 'port' => 6379, 'db' => 0), 'nosql' => array('host' => '10.160.51.166', 'port' => 6379, 'db' => 1), 'queue' => array('host' => '10.160.51.166', 'port' => 6379, 'db' => 2),)));

// mongodb 相关设置
define('SET_MONGODB', serialize(array('master' => array('host' => '127.0.0.1', 'port' => 11611, 'db' => 'db'), 'slave' => array('host' => '127.0.0.1', 'port' => 11611, 'db' => 'db'),)));
//define('SET_USE_MEMCACHED',0);  
// memcache 相关设置
// define('SET_MEMCACHE', serialize(array('master' => array('host' => '127.0.0.1', 'port' => 11211), 'user' => array('host' => '127.0.0.1', 'port' => 13210))));
 define('SET_MEMCACHE', serialize(array('default' => array(array('host' => '10.160.51.166', 'port' => 11211)), 'user' => array('host' => '10.160.51.166', 'port' => 13210))));
//define('SET_MEMCACHE', serialize(
//    array(
//        'master' => array(
//            'host' => 'a26f413742ff11e4.m.cnhzalicm10pub001.ocs.aliyuncs.com', 'port' => 11211,
//            'username'=>'a26f413742ff11e4', 'password'=>'hljj_2014S'
//        ),
//        'user' => array('host' => '127.0.0.1', 'port' => 13210, 'username'=>'', 'password'=>'')
//    )
//));
// itourism api
define('SET_ITOURISM_API_URL', 'http://itourism-api.api.jinglvtong.com/');

// itourism auth
define('SET_ITOURISM_API_AUTH', serialize(array('username' => 'itourism-distribution-api', 'password' => 'itourism-distribution-api')));

// upload url
// define('PI_UPLOADS_URL', 'http://test.upload.ihuilian.com/');
// define('SET_UPLOADS_URL', 'http://u.ihuilian.com/');
define('SET_UPLOADS_URL', 'http://itourism-api.api.jinglvtong.com/attachments/');
define('SET_UPLOADS_USER','itourism-distribution-api');
define('SET_UPLOADS_PWD','itourism-distribution-api');
define('SET_PANO_SDK_URL', 'http://pano-sdk-js.api.jinglvtong.com/');

// scenic
define('SET_SCENIC_VER', '0.1');
define('SET_SCENIC_NAME', 'scenic');
define('SET_SCENIC_DOMAIN_PREFIX', 's');

// agency
define('SET_AGENCY_VER', '0.1');
define('SET_AGENCY_NAME', 'agency');
define('SET_AGENCY_DOMAIN_PREFIX', 'a');
define('SET_AGENCY_SMS_CONTROL', 1);

// api
define('SET_API_VER', '0.1');
define('SET_API_NAME', 'api');
define('SET_API_DOMAIN_PREFIX', 'fx-api');

// phprpc开启的服务
define('SET_API_PHPRPC_ACTIONS', serialize(array(array('class' => 'RpcController', 'methods' => array('qrcode')), array('class' => 'RpcController', 'methods' => array('test')))));

// phprpc的用户名和密码
define('SET_API_PHPRPC_USER', serialize(array('account' => 'demo', 'password' => 'fe01ce2a7fbac8fafaed7c982a04e229')));

// API auth 的用户名和密码
define('SET_API_AUTH_USER', serialize(array('username' => 'hljj', 'password' => '8038da89e49ac5eabb489cfc6cea9fc1')));

// sync api
define('SET_SYNCAPI_VER', '0.1');
define('SET_SYNCAPI_NAME', 'syncapi');
define('SET_SYNCAPI_DOMAIN_PREFIX', 'fx-syn-capi');
define('SET_SYNCAPI_DOMAIN', 'fx-sync-api.'.SET_DOMAIN);
#define('SET_SYNCAPI_URL', 'http://'.SET_SYNCAPI_DOMAIN);
define('SET_SYNCAPI_URL', 'http://112.124.35.37:9001');
define('SET_API_URL', serialize(array(
        'ticket_organization' => 'http://ticket-api-org.piaotai.com',
        'ticket_info' => 'http://ticket-api-info.piaotai.com',
        'ticket_scenic' => 'http://ticket-api-scenic.piaotai.com',
        'ticket_order' => 'http://ticket-api-order.piaotai.com',
        'itourism-api'=>'http://itourism-api.api.jinglvtong.com'
        )));
define('SET_API_SECRET', "huilian123");
