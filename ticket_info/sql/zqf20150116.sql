use `ticket_info`;

iNSERT INTO `language_config` (id,zh) values ('ERROR_AddGenerate_25','缺少销售价');
iNSERT INTO `language_config` (id,zh) values ('ERROR_AddGenerate_26','请添加门票信息');
iNSERT INTO `language_config` (id,zh) values ('ERROR_AddGenerate_27','缺少门票类型');
iNSERT INTO `language_config` (id,zh) values ('ERROR_AddGenerate_28','有产品在使用该门票，无法进行此操作');
iNSERT INTO `language_config` (id,zh) values ('ERROR_AddGenerate_29','上架失败，该产品存在未上架门票');
INSERT INTO `language_config` (id,zh) VALUES ('ERR_PRODUCT_1', '产品添加失败');

update `language_config` set `zh`='门票添加失败' where `id`='ERROR_AddGenerate_24';

DROP TABLE IF EXISTS `ticket_policy`;

CREATE TABLE `ticket_policy` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '内部ID',
  `supplier_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT '规则名称',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '规则说明',
  `other_fat_price` decimal(10,2) DEFAULT '0.00' COMMENT '不合作分销商散客价调整量',
  `other_group_price` decimal(10,2) DEFAULT '0.00' COMMENT '不合作分销商团客价调整量',
  `other_blackname_flag` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '不合作分销商黑名单开关：0关闭 1开启',
  `other_credit_flag` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '不合作分销商信用支付开关：0关闭 1开启',
  `other_advance_flag` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '不合作分销商储值支付开关：0关闭 1开启',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作者uid',
  `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作者账号',
  `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作者姓名',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='分销策略';


DROP TABLE IF EXISTS `ticket_policy_items`;

CREATE TABLE `ticket_policy_items` (
  `policy_id` bigint(20) unsigned NOT NULL COMMENT '策略ID',
  `distributor_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商ID',
  `fat_price` decimal(10,2) DEFAULT '0.00' COMMENT '散客价调整量',
  `group_price` decimal(10,2) DEFAULT '0.00' COMMENT '团客价调整量',
  `blackname_flag` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '黑名单开关：0关闭 1开启',
  `credit_flag` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '信用支付开关：0关闭 1开启',
  `advance_flag` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '储值支付开关：0关闭 1开启',
  UNIQUE KEY `policy_unique` (`policy_id`,`distributor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='分销策略明细';

ALTER TABLE `ticket_template` CHANGE `payment` `payment` VARCHAR(30) NULL DEFAULT '1,4' COMMENT '支付方式：1在线支付，2信用支付，3储值支付，4平台储值支付'
ALTER TABLE `ticket_template` COMMENT '产品表';
ALTER TABLE `ticket_template` ADD `policy_id` bigint(20) unsigned DEFAULT '0' COMMENT '分销策略ID' after `rule_id`;
ALTER TABLE `ticket_template` ADD `base_org_num` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '基础票创建机构数量';
ALTER TABLE `ticket_template` ADD `sale_start_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '销售起始日' after `date_available`,
  ADD `sale_end_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '销售结束日' after `sale_start_time`;
ALTER TABLE `ticket_template` DROP `ticket_template_base_ids`;
ALTER TABLE `ticket_template_base` change `scenic_id` `scenic_id` int(11) unsigned NOT NULL COMMENT '景区ID';
ALTER TABLE `ticket_template_base` ADD `gid` BIGINT  UNSIGNED  NOT NULL  DEFAULT '0'  COMMENT 'GroupID';


DROP TABLE IF EXISTS `ticket_template_items`;

CREATE TABLE `ticket_template_items` (
  `product_id` int(11) unsigned NOT NULL COMMENT '产品ID',
  `base_id` int(11) unsigned NOT NULL COMMENT '基础票ID',
  `base_org_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门票所属供应商id',
  `scenic_id` int unsigned NOT NULL COMMENT '景区ID',
  `sceinc_name` varchar(100) NOT NULL COMMENT '景区名称',
  `view_point` text NOT NULL COMMENT '景点ID',
  `base_name` varchar(100) NOT NULL COMMENT '门票名称',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '票类型：1成人票，2儿童票，3老人票，4团队票',
  `sale_price` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '销售价',
  `num` int unsigned NOT NULL DEFAULT '1' COMMENT '张数',
  `province_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所在省',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所在市',
  `district_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所在地',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  UNIQUE KEY `uniq_index` (`product_id`,`base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品基础票关联表';
