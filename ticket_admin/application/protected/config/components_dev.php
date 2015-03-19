<?php

return array(
    'components'=>array(
    		'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
            'user'=>array(
                // enable cookie-based authentication
                'allowAutoLogin'=>true,
            ),
            // uncomment the following to enable URLs in path-format
            /**/
            'urlManager'=>array(
                'urlFormat'=>'path',
                'rules'=>array(
                    '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                    '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                    '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                ),
                'showScriptName'=>false
            ),
            /**/
            'platformManager'=>array(
                'class'=>'common.components.UPlatformManager',
            ),
//
//            'db'=>array(
//                'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
//            ),
            // uncomment the following to use a MySQL database
            'db'=>array(
                'connectionString' => 'mysql:host=localhost;dbname=livesupport',
                'emulatePrepare' => true,
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
            ),
            'serverData'=>array(
                'class'=>'common.extensions.CDbConnectionExt',
                'connectionString' => 'mysql:host=192.168.1.91;dbname=data_server',
                'emulatePrepare' => true,
                'username' => 'dbaroot',
                'password' => 'M2zK7qL5nQtT3yuN',
                'charset' => 'utf8',
                'slaveConfig'=>array(
                    's1'=>array(
                        'connectionString'=>'mysql:host=192.168.1.91;dbname=data_server',
                        'username' => 'dbaroot',
                        'password' => 'M2zK7qL5nQtT3yuN',
                    ),
                    's2'=>array(
                        'connectionString'=>'mysql:host=192.168.1.91;dbname=data_server',
                        'username' => 'dbaroot',
                        'password' => 'M2zK7qL5nQtT3yuN',
                    )
                )
            ),
            'OAUserApi'=>array(
                    'class'=>'common.components.OAUserApi',
                    'apiUrl'=>'http://api.office.uuzuonline.com:80',
                    'apiIp'=>'192.168.1.176',
            ),
            'OPApi'=>array(
                'class'=>'common.components.OPApi',
                'apiUrl'=>'http://op.uuzuonline.com:80/api',
                'cacheExpire'=>3600*24,
                'apiIp'=>'192.168.1.53',
            ),
            'cache'=>array(
                'class'=>'CMemCache',
                'keyPrefix'=>'l',
                'servers'=>array(
                    's1'=>array(
                        'host'=>'192.168.1.53',
                        'port'=>12001,
                    )
                ),
            ),
            'errorHandler'=>array(
                // use 'site/error' action to display errors
                'errorAction'=>'site/error',
            ),
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class'=>'CFileLogRoute',
                        'levels'=>'error, warning',
                    ),
                    // uncomment the following to show log messages on web pages
                    /**
                    array(
                        'class'=>'CWebLogRoute',
                    ),
                     /**/
                ),
            ),
            /**
            'clientScript'=>array(
                    'scriptMap'=>array(
                        'jquery.js'=>'/js/jquery-1.7.2.min.js',
                    )

            )
             **/
        )
);