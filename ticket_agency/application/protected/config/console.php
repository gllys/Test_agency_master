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
	// application components
	'components' => array(
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
		'errorHandler' => array(
			// use 'site/error' action to display errors
			'errorAction' => 'site/error',
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'trace,error, warning',
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
