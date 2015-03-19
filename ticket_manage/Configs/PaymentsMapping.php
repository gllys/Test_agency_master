<?php 
/**
 * 2013-12-09
 * @author liuhe(liuhe009@gmail.com)
 * @version 1.0
 *
 **/

$paymentsMapping = array(
	'alipay',
	'credit',
	'offline',
	'kuaiqian',
);

define('PI_PAYMENTS_MAPPING', serialize($paymentsMapping));
unset($paymentsMapping);