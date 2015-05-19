CREATE TABLE `activity_charge` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(128) NOT NULL DEFAULT '0' COMMENT '主题',
  `num` decimal(10,2) DEFAULT '0.00' COMMENT '充值额度',
  `coupon` decimal(10,2) DEFAULT '0.00' COMMENT '赠送礼券',
  `start_time` int unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发布状态：0未发布 1已发布',
  `created_by` varchar(60) NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='充值优惠';

CREATE TABLE `activity_charge_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `organization_id` int(12) NOT NULL DEFAULT '0' COMMENT '机构ID',
  `organization_name` varchar(100) NOT NULL DEFAULT '0' COMMENT '机构名称',
  `activity_id` bigint(20) unsigned NOT NULL COMMENT '优惠活动ID',
  `activity_title` varchar(128) NOT NULL DEFAULT '0' COMMENT '主题',
  `num` decimal(10,2) DEFAULT '0.00' COMMENT '充值额度',
  `coupon` decimal(10,2) DEFAULT '0.00' COMMENT '赠送礼券',
  `coupon_total` decimal(10,2) DEFAULT '0.00' COMMENT '礼券总额',
  `created_by` int unsigned NOT NULL DEFAULT '0' COMMENT '创建人ID',
  `created_name` varchar(60) NOT NULL DEFAULT '' COMMENT '创建人',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='充值优惠日志';