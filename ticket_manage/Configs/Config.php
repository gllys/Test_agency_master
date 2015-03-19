<?php

/**
 * 项目主配置文件
 *
 * 2012-12-12 1.0 lizi 创建
 *
 * @author  zhouli
 * @version 1.0
 */
//未设置默认为线上
if(!isset($_SERVER['FX_REMOTE'])) {
	$_SERVER['FX_REMOTE'] = 2;
}

// 项目版本
define('PI_APP_VER', '0.1');

// 项目名称
define('PI_APP_NAME', 'fx-backend');

// 网站域名设置
define('PI_DOMAIN', 'demo.org.cn');
define('PI_APP_DOMAIN', PI_APP_NAME . '.' . PI_DOMAIN);

// 框架根路径
define('PI_CORE_ROOT', PI_APP_ROOT . '/Libs/');
define('PI_VIEW_URL', 'http://' . PI_APP_DOMAIN . '/Views/');

// 是否载入项目函数库 Libs/Function.php
define('PI_APP_LOAD_FUN', TRUE);

// 是否载入项目HOOK Configs/Hook.php
define('PI_APP_LOAD_HOOK', FALSE);

// ini_set设置,详细请看 php.ini 
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PI_CORE_ROOT);               // 框架根目录
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PI_CORE_ROOT . 'Tools/');      // 框架工具目录
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PI_APP_ROOT . 'Libs/');        // 项目类库目录
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PI_APP_ROOT . 'Controllers/'); // 项目控制器根目录
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PI_APP_ROOT . 'Commons/');     // 项目公共方法目录
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PI_APP_ROOT . 'Models/');     // 模型层目录
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PI_APP_ROOT . 'Api/');     // API层
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PI_APP_ROOT . 'Libs/Payments/'); // 支付方式
//ini_set('session.cookie_domain', PI_DOMAIN); // 避免二级域名无法得到SESSION的问题
// 设置时区,上海--以免出现时间不正确的情况
date_default_timezone_set("Asia/Shanghai");

// 数据库设置 
define('PI_DBS', serialize(array(
    //本地
    //'fx' => array('host' => '121.199.48.104', 'user' => 'root', 'password' => 'af329821', 'database' => 'fx', 'port' => '3306'),
    'fx' => array('host' => '192.168.1.14', 'user' => 'dbaroot', 'password' => 'dbaroot20090606', 'database' => 'ticket_manage', 'port' => '3306'),
    'ticket' => array('host' => '192.168.1.14', 'user' => 'dbaroot', 'password' => 'dbaroot20090606', 'database' => 'ticket', 'port' => '3306'),
    //'fx' => array('host' => 'localhost', 'user' => 'root', 'password' => '111111', 'database' => 'fx', 'port' => '3306'),
    //redmine
    // 'redmine' => array('host' => 'rdsna2yuazjavf2.mysql.rds.aliyuncs.com', 'user' => 'redmine123', 'password' => 'redmine123', 'database' => 'redmine', 'port' => '3306'),
    'redmine' => array('host' => '192.168.1.14', 'user' => 'redmine123', 'password' => 'redmine123', 'database' => 'redmine', 'port' => '3306'),
//	//rds
//	'fx' => array('host' => 'rdsna2yuazjavf2.mysql.rds.aliyuncs.com', 'user' => 'fx', 'password' => 'c3f9558d', 'database' => 'fx', 'port' => '3306'),
)));

// redis 相关设置 [cache/nosql/queue]
define('PI_REDIS', serialize(array(
 	'cache' => array('host' => '192.168.1.14', 'port' => 6379, 'db' => 10),
// 	'nosql' => array('host' => '127.0.0.1', 'port' => 6379, 'db' => 1),
// 	'queue' => array('host' => '127.0.0.1', 'port' => 6379, 'db' => 2),
)));
// mongodb 相关设置
// define('PI_MONGO', serialize(array(
// 	'master' => array('host' => '127.0.0.1', 'port' => 11611, 'db' => 'db'),
// 	'slave'  => array('host' => '127.0.0.1', 'port' => 11611, 'db' => 'db'),
// )));
// memcache 相关设置
// define('PI_MEMCACHE', serialize(array(
// 	'master' => array('host' => '127.0.0.1', 'port' => 11211),
// 	'user' => array('host' => '127.0.0.1', 'port' => 13210)
// )));
// sphinx 相关设置
// define('PI_SPHINX', serialize(array('host' => '127.0.0.1', 'port' => 3312)));
//平台ID,用于生成订单和票
define('PI_PLATFORM_ID', '1');
//数据中心 API 地址
define('PI_ITOURISM_API_BASE_URL', 'http://api.dev.test1.demo.org.cn/');
//数据中心 API basic auth 的用户名和密码
define('PI_ITOURISM_API_AUTH', serialize(array(
    'username' => 'itourism-distribution-api',
    'password' => 'itourism-distribution-api'
)));
// define('PI_HTTP_404_PAGE',      '/Views/tpl/404.html'); // 404页面
//定义日志的根目录
define('PI_LOG_BASE_PATH', PI_APP_ROOT . 'Logs/');


//定义上传文件url
// define('PI_UPLOADS_URL', 'http://test.upload.ihuilian.com/');
//define('PI_UPLOADS_URL', 'http://upload.demo.org.cn/');
//define('PI_UPLOADS_URL', SET_UPLOADS_URL);


//定义上传又拍云信息
define('SET_UPLOADS_URL', 'http://itourism-api.api.jinglvtong.com/attachments/');
define('SET_UPLOADS_USER','itourism-distribution-api');
define('SET_UPLOADS_PWD','itourism-distribution-api');
/* End */

//地址参数
define('PARAMS', serialize(array('params' => array(
        'agency-url' => array('url' => 'http://agency.test.demo.org.cn', 'sign' => 'huilian123'),
        'supply-url' => array('url' => 'http://supply.test.demo.org.cn', 'sign' => 'huilian123'),
        'ticket-url' => array('url' => 'http://ticket.test.demo.org.cn/', 'sign' => 'huilian123'),
        'ticket-api-info' => array('url' => 'http://ticket-api-info.demo.org.cn/v1/', 'sign' => 'huilian123'),
        'ticket-api-organization' => array('url' => 'http://ticket-api-organization.demo.org.cn/v1/', 'sign' => 'huilian123'),
        'ticket-api-scenic' => array('url' => 'http://ticket-api-scenic.demo.org.cn/v1/', 'sign' => 'huilian123'),
        'ticket-api-order' => array('url' => 'http://ticket-api-order.demo.org.cn/v1/', 'sign' => 'huilian123'),
))));
