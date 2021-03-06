/*
SQLyog Ultimate v11.24 (32 bit)
MySQL - 5.5.35-0ubuntu0.13.10.2 : Database - ticket_log
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
USE `ticket_log`;

/*Table structure for table `log_payment` */

DROP TABLE IF EXISTS `log_payment`;

CREATE TABLE `log_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `type` tinyint(4) DEFAULT '0' COMMENT '操作类型',
  `num` int(11) DEFAULT '0' COMMENT '操作数量',
  `payment_id` int(11) DEFAULT '0' COMMENT '支付单ID',
  `order_ids` varchar(500) NOT NULL COMMENT '订单ID，多个逗号隔开',
  `content` text COMMENT '内容',
  `distributor_id` int(11) DEFAULT '0' COMMENT '分销商ID',
  `organization_id` int(11) DEFAULT NULL COMMENT '机构ID',
  `landscape_id` int(11) DEFAULT NULL COMMENT '景区ID',
  `user_id` int(11) NOT NULL COMMENT '操作人编号',
  `user_name` varchar(64) NOT NULL COMMENT '操作人名称',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
