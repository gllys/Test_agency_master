/*
SQLyog Ultimate v11.24 (32 bit)
MySQL - 5.5.40-log : Database - ticket_order
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `order_items` */

DROP TABLE IF EXISTS `order_items`;

CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(20) NOT NULL COMMENT '订单号',
  `ticket_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '票类型:1电子票2任务单',
  `ticket_template_id` int(11) NOT NULL COMMENT '票种ID',
  `price_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '价格类型：1散客2团客3销售4挂牌,5日价,6合作',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '票单价',
  `nums` mediumint(8) unsigned NOT NULL DEFAULT '1' COMMENT '门票张数',
  `used_nums` mediumint(8) unsigned DEFAULT '0' COMMENT '已使用张数',
  `refunding_nums` mediumint(8) unsigned DEFAULT '0' COMMENT '退款中张数',
  `refunded_nums` mediumint(8) unsigned DEFAULT '0' COMMENT '已退款张数',
  `supplier_id` int(10) unsigned NOT NULL COMMENT '供应商id',
  `landscape_id` int(10) unsigned NOT NULL COMMENT '景区id',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '记录更新时间',
  `deleted_at` int(10) DEFAULT '0' COMMENT '记录删除时间',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '门票名称',
  `fat_price` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '散客价',
  `group_price` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '团客价',
  `sale_price` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '销售价',
  `listed_price` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '挂牌价',
  `valid` int(11) NOT NULL DEFAULT '0' COMMENT '门票有效期，预定后多少天内有效',
  `payment` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1,2,3,4' COMMENT '支付方式： 1：支线支付，2：信用支付，3：储值支付，4：平台储值支付',
  `scenic_id` int(12) NOT NULL DEFAULT '0' COMMENT '景区ID',
  `view_point` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '景点ID',
  `week_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '周几使用只能',
  `refund` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许退票1:允许：0不允许',
  `remark` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '名票说明',
  `organization_id` int(12) NOT NULL DEFAULT '0' COMMENT '机构ID',
  `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '票类型 ；0 =>电子票  1=>任务单',
  `date_available` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '可玩日期  int(11),int(11) 表示一个时间段 ，逗号分隔',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20993 DEFAULT CHARSET=utf8 COMMENT='订单明细表';

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL COMMENT '订单号',
  `status` enum('active','cancel','finish','billed') NOT NULL DEFAULT 'active' COMMENT '订单状态 活动订单（可操作）|已取消|已结束|已结款',
  `nums` mediumint(8) unsigned NOT NULL DEFAULT '1' COMMENT '门票张数',
  `used_nums` mediumint(8) unsigned DEFAULT '0' COMMENT '已使用张数',
  `refunding_nums` mediumint(8) unsigned DEFAULT '0' COMMENT '退款中张数',
  `refunded_nums` mediumint(8) unsigned DEFAULT '0' COMMENT '已退款张数',
  `money_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单结算金额',
  `money_remain` decimal(10,2) DEFAULT '0.00' COMMENT '订单剩余金额',
  `use_day` date NOT NULL COMMENT '游玩时间',
  `pay_at` int(10) DEFAULT '0' COMMENT '支付时间',
  `pay_type` enum('online','offline','credit','advance','union') NOT NULL DEFAULT 'offline' COMMENT '支付方式类型：线上、线下、信用支付、储值支付',
  `payment` tinyint(3) NOT NULL DEFAULT '0' COMMENT '支付渠道:1cash,2offline,3credit,4pos,5alipay,6advance,7union,8kuaiqian',
  `payment_id` bigint(20) DEFAULT NULL,
  `payed` decimal(10,2) DEFAULT '0.00' COMMENT '已支付金额',
  `owner_name` varchar(100) DEFAULT NULL COMMENT '取票人',
  `owner_mobile` varchar(100) DEFAULT NULL COMMENT '取票人手机',
  `owner_card` varchar(20) NOT NULL DEFAULT '' COMMENT '订单使用者身份证',
  `distributor_id` int(10) unsigned DEFAULT '0' COMMENT '分销商ID',
  `supplier_id` int(10) unsigned DEFAULT '0' COMMENT '供应商ID',
  `landscape_id` int(10) unsigned DEFAULT NULL COMMENT '景区id',
  `remarks` text COMMENT '备注',
  `changed_useday_times` tinyint(3) DEFAULT '0' COMMENT '游玩日期修改次数',
  `send_sms_nums` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发送短信成功次数',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录更新时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `orders_buyer_organization_id_idx` (`distributor_id`),
  KEY `orders_seller_organization_id_idx` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表';

/*Table structure for table `payments` */

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `id` bigint(20) NOT NULL COMMENT '支付单号',
  `order_id` bigint(20) NOT NULL COMMENT '订单号',
  `status` enum('succ','fail','cancel','error','invalid','progress','timeout','ready') NOT NULL DEFAULT 'ready' COMMENT '支付单状态',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `pay_type` enum('online','offline','credit','advance','union') NOT NULL DEFAULT 'online' COMMENT '支付方式类型：线上、线下、信用支付、储值支付',
  `payment` tinyint(3) NOT NULL DEFAULT '0' COMMENT '支付渠道:1cash,2offline,3credit,4pos,5alipay,6advance,7union,8kuaiqian',
  `money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `u_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `op_id` int(10) unsigned NOT NULL COMMENT '操作人员',
  `account` varchar(50) DEFAULT NULL COMMENT '收款账号',
  `bank` varchar(50) DEFAULT NULL COMMENT '收款银行',
  `pay_account` varchar(50) DEFAULT NULL COMMENT '支付账户',
  `remark` text COMMENT '支付单备注',
  `payment_bn` varchar(30) DEFAULT NULL COMMENT '交易流水',
  `ip` varchar(20) DEFAULT NULL COMMENT 'ip地址',
  `pay_app_id` varchar(100) NOT NULL DEFAULT '0' COMMENT '支付app',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `payments_order_id_idx` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付单';

/*Table structure for table `ticket_record` */

DROP TABLE IF EXISTS `ticket_record`;

CREATE TABLE `ticket_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '检票员id',
  `code` varchar(20) NOT NULL COMMENT '扫描二维码号',
  `record_code` varchar(20) DEFAULT NULL COMMENT '订单号',
  `tickets_code` text COMMENT '使用成功或失败的票，一张或多张,以逗号分隔',
  `ticket_type_name` varchar(30) DEFAULT NULL COMMENT '票名称',
  `num` smallint(6) NOT NULL DEFAULT '0' COMMENT '定义用了多少张票',
  `supplier_id` int(11) NOT NULL COMMENT '机构id',
  `landscape_id` int(11) NOT NULL COMMENT '景点id',
  `poi_id` int(11) NOT NULL COMMENT '子景点id',
  `equipment_code` varchar(20) NOT NULL COMMENT '检票设备id',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '检票结果 0:失败 1:成功',
  `http_status` smallint(6) NOT NULL,
  `note` varchar(50) DEFAULT NULL COMMENT '日志',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `ymd` (`created_at`),
  KEY `order_code` (`code`(10)),
  KEY `ticket_record_organization_id_idx` (`supplier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=716 DEFAULT CHARSET=utf8 COMMENT='检票记录(用户票使用表记录)';

/*Table structure for table `ticket_relations` */

DROP TABLE IF EXISTS `ticket_relations`;

CREATE TABLE `ticket_relations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '票号',
  `order_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单号',
  `poi_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '子景点id',
  `landscape_id` int(10) unsigned NOT NULL COMMENT '景区id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `ticket_relations_order_id_idx` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61472 DEFAULT CHARSET=utf8 COMMENT='订单的票跟子景点关联';

/*Table structure for table `ticket_used` */

DROP TABLE IF EXISTS `ticket_used`;

CREATE TABLE `ticket_used` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL COMMENT '检票员id',
  `order_id` bigint(20) unsigned NOT NULL COMMENT '订单号',
  `ticket_id` bigint(20) unsigned NOT NULL COMMENT '票号',
  `supplier_id` int(11) unsigned NOT NULL COMMENT '供应商id',
  `landscape_id` int(11) unsigned NOT NULL COMMENT '景点id',
  `poi_id` int(11) NOT NULL COMMENT '子景点id',
  `equipment_id` int(11) DEFAULT NULL COMMENT '检票设备id',
  `check_num` tinyint(3) NOT NULL DEFAULT '1' COMMENT '第几次验票',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `ymd` (`created_at`),
  KEY `order_id` (`order_id`),
  KEY `ticket_used_poi_id_idx` (`poi_id`)
) ENGINE=MyISAM AUTO_INCREMENT=377 DEFAULT CHARSET=utf8 COMMENT='检票记录(用户票使用表记录)';

/*Table structure for table `tickets` */

DROP TABLE IF EXISTS `tickets`;

CREATE TABLE `tickets` (
  `id` bigint(20) unsigned NOT NULL COMMENT '票号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0：不可使用 1：可使用',
  `order_id` bigint(20) unsigned NOT NULL COMMENT '订单号',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='票明细表';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
