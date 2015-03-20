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
                        'connectionString' => 'mysql:host=rdseeyivej2eaqa.mysql.rds.aliyuncs.com;dbname=ticket',
                        'username' => 'deploy_sys',
                        'password' => 'CrUb0e87r0ER',
                )
        )
)));
define('FX_DB', serialize(array(
        'components' => array(
                'fx' => array(
                        'connectionString' => 'mysql:host=rdseeyivej2eaqa.mysql.rds.aliyuncs.com;dbname=fx',
                        'username' => 'deploy_sys',
                        'password' => 'CrUb0e87r0ER',
                ),
        )
)));
define('USER_DB', serialize(array(
#       'components' => array(
#               'user_info' => array(
#                       'connectionString' => 'mysql:host=42.121.0.205;dbname=user_info',
#                       'username' => 'xihazhijia',
#                       'password' => 'fsx123456',
#               ),
#       )
)));
