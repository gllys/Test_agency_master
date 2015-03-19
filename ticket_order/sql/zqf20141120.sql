/*
SQLyog Ultimate v11.24 (32 bit)
MySQL - 5.5.35-0ubuntu0.13.10.2 : Database - ticket_order
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
USE `ticket_order`;

/*Table structure for table `agency_tk_stat` */

DROP TABLE IF EXISTS `agency_tk_stat`;

CREATE TABLE `agency_tk_stat` (
  `distributor_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商ID',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '统计时间',
  `ticket_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '票张数',
  `money_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售金额',
  KEY `uniq_index` (`distributor_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
