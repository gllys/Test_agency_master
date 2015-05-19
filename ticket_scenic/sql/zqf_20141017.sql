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
/*Table structure for table `poi` */
/*
DROP TABLE IF EXISTS `poi`;

CREATE TABLE `poi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '景点id',
  `name` varchar(100) NOT NULL COMMENT 'POI名称，指物理景区，跟票的景区无关系',
  `organization_id` int(10) unsigned NOT NULL COMMENT '机构id',
  `landscape_id` int(11) unsigned NOT NULL COMMENT '景区id',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已删除0否1是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态1上架0下架',
  PRIMARY KEY (`id`),
  KEY `poi_organization_id_idx` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='poi';
*/
ALTER TABLE `ticket_scenic`.`poi` ADD COLUMN `status` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL COMMENT '状态1上架0下架' AFTER `deleted`;
ALTER TABLE `ticket_scenic`.`landscapes` DROP COLUMN `description`;
ALTER TABLE `ticket_scenic`.`landscapes` ADD COLUMN `on_shelf` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL COMMENT '是否上架：1是，0否' AFTER `api_channel_id`;

CREATE TABLE `landscape_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片ID',
  `landscape_id` int(10) unsigned NOT NULL COMMENT '景区ID',
  `img_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '图片url',
  `created_at` datetime NOT NULL COMMENT '上传时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  COMMENT='景区图片';

/*[9:19:18][3 ms]*/
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
