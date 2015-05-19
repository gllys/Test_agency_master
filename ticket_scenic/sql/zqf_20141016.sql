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
  `description` text,
  `biography` text COMMENT '景区介绍',
  `note` text COMMENT '购票须知',
  `transit` text COMMENT '交通指南',
  `status` enum('normal','unaudited','failed') NOT NULL DEFAULT 'unaudited' COMMENT '审核状态',
  `organization_id` int(10) unsigned NOT NULL COMMENT '机构id',
  `normal_before` tinyint(1) DEFAULT '0' COMMENT '曾经审核通过过',
  `impower_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '授权书',
  `location_hash` varchar(20) NOT NULL DEFAULT '' COMMENT '数据中心关联的景区hash',
  `location_name` varchar(30) NOT NULL DEFAULT '' COMMENT '数据中心关联的景区名称',
  `location_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '数据中心关联的景区时间',
  `audited_by` int(10) unsigned DEFAULT NULL COMMENT '审核人？有用？',
  `audited_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  `last_updated_source` tinyint(4) NOT NULL DEFAULT '1' COMMENT '记录最后更新地',
  `lat` double(10,6) DEFAULT '0.000000' COMMENT '经度',
  `lng` double(10,6) DEFAULT '0.000000' COMMENT '维度',
  `api_channel_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '对接渠道编号',
  `sys` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统类型 0不限制版 1限制版',
  PRIMARY KEY (`id`),
  KEY `ind_org` (`organization_id`),
  KEY `ind_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8 COMMENT='景区';

/*Table structure for table `poi` */

DROP TABLE IF EXISTS `poi`;

CREATE TABLE `poi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) NOT NULL COMMENT 'POI名称，指物理景区，跟票的景区无关系',
  `organization_id` int(10) unsigned NOT NULL COMMENT '机构id',
  `landscape_id` int(11) NOT NULL COMMENT '景点id',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  `last_updated_source` tinyint(4) NOT NULL DEFAULT '1' COMMENT '记录最后更新地',
  PRIMARY KEY (`id`),
  KEY `poi_organization_id_idx` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8 COMMENT='poi';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
