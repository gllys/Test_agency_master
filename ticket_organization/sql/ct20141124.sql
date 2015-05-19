REATE TABLE `subscribes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(20) NOT NULL COMMENT '票模板ID',
  `organization_id` int(20) NOT NULL COMMENT '机构ID',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '票名称',
  `fat_price` decimal(20,2) NOT NULL COMMENT '散客价',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `group_price` decimal(20,2) NOT NULL COMMENT '团客价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `favorites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(20) NOT NULL COMMENT '机构ID',
  `ticket_id` int(20) NOT NULL COMMENT '票模板ID',
  `add_time` int(20) NOT NULL COMMENT '添加时间',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '票名称',
  `type` int(1) NOT NULL COMMENT '0:团客，1:散客',
  PRIMARY KEY (`id`),
  UNIQUE KEY `organization_id` (`organization_id`,`type`,`ticket_id`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
