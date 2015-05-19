# Dump of table credit
# ------------------------------------------------------------

DROP TABLE IF EXISTS `supply_agency`;

CREATE TABLE `credit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `distributor_id` int(20) NOT NULL DEFAULT '0' COMMENT '分销商ID',
  `distributor_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '分销商名字',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '供应商名字',
  `credit_money` decimal(20,2) NOT NULL COMMENT '信用余额',
  `balance_money` decimal(20,2) NOT NULL COMMENT '储值余额',
  `checkout_type` tinyint(1) DEFAULT NULL COMMENT '结算周期  类型 0->周， 1->月',
  `checkout_date` int(2) NOT NULL COMMENT '结算周期1-31',
  `credit_infinite` tinyint(1) NOT NULL DEFAULT '0' COMMENT '信用无限 0：否，1：是',
  `balance_over` varchar(20) COLLATE utf8_unicode_ci DEFAULT '0.00' COMMENT '储值透支额度 MAX:USE  上限：已用',
  `add_time` int(11) DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `distributor_id` (`distributor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table credit_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `credit_log`;

CREATE TABLE `credit_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `distributor_id` int(11) DEFAULT NULL COMMENT '分销商ID',
  `supplier_id` int(11) DEFAULT NULL COMMENT '供应商ID',
  `user_id` int(11) DEFAULT NULL COMMENT '操作员',
  `credit_moeny` int(11) DEFAULT '0' COMMENT '操作信用额度',
  `balance_money` int(11) DEFAULT '0' COMMENT '操作储值额度',
  `remark` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作原因',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `distributor_id` (`distributor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table credit_pay
# ------------------------------------------------------------

DROP TABLE IF EXISTS `credit_pay`;

CREATE TABLE `credit_pay` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `serial_id` int(13) DEFAULT NULL COMMENT '流水ID',
  `supplier_id` int(11) DEFAULT NULL COMMENT '供应商ID',
  `distributor_id` int(11) DEFAULT NULL COMMENT '分销商ID',
  `money` decimal(20,2) DEFAULT NULL COMMENT '金额',
  `type` tinyint(1) DEFAULT NULL COMMENT '支付方式0：信用，1：储值',
  `add_time` int(11) DEFAULT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table supply_agency_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `supply_agency_history`;

CREATE TABLE `supply_agency_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supply_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '供应商id',
  `agency_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分销商id',
  `agency_name` varchar(255) NOT NULL DEFAULT '' COMMENT '分销商名字',
  `is_bind` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否绑定',
  `unbind_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '解除时间',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='供应分销关联';




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


INSERT INTO `language_config` (`id`, `zh`)
VALUES
	('ERROR_CREDIT_10', '没有结算天算'),
	('ERROR_CREDIT_11', '没有类型'),
	('ERROR_CREDIT_12', '添加失败'),
	('ERROR_CREDIT_13', '没有分销商名字'),
	('ERROR_CREDIT_14', '已存在绑定关系'),
	('ERROR_CREDIT_15', '删除失败'),
	('ERROR_CREDIT_16', '没有这条数据 '),
	('ERROR_CREDIT_17', '没有输入额度'),
	('ERROR_CREDIT_18', '没有供应商ID'),
	('ERROR_CREDIT_19', '没有支付金额'),
	('ERROR_CREDIT_20', '帐户余额不足'),
	('ERROR_CREDIT_21', '没有流水号'),
	('ERROR_CREDIT_22', '支付失败！'),
	('ERROR_CREDIT_23', '没有绑定供应商'),
	('ERROR_CREDIT_24', '查询条件不能为空'),
	('ERROR_CREDIT_25', '供应商类型不正确'),
	('ERROR_CREDIT_26', '已经存在的关系，不需要再绑定'),
	('ERROR_CREDIT_27', '额度不能为负数'),
	('ERROR_CREDIT_28', '订单已经支付成功，不需要重复提交'),
	('ERROR_CREDIT_8', '没有USER_ID'),
	('ERROR_CREDIT_9', '没有supplier_id');

