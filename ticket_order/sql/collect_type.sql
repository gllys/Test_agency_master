-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2014-12-02 18:47:27
-- 服务器版本： 5.5.40-log
-- PHP Version: 5.4.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `statsys_nponeyuan`
--

-- --------------------------------------------------------

--
-- 表的结构 `collect_type`
--

CREATE TABLE IF NOT EXISTS `collect_type` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

--
-- 转存表中的数据 `collect_type`
--

INSERT INTO `collect_type` (`id`, `name`) VALUES
(1, '顺昌张敦乡村休闲旅游景区'),
(2, '武夷山风景名胜区'),
(3, '武夷山茶博园景区'),
(4, '邵武和平古镇'),
(5, '邵武天成奇峡景区'),
(6, '邵武瀑布林温泉景区'),
(10, '顺昌华阳山景区'),
(12, '建瓯根雕城景区'),
(17, '延平溪源峡谷景区'),
(100, '单页扫描');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
