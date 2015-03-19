-- phpMyAdmin SQL Dump
-- version 4.2.5
-- http://www.phpmyadmin.net
--
-- Host: 192.168.1.249
-- Generation Time: 2014-12-01 19:06:05
-- 服务器版本： 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ticket_order`
--

-- --------------------------------------------------------

--
-- 表的结构 `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) unsigned NOT NULL COMMENT '订单号',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型:0电子票订单1任务票订单',
  `kind` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '种类:1单票2联票3套票',
  `status` enum('unpaid','cancel','paid','finish','billed') NOT NULL DEFAULT 'unpaid' COMMENT '订单状态：未支付|已取消|已支付|已结束|已结款',
  `nums` mediumint(8) unsigned NOT NULL DEFAULT '1' COMMENT '门票张数',
  `used_nums` mediumint(8) unsigned DEFAULT '0' COMMENT '已使用张数',
  `refunding_nums` mediumint(8) unsigned DEFAULT '0' COMMENT '退款中张数',
  `refunded_nums` mediumint(8) unsigned DEFAULT '0' COMMENT '已退款张数',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单结算金额',
  `use_day` date NOT NULL COMMENT '游玩时间',
  `pay_type` enum('online','offline','credit','advance','union') DEFAULT NULL COMMENT '支付方式类型：线上、线下、信用支付、储值支付',
  `payment` enum('cash','offline','credit','pos','alipay','advance','union','kuaiqian','taobao','') DEFAULT NULL COMMENT '支付渠道:cash,offline,credit,pos,alipay,advance,union,kuaiqian,taobao',
  `payment_id` bigint(20) DEFAULT NULL COMMENT '支付单号',
  `refunded` decimal(10,2) DEFAULT '0.00' COMMENT '已退款金额',
  `payed` decimal(10,2) DEFAULT '0.00' COMMENT '已支付金额',
  `pay_at` int(10) DEFAULT '0' COMMENT '支付时间',
  `owner_name` varchar(20) DEFAULT NULL COMMENT '取票人',
  `owner_mobile` varchar(20) DEFAULT NULL COMMENT '取票人手机',
  `owner_card` varchar(20) DEFAULT '' COMMENT '取票人身份证',
  `remark` text COMMENT '备注',
  `distributor_id` int(10) unsigned DEFAULT '0' COMMENT '分销商ID',
  `distributor_name` varchar(255) NOT NULL DEFAULT '',
  `supplier_id` int(10) unsigned DEFAULT '0' COMMENT '供应商ID',
  `supplier_name` varchar(255) NOT NULL DEFAULT '',
  `landscape_ids` varchar(255) NOT NULL COMMENT '景区id,多个逗号分隔',
  `changed_useday_times` tinyint(3) DEFAULT '0' COMMENT '游玩日期修改次数',
  `send_sms_nums` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发送短信成功次数',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录更新时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '记录删除时间',
  `price_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '价格类型：0散客1团客2合作3日价',
  `ota_type` varchar(20) NOT NULL DEFAULT 'system' COMMENT 'ota类型system、weixin',
  `ota_account` int(11) DEFAULT '0',
  `ota_name` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='alter table orders201410 modify owner_name varchar(20) COMMENT ''取票人'',modify owner_mobile varchar(20) COMMENT ''取票人手机'';';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
 ADD PRIMARY KEY (`id`), ADD KEY `orders_buyer_organization_id_idx` (`distributor_id`), ADD KEY `orders_seller_organization_id_idx` (`supplier_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
