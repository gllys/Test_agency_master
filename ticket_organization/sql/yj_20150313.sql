CREATE TABLE `taobao_organization` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `account` varchar(255) NOT NULL DEFAULT '0' COMMENT '淘宝账号',
  `organization_id` int(20) NOT NULL DEFAULT '0' COMMENT '机构ID',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '淘宝账号审核状态：0未审核 1已审核',
  `created_by` varchar(60) NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_org` (`account`,`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='淘宝机构关联';