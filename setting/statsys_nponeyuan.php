<?php
define('JG_CACHE', serialize(array(
        'components' => array(
                'cache' => array(
                        'host' => '10.160.51.166',
                        'port' => '6379',
                )
        )
)));
define('JG_REDIS', serialize(array(
        'components' => array(
                'redis' => array(
                        'host' => '10.160.51.166',
                        'port' => '6379',
                )
        )
)));
define('JG_DB', serialize(array(
        'components' => array(
                'db' => array(
                        'connectionString' => 'mysql:host=rdseeyivej2eaqa.mysql.rds.aliyuncs.com;dbname=statsys_nponeyuan',
                        'username' => 'fx',
                        'password' => 'c3f9558d',
                )
        )
)));
define('FX_DB', serialize(array(
        'components' => array(
                'fx' => array(
                        'connectionString' => 'mysql:host=rdseeyivej2eaqa.mysql.rds.aliyuncs.com;dbname=fx',
                        'username' => 'fx',
                        'password' => 'c3f9558d',
                ),
        )
)));
define('USER_DB', serialize(array(
        'components' => array(
                'user_info' => array(
                        'connectionString' => 'mysql:host=rdseeyivej2eaqa.mysql.rds.aliyuncs.com;dbname=user_info',
                        'username' => 'fx',
                        'password' => 'c3f9558d',
                ),
        )
)));
define('EXT', serialize(array(
    'components' => array(
        'ticket_order' => array(
            'class' => 'common.extensions.CDbConnectionExt',
            'connectionString' => 'mysql:host=rdseeyivej2eaqa.mysql.rds.aliyuncs.com;dbname=ticket_order',
            'username' => 'fx',
'charset' => 'utf8',
            'password' => 'c3f9558d',
        ),
    ),
)));
