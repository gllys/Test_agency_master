<?php

// uncomment the following to define a path alias
Yii::setPathOfAlias('common', realpath(dirname(__FILE__) . '/../../../common'));
include(dirname(__FILE__) . '/../../../../setting/jg_storage.php');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$config = array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => '智慧旅游管理平台',
	// preloading 'log' component
	'preload' => array('log'),
	'defaultController' => 'index',
	// autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.components.*',
		'application.helpers.*',
		'application.extensions.*',
		'common.components.UActiveRecord',
		'common.extensions.CDbConnectionExt',
		'common.helpers.*'
	),
	'modules' => array(
		// uncomment the following to enable the Gii tool
		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => '1',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			//'ipFilters'=>array('127.0.0.1','::1'),
			'ipFilters' => FALSE,
		),
		'igii' => array(
			'class' => 'common.igii.IgiiModule',
		),
		'urbac' => array(
			'userClass' => 'Member',
			'userId' => 'username',
			'username' => 'display_name',
			'resourceClass' => 'Site',
			'resourceId' => 'id',
			'resourceName' => 'name',
			'resourceKey' => 'site_id',
			'resourceLabel' => '网站'
		),
	),
	// application components
	'components' => array(
		'user' => array(
			'class' => 'common.web.auth.UWebUser',
			'cookieKey' => 'huilian_UAUTH',
			'key' => '!udfsdf*&^**(o',
			'loginUrl' => '/site/login/',
			'cookieDomain' => 'stat.demo.org.cn'
		),
		'upyun' => array(
			'class' => 'common.components.UYouPai',
			'formApiSecret' => 'yBw5ogXiJp5ATampzkSBtXaqZUE=',
			'bucket' => 'shehuan-image',
			'uploadDir' => 'admin_permission',
			'returnUrl' => '/site/upyunAgent/',
			'host' => 'http://image.shehuan.net',
		),
		// uncomment the following to enable URLs in path-format
		/**/
		'urlManager' => array(
			'urlFormat' => 'path',
			'rules' => array(
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			),
			'showScriptName' => false,
			'caseSensitive' => false,
		),
		/*
		 * 就激活码后台需求
		 */
		'authManager' => array(
			'class' => 'CDbAuthManager',
			'connectionID' => 'db',
			'itemTable' => 'kf_auth_item',
			'assignmentTable' => 'kf_auth_assignment',
			'itemChildTable' => 'kf_auth_item_child'
		),
		#键值缓存一般缓存本地
		'cache' => array(
			'class' => 'common.caching.URedisCache',
			'host' => '127.0.0.1',
			'port' => '6379',
		),
		'redis' => array(
			'class' => 'common.components.URedis',
			'host' => '127.0.0.1',
			'port' => '6379',
		),
		/**/
		// uncomment the following to use a MySQL database
		'db' => array(
			'connectionString' => 'mysql:host=127.0.0.1;dbname=statsys',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			//'tablePrefix'=>'kf_'
		),
		'fx' => array(
			'class' => 'common.extensions.CDbConnectionExt',
			'connectionString' => 'mysql:host=127.0.0.1;dbname=fx',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			//'tablePrefix'=>'kf_'
		),
		'user_info' => array(
			'class' => 'common.extensions.CDbConnectionExt',
			'connectionString' => 'mysql:host=42.121.0.205;dbname=user_info',
			'emulatePrepare' => true,
			'username' => 'xihazhijia',
			'password' => 'fsx123456',
			'charset' => 'utf8',
			//'tablePrefix'=>'kf_'
		),
		'clientScript' => array(
			'scriptMap' => array(
				'jquery.js' => '/js/jquery-1.7.2.min.js'
			)
		),
		'errorHandler' => array(
			// use 'index/error' action to display errors
			'errorAction' => 'site/error',
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/* array(
				  'class' => 'CWebLogRoute',
				  'levels' => 'trace', //级别为trace
				  'categories' => 'system.db.*' //只显示关于数据库信息,包括数据库连接,数据库执行语句
				  ), */
			),
		),
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => array(
		// this is used in contact page
		'imgUrl' => 'http://image.shehuan.net/',
		'headUrl' => 'http://head.shehuan.net/',
		'domainUrl' => 'http://manage.www.shehuan.net/',
	),
);
defined('JG_CACHE') || defined('JG_CACHE', array());
defined('JG_REDIS') || defined('JG_REDIS', array());
defined('JG_DB') || defined('JG_DB', array());
defined('FX_DB') || defined('FX_DB', array());
defined('USER_DB') || defined('USER_DB', array());
return array_replace_recursive($config,
	unserialize(JG_CACHE),
	unserialize(JG_REDIS),
	unserialize(JG_DB),
	unserialize(FX_DB),
	unserialize(USER_DB)
);
