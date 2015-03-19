/*
SQLyog Ultimate v11.24 (32 bit)
MySQL - 5.5.40-0ubuntu0.14.04.1 : Database - ticket_order
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
USE `ticket_order`;

/*Table structure for table `order_group201411` */

DROP TABLE IF EXISTS `order_group201411`;

CREATE TABLE `order_group201411` (
  `id` bigint(20) unsigned NOT NULL COMMENT '组合票订单号，4开头',
  `distributor_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商ID',
  `ticket_group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '组合票ID',
  `ticket_template_ids` varchar(500) NOT NULL COMMENT '票种ID，多个用逗号分隔',
  `order_ids` varchar(500) NOT NULL COMMENT '订单ID，多个用逗号分隔',
  `nums` mediumint(8) unsigned NOT NULL DEFAULT '1' COMMENT '门票张数',
  `use_day` date NOT NULL COMMENT '游玩时间',
  `owner_name` varchar(20) DEFAULT NULL COMMENT '取票人',
  `owner_mobile` varchar(20) DEFAULT NULL COMMENT '取票人手机',
  `owner_card` varchar(20) DEFAULT '' COMMENT '取票人身份证',
  `remark` text COMMENT '备注',
  `op_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作者UID',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录添加时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录编辑时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `distributor_id` (`distributor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组合票订单记录';

/*Table structure for table `ticket_group` */

DROP TABLE IF EXISTS `ticket_code`;

CREATE TABLE `ticket_code` (
  `id` int(11) unsigned NOT NULL COMMENT '票模版票码',
  `distributor_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商ID',
  `ticket_template_ids` varchar(500) NOT NULL COMMENT '票种ID，多个逗号分隔',
  `ticket_template_infos` text NOT NULL COMMENT '票种信息，序列化数组',
  `op_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作者UID',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录添加时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录更改时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `distributor_id` (`distributor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销商票码关联表';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
