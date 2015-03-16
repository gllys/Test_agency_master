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
/*Table structure for table `ticket_tpl_day_price` */

DROP TABLE IF EXISTS `ticket_tpl_day_price`;

CREATE TABLE `ticket_tpl_day_price` (
  `ticket_template_id` int(11) unsigned NOT NULL COMMENT '票ID',
  `date` date NOT NULL COMMENT '日期',
  `price` double(10,2) NOT NULL COMMENT '价格',
  `setting_by` int(10) NOT NULL DEFAULT '0' COMMENT '操作者UID',
  `setting_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '设置记录时间',
  UNIQUE KEY `ticket_date` (`ticket_template_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='票的日价格';

/*Table structure for table `ticket_tpl_day_reserve` */

DROP TABLE IF EXISTS `ticket_tpl_day_reserve`;

CREATE TABLE `ticket_tpl_day_reserve` (
  `ticket_template_id` int(11) unsigned NOT NULL COMMENT '票ID',
  `date` date NOT NULL COMMENT '日期',
  `reserve` int(10) unsigned NOT NULL COMMENT '库存',
  `setting_by` int(10) unsigned NOT NULL COMMENT '操作者UID',
  `setting_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '设置记录时间',
  UNIQUE KEY `ticket_date` (`ticket_template_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='票的日库存';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
