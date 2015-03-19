USE `ticket_order`;

CREATE TABLE `order_group201412` (
  `id` bigint(20) unsigned NOT NULL COMMENT '组合票订单号，4开头',
  `distributor_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商ID',
  `ticket_code_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '票码',
  `ticket_template_ids` varchar(500) NOT NULL COMMENT '票种ID，多个用逗号分隔',
  `order_ids` varchar(500) NOT NULL COMMENT '订单ID，多个用逗号分隔',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态，0未支付1已支付',
  `ota_account` int(11) DEFAULT '0' COMMENT 'OTA账号',
  `ota_name` varchar(60) DEFAULT NULL COMMENT 'OTA名称',
  `op_id` int(11) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录添加时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录编辑时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `distributor_id` (`distributor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组合票订单记录';

CREATE TABLE `ticket_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '票模版票码',
  `distributor_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商ID',
  `ticket_template_ids` varchar(500) NOT NULL COMMENT '票种ID，多个逗号分隔',
  `op_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作者UID',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录添加时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录更改时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `distributor_id` (`distributor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='分销商组合票关联表';