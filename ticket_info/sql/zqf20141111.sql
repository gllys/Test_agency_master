/*
SQLyog Ultimate v11.24 (32 bit)
MySQL - 5.5.40-log : Database - ticket_info
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `ticket_discount_rule` */

DROP TABLE IF EXISTS `ticket_discount_rule`;

CREATE TABLE `ticket_discount_rule` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '内部ID',
  `supplier_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '供应室ID',
  `namelist_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '白名单记录ID',
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT '规则名称',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '规则说明',
  `start_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始日期',
  `end_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束日期',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠减免',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建者UID',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `namelist_id` (`namelist_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `ticket_org_namelist` */

DROP TABLE IF EXISTS `ticket_org_namelist`;

CREATE TABLE `ticket_org_namelist` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '内部ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '名单类型:0白名单1黑名单',
  `supplier_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `agency_ids` text COLLATE utf8_unicode_ci NOT NULL COMMENT '分销商ID，多个逗号分隔',
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT '规则名称',
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '规则说明',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加者UID',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录添加时间',
  `updated_at` int(10) unsigned DEFAULT '0' COMMENT '记录更新时间',
  PRIMARY KEY (`id`),
  KEY `namelist_type` (`type`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='票的分销商限制名单';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
