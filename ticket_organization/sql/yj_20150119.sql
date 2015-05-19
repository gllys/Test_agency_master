CREATE TABLE `ad_pos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告位ID',
  `name` varchar(128) NOT NULL DEFAULT '0' COMMENT '广告位名称',
  `width` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宽度',
  `height` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '高度',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0关闭 1开启',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='广告位配置';

CREATE TABLE `ad` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告ID',
  `pos_id` varchar(128) NOT NULL DEFAULT '0' COMMENT '广告位IDs',
  `title` varchar(128) NOT NULL DEFAULT '0' COMMENT '主题',
  `bimg` varchar(128) NOT NULL DEFAULT '0' COMMENT '图片路径',
  `url` varchar(128) NOT NULL DEFAULT '0' COMMENT '链接地址',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `width` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宽度',
  `height` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '高度',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发布状态：0未发布 1已发布',
  `created_by` varchar(60) NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `pos_id` (`pos_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='广告';

INSERT INTO `ticket_organization`.`ad_pos` (`id`, `name`, `width`, `height`, `status`) VALUES (NULL, '登陆页', '0', '0', '1');
INSERT INTO `ticket_organization`.`ad_pos` (`id`, `name`, `width`, `height`, `status`) VALUES (NULL, '工作台', '0', '0', '1');