ALTER TABLE `ticket_info`.`ticket_template` ADD COLUMN `rule_id` INT(11) UNSIGNED NULL COMMENT '价格规则ID' AFTER `is_full`; 

CREATE TABLE `ticket_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则ID',
  `supplier_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `name` varchar(60) NOT NULL COMMENT '规则名称',
  `desc` varchar(500) DEFAULT NULL COMMENT '规则说明',
  `created_by` int(11) unsigned NOT NULL COMMENT '添加者uid',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '编辑时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='票的价格规则';




CREATE TABLE `ticket_rule_items` (
  `rule_id` int(11) unsigned NOT NULL COMMENT '规则iD',
  `date` date NOT NULL COMMENT '日期',
  `fat_price` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '散客价规则',
  `group_price` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '团客价规则',
  `reserve` int(11) unsigned DEFAULT NULL COMMENT '库存规则',
  UNIQUE KEY `rule_id_date` (`rule_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='票的价格规则详细设置'