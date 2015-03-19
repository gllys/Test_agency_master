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

/*Table structure for table `config` */

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `config_key` varchar(100) NOT NULL COMMENT '设置的key',
  `config_value` varchar(512) NOT NULL COMMENT '设置的value',
  PRIMARY KEY (`config_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `config` */

insert  into `config`(`config_key`,`config_value`) values ('conf_bill_type','1');
insert  into `config`(`config_key`,`config_value`) values ('conf_bill_value','3');
insert  into `config`(`config_key`,`config_value`) values ('device_version','5');
insert  into `config`(`config_key`,`config_value`) values ('device_force','1');
insert  into `config`(`config_key`,`config_value`) values ('device_url','http://mobile1-test.b0.upaiyun.com/android/Checkin/Checkin_Distributor_V5.apk');
insert  into `config`(`config_key`,`config_value`) values ('activity_ticket_id','175');
insert  into `config`(`config_key`,`config_value`) values ('activity_distributor_id','217');
insert  into `config`(`config_key`,`config_value`) values ('activity_use_day','2014-11-18');
insert  into `config`(`config_key`,`config_value`) values ('activity_payment','credit');
insert  into `config`(`config_key`,`config_value`) values ('np_device_force','1');
insert  into `config`(`config_key`,`config_value`) values ('np_device_version','1');
insert  into `config`(`config_key`,`config_value`) values ('np_device_url','http://mobile1-test.b0.upaiyun.com/android/Checkin/Checkin_WuYi_V1.apk');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
