INSERT INTO `language_config` (id,zh) VALUES ('ERROR_RESERVE_2','该日不存在日库存设置');
INSERT INTO `language_config` (id,zh) VALUES ('ERR_TKT_POLICY_1','分销策略名称不能为空');
INSERT INTO `language_config` (id,zh) VALUES ('ERR_TKT_POLICY_2','请设置分销策略明细');
INSERT INTO `language_config` (id,zh) VALUES ('ERR_TKT_POLICY_3','缺少分销策略ID参数');
INSERT INTO `language_config` (id,zh) VALUES ('ERR_TKT_POLICY_4','该分销策略记录不存在');
INSERT INTO `language_config` (id,zh) VALUES ('ERR_TKT_POLICY_5','删除失败！有产品票在使用此分销策略，请取消后再删除');
INSERT INTO `language_config` (id,zh) VALUES ('INFO_TKT_POLICY_1','添加了分销策略记录');
INSERT INTO `language_config` (id,zh) VALUES ('INFO_TKT_POLICY_2','更新了分销策略记录');
INSERT INTO `language_config` (id,zh) VALUES ('INFO_TKT_POLICY_3','删除了分销策略记录');

alter table `ticket_rule` add `ticket_template_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '产品票ID' after `id`, change `name` `name` varchar(60) NULL COMMENT '规则名称';

alter table `ticket_rule_items` add `ticket_template_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '产品票ID' after `rule_id`;

alter table `ticket_tpl_day_price` change `price` `fat_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '散客价', add `group_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '团客价' after `fat_price`;

alter table `ticket_tpl_day_price` change `setting_at` `setting_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '设置时间';
alter table `ticket_tpl_day_reserve` change `setting_at` `setting_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '设置时间';

CREATE TABLE `ticket_policy` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '内部ID',
  `supplier_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT '规则名称',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '规则说明',
  `allow_other_fat` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '允许未合作分销商散客价',
  `other_fat_price` decimal(10,2) DEFAULT '0.00' COMMENT '其他散客价',
  `allow_other_group` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '允许未合作分销商团客价',
  `other_group_price` decimal(10,2) DEFAULT '0.00' COMMENT '其他团客价',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作者uid',
  `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作者账号',
  `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作者姓名',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='分销策略';

CREATE TABLE `ticket_policy_items` (
  `policy_id` bigint(20) unsigned NOT NULL COMMENT '策略ID',
  `distributor_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商ID',
  `is_fat` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '有散客价',
  `is_group` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '有团客价',
  `fat_price` decimal(10,2) DEFAULT '0.00' COMMENT '其他散客价',
  `group_price` decimal(10,2) DEFAULT '0.00' COMMENT '其他团客价',
  UNIQUE KEY `policy_unique` (`policy_id`,`distributor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='分销策略明细';

alter table `ticket_template` add `policy_id` bigint(20) unsigned NULL DEFAULT '0' COMMENT '分销策略ID' after `discount_id`;

