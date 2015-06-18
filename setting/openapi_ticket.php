<?php
include('api_db_host.php');
// base
define('SET_DOMAIN', 'ticket-api-order.demo.org.cn');
define('SET_URL', 'http://ticket-api-order.demo.org.cn');

// db
define('SET_DB', serialize(array(
	'itourism' => array('host' => API_DB_HOST, 'user' => 'mysql100', 'password' => 'HuiLian100', 'database' => 'openapi_ticket', 'port' => '3306'),
	'log' => array('host' => API_DB_HOST, 'user' => 'mysql100', 'password' => 'HuiLian100', 'database' => 'openapi_log', 'port' => '3306'),
)));

// redis
define('SET_REDIS', serialize(array('default' => array('host' => '127.0.0.1', 'port' => 6379, 'db' => 3), 'nosql' => array('host' => '127.0.0.1', 'port' => 6379, 'db' => 4), 'queue' => array('host' => '127.0.0.1', 'port' => 6379, 'db' => 5),)));

// memcache 相关设置
define('SET_MEMCACHE', serialize(array(
  'default' => array('host' => '127.0.0.1', 'port' => 11211),
  //'default' => array('host' => '127.0.0.1', 'port' => 11211, 'username'=>'aaa', 'password'=>'bbb'),
  'user' => array('host' => '127.0.0.1', 'port' => 13210)
)));
define('SET_USE_MEMCACHED', true);


// xhprof
define('XHPROF_OPEN', 0);
define('XHPROF_ROOT', '/Users/zqf/www/xhprof/');

// api
define('SET_API_URL', serialize(array(
    //本地服务器
// 'ticket_info' =>'ticket-info.com',
//  'ticket_order' => 'ticket-order.com',
// 'ticket_scenic' => 'ticket-scenic.com',
// 'ticket_organization'=>'ticket-organization.com',
    //14服务器
//    'ticket_organization' => 'http://ticket-api-organization.demo.org.cn',
//	'ticket_info' => 'http://ticket-api-info.demo.org.cn',
//    'ticket_scenic' => 'http://ticket-api-scenic.demo.org.cn',
//	'ticket_order' => 'http://ticket-api-order.demo.org.cn',
    //100服务器
    'ticket_organization' => 'http://bt-ticket-api-organization.demo.org.cn',
    'ticket_info' => 'http://bt-ticket-api-info.demo.org.cn',
	'ticket_scenic' => 'http://bt-ticket-api-scenic.demo.org.cn',
	'ticket_order' => 'http://bt-ticket-api-order.demo.org.cn',
	)));


//qunar api
define('QUNAR_SETTING', serialize(array(
    'sendcode_url'=>'http://agent.beta.qunar.com/api/external/supplierServiceV2.qunar',
//    'sendcode_url'=>'http://huilian.tunnel.mobi/api/external/supplierServiceV2.qunar',
    'consume_url'=>'http://agent.beta.qunar.com/api/external/supplierServiceV2.qunar',
    'qunar_url'=>'http://agentat.piao.qunar.com/singleApiDebugData',
    'agency_id'=>'147',
    'user_id'=>'3383470907',
    'supplier_identity'=>'DEBUGSUPPLIER',//'',ZFZJY,MEIJINGTEST1
    'signedKey'=>'DEBUGSINGKEY',//'',WkZaSlk=,MEIJINGTESTSIGN
)));

define('MEITUAN_SETTING' , serialize(array(
    'partnerId' => '1032',
    'clientId' => 'lvyou_partner',
    'clientSecret' => '503849402c92d852bffdc7a75a27b9bb',
    'url' => 'http://lvyou.test.sankuai.info',
)));

define('AGENCY_DISPATCH', serialize(array(
    1 => 'Taobao',
    10 => 'Qunar',
    13 => 'Way',
    15 => 'Meituan',
)));

include('openapi_log_tables.php');