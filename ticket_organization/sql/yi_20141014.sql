-- 机构表
CREATE TABLE IF NOT EXISTS `organizations`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('landscape','agency','supply') NOT NULL,
  `agency_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否旅行社,0否1是',
  `name` varchar(100) NOT NULL COMMENT '公司名称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `contact` varchar(50) NOT NULL DEFAULT '' COMMENT '联系人',
  `fax` varchar(20) NOT NULL DEFAULT '' COMMENT '公司传真',
  `abbreviation` varchar(20) NOT NULL DEFAULT '' COMMENT '简称',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '联系邮箱',
  `telephone` varchar(20) NOT NULL DEFAULT '' COMMENT '固定电话',
  `province_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所在省',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所在市',
  `district_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所在区',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '详细地址',
  `description` text NOT NULL COMMENT '简介',
  `business_license` varchar(255) NOT NULL DEFAULT '' COMMENT '营业执照',
  `tax_license` varchar(255) NOT NULL DEFAULT '' COMMENT '税务登记证',
  `certificate_license` varchar(255) NOT NULL DEFAULT '' COMMENT '经营许可证',
  `logo` varchar(255) NOT NULL DEFAULT '' COMMENT '机构logo',
  `verify_status` enum('apply','checked','reject') NOT NULL DEFAULT 'apply' COMMENT '审核状态',
  `verify_by` int(11) NOT NULL DEFAULT '0' COMMENT '审核人id',
  `verify_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核时间',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '启用状态',
  `agency_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '直营分销商id，0否',
  `supply_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '直属供应商id，0否',
  `landscape_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '直供景区id，0否',
  `is_distribute_person` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开通全平台散客票,0否1是',
  `is_distribute_group` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开通全平台团体票，0否1是',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录更新时间',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '机构';

-- 供应商绑定分销商关联表
CREATE TABLE IF NOT EXISTS `supply_agency`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supply_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '供应商id',
  `agency_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分销商id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '供应分销关联';