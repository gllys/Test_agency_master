DROP TABLE IF EXISTS `transaction_flow`;
CREATE TABLE `transaction_flow` (
  `id` bigint(20) NOT NULL COMMENT '交易流水号',
  `mode` enum('cash','offline','credit','pos','alipay','advance','union','kuaiqian','taobao') NOT NULL DEFAULT 'credit' COMMENT '交易方式:cash,offline,credit,pos,alipay,advance,union,kuaiqian,taobao',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '交易类型:1pay,2refund,3recharge,4enchashment',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '支付金额',
  `supplier_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `agency_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商ID',
  `ip` varchar(20) DEFAULT NULL COMMENT 'ip地址',
  `op_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作人员uid',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `agency_id` (`agency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='交易流水记录表';

DROP TABLE IF EXISTS `agency_tk_stat`;
CREATE TABLE `agency_tk_stat` (
  `distributor_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商ID',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '统计时间',
  `ticket_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '票张数',
  `money_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售金额',
  KEY `uniq_index` (`distributor_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert  into `process_config`(`name`,`path`,`num`,`state`) values ('AgencyTkStat','AgencyTkStat.php',1,1);
INSERT INTO `ticket_order`.`language_config` (`id`, `zh`) VALUES ('INFO_GEN_BILL_0', '立即结算');
INSERT INTO `ticket_order`.`language_config` (`id`, `zh`) VALUES ('ERROR_GEN_BILL_0', '成功生成了{n}张结款单');
INSERT INTO `ticket_order`.`language_config` (`id`, `zh`) VALUES ('ERROR_GEN_BILL_1', '已经结算');
INSERT INTO `ticket_order`.`language_config` (`id`, `zh`) VALUES ('ERROR_CANCELCODE_6', '已无票可退');
INSERT INTO `ticket_order`.`language_config` (`id`, `zh`) VALUES ('ERROR_CANCELCODE_7', '退票数不能大于未使用票数');
ALTER TABLE `ticket_order`.`orders201410` ADD COLUMN `ota_type` VARCHAR(20) NOT NULL DEFAULT 'system' COMMENT '票来源' AFTER `price_type`;
ALTER TABLE `ticket_order`.`orders201411` ADD COLUMN `ota_type` VARCHAR(20) NOT NULL DEFAULT 'system' COMMENT '票来源' AFTER `price_type`;
ALTER TABLE `ticket_order`.`orders201412` ADD COLUMN `ota_type` VARCHAR(20) NOT NULL DEFAULT 'system' COMMENT '票来源' AFTER `price_type`;

ALTER TABLE `ticket_record` ADD `cancel_status` TINYINT NOT NULL DEFAULT '0' COMMENT '是否撤销 0未撤销 1已撤销' AFTER `http_status`;
alter table cart add type int(2)  default 0 not null;
alter table orders201412  add `ota_type` varchar(20) not null default 'system' COMMENT '票来源';
alter table orders201411  add `ota_type` varchar(20) not null default 'system' COMMENT '票来源';
insert into version (version) values ('20141127');
