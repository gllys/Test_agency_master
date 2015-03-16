<?php

// uncomment the following to define a path alias
Yii::setPathOfAlias('common', realpath(dirname(__FILE__) . '/../../../common'));
// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'My Console Application',
    // application components
    'import' => array(
        'application.models.*',
        'application.models.cs.*',
        'application.components.*',
        'application.helpers.*',
        'application.extensions.*',
        'common.components.UActiveRecord',
        'common.extensions.CDbConnectionExt',
        'common.helpers.*'
    ),
    'components' => array(
        #键值缓存一般缓存本地
        'cache' => array(
            'class' => 'common.caching.URedisCache',
            'host' => '192.168.1.248',
            'port' => '6379',
        ),
        'redis' => array(
            'class' => 'common.components.URedis',
            'host' => '192.168.1.248',
            'port' => '6379',
        ),
        /**/
        // uncomment the following to use a MySQL database
        'db' => array(
            'connectionString' => 'mysql:host=192.168.1.248;dbname=statsys',
            'emulatePrepare' => true,
            'enableProfiling' => true,
            'username' => 'dbaroot',
            'password' => 'dbaroot20090606',
            'charset' => 'utf8',
        //'tablePrefix'=>'kf_'
        ),
        'fx' => array(
            'class' => 'common.extensions.CDbConnectionExt',
            'connectionString' => 'mysql:host=192.168.1.248;dbname=fx',
            'emulatePrepare' => true,
            'username' => 'dbaroot',
            'password' => 'dbaroot20090606',
            'charset' => 'utf8',
        //'tablePrefix'=>'kf_'
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
