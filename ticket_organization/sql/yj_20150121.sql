ALTER TABLE `ad` ADD `detail` text NOT NULL COMMENT '内容';
ALTER TABLE `union_money` ADD `activity_money` decimal(10,2) DEFAULT '0.00' COMMENT '抵用券金额';
ALTER TABLE `union_money_recharge` ADD `activity_money` decimal(10,2) DEFAULT '0.00' COMMENT '抵用券金额';
ALTER TABLE `union_money_recharge` ADD `activity_charge_log_id` int(12) NOT NULL DEFAULT '0' COMMENT '充值优惠日志id';
ALTER TABLE `activity_charge_log` ADD `paid_at` int(10) unsigned DEFAULT '0' COMMENT '打款时间';
ALTER TABLE `activity_charge_log` ADD `pay_type` tinyint(1) DEFAULT '0' COMMENT '支付方式：1块钱2支付宝';
ALTER TABLE `activity_charge_log` ADD `money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '充值金额';