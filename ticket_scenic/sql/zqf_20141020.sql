/*
SQLyog Ultimate v11.24 (32 bit)
MySQL - 5.5.40-log : Database - ticket_scenic
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `equipment` */

DROP TABLE IF EXISTS `equipment`;

CREATE TABLE `equipment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(2) DEFAULT NULL COMMENT '设备类型 1=>闸机， 0=>手持',
  `code` varchar(40) NOT NULL DEFAULT '' COMMENT '设备编号',
  `name` varchar(100) DEFAULT '' COMMENT '名称（预留）',
  `organization_id` int(11) NOT NULL COMMENT '机构id',
  `landscape_id` int(11) DEFAULT '0' COMMENT '景区id',
  `poi_id` int(11) DEFAULT '0' COMMENT '子景点id',
  `telephone` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '手机号码',
  `create_by` int(11) DEFAULT '0' COMMENT '添加人员（admin_id）',
  `update_by` int(11) DEFAULT '0' COMMENT '修改人员',
  `update_from` enum('admin','users') DEFAULT 'admin' COMMENT '修改人员来源（后台、前台）',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '记录更新时间',
  `deleted_at` int(11) DEFAULT '0' COMMENT '记录删除时间',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否启用：1-启用，0-禁用',
  `last_updated_source` tinyint(4) DEFAULT '1' COMMENT '记录最后更新地',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `equipment_organization_id_idx` (`organization_id`),
  KEY `equipment_landscape_id_idx` (`landscape_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

/*Table structure for table `landscape_images` */

DROP TABLE IF EXISTS `landscape_images`;

CREATE TABLE `landscape_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片ID',
  `landscape_id` int(10) unsigned NOT NULL COMMENT '景区ID',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '图片url',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作者UID',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录更新时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '上传时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `landscape_levels` */

DROP TABLE IF EXISTS `landscape_levels`;

CREATE TABLE `landscape_levels` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `rank` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  `last_updated_source` tinyint(4) NOT NULL DEFAULT '1' COMMENT '记录最后更新地',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `landscape_organization` */

DROP TABLE IF EXISTS `landscape_organization`;

CREATE TABLE `landscape_organization` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '内部ID',
  `landscape_id` int(11) unsigned NOT NULL COMMENT '景区ID',
  `organization_id` int(11) unsigned NOT NULL COMMENT '供应商ID',
  `release_right` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '发票权：1有0无',
  `check_right` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '核销权：1自我2景区所有票',
  `check_log_right` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '核销记录查看权：1自我2景区所有票',
  `poi_manage_right` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '景点管理权：1有0无',
  `created_by` int(10) NOT NULL DEFAULT '0' COMMENT '记录添加者',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录添加时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `landorg` (`landscape_id`,`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `landscapes` */

DROP TABLE IF EXISTS `landscapes`;

CREATE TABLE `landscapes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '一级票务',
  `landscape_level_id` int(10) unsigned NOT NULL COMMENT '级别',
  `province_id` int(10) unsigned NOT NULL COMMENT '地区Level 1 ID',
  `city_id` int(10) unsigned NOT NULL COMMENT '地区Level 2 ID',
  `district_id` int(10) unsigned NOT NULL COMMENT '地区Level 3 ID',
  `address` varchar(100) DEFAULT NULL COMMENT '联系地址',
  `thumbnail_id` int(10) unsigned DEFAULT NULL COMMENT '封面图',
  `phone` varchar(100) DEFAULT NULL COMMENT '联系电话',
  `hours` text COMMENT '开放时间',
  `exaddress` varchar(100) DEFAULT NULL COMMENT '取票地址',
  `biography` text COMMENT '景区介绍',
  `note` text COMMENT '购票须知',
  `transit` text COMMENT '交通指南',
  `status` enum('normal','unaudited','failed') NOT NULL DEFAULT 'unaudited' COMMENT '审核状态',
  `organization_id` int(10) unsigned NOT NULL COMMENT '机构id',
  `normal_before` tinyint(1) DEFAULT '0' COMMENT '曾经审核通过过',
  `impower_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '授权书',
  `audited_by` int(10) unsigned DEFAULT NULL COMMENT '审核人？有用？',
  `audited_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  `lat` double(10,6) DEFAULT '0.000000' COMMENT '经度',
  `lng` double(10,6) DEFAULT '0.000000' COMMENT '维度',
  `api_channel_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '对接渠道编号',
  `on_shelf` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否上架：1是，0否',
  PRIMARY KEY (`id`),
  KEY `ind_org` (`organization_id`),
  KEY `ind_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COMMENT='景区';

/*Table structure for table `poi` */

DROP TABLE IF EXISTS `poi`;

CREATE TABLE `poi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '景点id',
  `name` varchar(100) NOT NULL COMMENT 'POI名称，指物理景区，跟票的景区无关系',
  `organization_id` int(10) unsigned NOT NULL COMMENT '机构id',
  `landscape_id` int(11) unsigned NOT NULL COMMENT '景区id',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作者UID',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态1上架0下架',
  PRIMARY KEY (`id`),
  KEY `poi_organization_id_idx` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=utf8 COMMENT='poi';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
