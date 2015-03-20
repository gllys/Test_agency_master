<?php
define('PI_PHPRPC_USER', serialize(array('account' => 'demo', 'password' => 'fe01ce2a7fbac8fafaed7c982a04e229')));
define('PW_DB', serialize(array(
    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=rdseeyivej2eaqa.mysql.rds.aliyuncs.com;dbname=ticket',
            'username' => 'deploy_sys',
            'password' => 'CrUb0e87r0ER',
        )
    )
)));
define('PW_CACHE', serialize(array(
    'components' => array(
        'cache' => array(
            'host' => '10.160.51.166',
            'port' => '6379',
        )
    )
)));
define('PW_REDIS', serialize(array(
    'components' => array(
        'redis' => array(
            'host' => '10.160.51.166',
            'port' => '6379',
        )
    )
)));
define('EXT', serialize(array(
)));

//地址参数
define('PARAMS', serialize(array('params' => array(
        'supplyUrl' => 'http://supply.piaotai.com',
        'agency-url' => array('url' => 'http://www.piaotai.com', 'sign' => 'huilian123'),
        'supply-url' => array('url' => 'http://supply.piaotai.com', 'sign' => 'huilian123'),
        'ticket-url' => array('url' => 'http://ticket.piaotai.com', 'sign' => 'huilian123'),
        'ticket-api-info' => array('url' => 'http://ticket-api-info.piaotai.com/v1/', 'sign' => 'huilian123'),
        'ticket-api-organization' => array('url' => 'http://ticket-api-org.piaotai.com/v1/', 'sign' => 'huilian123'),
        'ticket-api-scenic' => array('url' => 'http://ticket-api-scenic.piaotai.com/v1/', 'sign' => 'huilian123'),
        'ticket-api-order' => array('url' => 'http://ticket-api-order.piaotai.com/v1/', 'sign' => 'huilian123'),
))));
