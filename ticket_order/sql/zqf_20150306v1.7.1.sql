use `ticket_order`;
ALTER TABLE `orders` ADD `pay_rate` DECIMAL(6,5)  NULL  DEFAULT '0'  COMMENT '费率'  AFTER `pay_at`;

CREATE TABLE `pay_rate` (
  `payment` varchar(30) NOT NULL DEFAULT '' COMMENT '支付方式：kuaiqian,alipay',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '第三方支付：块钱，支付宝',
  `rate` decimal(6,5) DEFAULT '0.00000' COMMENT '费率',
  `setted_at` int(10) DEFAULT '0' COMMENT '设置时间',
  `user_id` int(11) DEFAULT '0' COMMENT '操作者uid',
  `user_account` varchar(60) DEFAULT NULL COMMENT '操作者account',
  `user_name` varchar(60) DEFAULT NULL COMMENT '操作者name',
  PRIMARY KEY (`payment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付费率设置表';


CREATE TABLE `sms_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '内部编号ID',
  `sent_at` int(10) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '手机号码',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1成功，2失败',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型：0默认，1订单支付成功，2注册验证码，3重置密码，4提现验证码',
  `content` text COMMENT '短信内容',
  `order_id` varchar(20) DEFAULT NULL COMMENT '订单ID',
  `fail_reason` text COMMENT '短信发送失败原因',
  PRIMARY KEY (`id`),
  KEY `sent_at` (`sent_at`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短信日志';

