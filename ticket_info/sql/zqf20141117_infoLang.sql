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
USE `ticket_info`;

/*Table structure for table `language_config` */

DROP TABLE IF EXISTS `language_config`;

CREATE TABLE `language_config` (
  `id` char(20) NOT NULL COMMENT '编号',
  `zh` varchar(256) NOT NULL COMMENT '简体中文',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `language_config` */

insert  into `language_config`(`id`,`zh`) values ('ERROR_AddGenerate_1','门票名称缺失'),('ERROR_AddGenerate_10','是否允许退票缺失'),('ERROR_AddGenerate_11','发布供应商缺失'),('ERROR_AddGenerate_12','门票说明不能为空'),('ERROR_AddGenerate_13','是否退款参数错误'),('ERROR_AddGenerate_14','是否全平台散客分销参数错误'),('ERROR_AddGenerate_15','是否全平台团客分销参数错误'),('ERROR_AddGenerate_16','最少订票不得少于1张'),('ERROR_AddGenerate_17','最少订票数必须为整数'),('ERROR_AddGenerate_18','最多订票数必须为整数'),('ERROR_AddGenerate_19','最少订票不得大于最多订票张数'),('ERROR_AddGenerate_2','散客价缺失'),('ERROR_AddGenerate_20','不是全平台散客分销例外不得为空'),('ERROR_AddGenerate_21','不是全平台团客分销例外不得为空'),('ERROR_AddGenerate_22','用户id不得为空'),('ERROR_AddGenerate_23','散客价团客价至少填写一个'),('ERROR_AddGenerate_24','电子单添加失败'),('ERROR_AddGenerate_3','团体价缺失'),('ERROR_AddGenerate_4','游玩有效期缺失'),('ERROR_AddGenerate_5','景区缺失'),('ERROR_AddGenerate_6','景点缺失'),('ERROR_AddGenerate_7','需提前天数缺失'),('ERROR_AddGenerate_8','可玩日期缺失'),('ERROR_AddGenerate_9','可用星期缺失'),('ERROR_DATE_1','请选择日期'),('ERROR_DATE_2','日期格式必须是xxxx-xx-xx'),('ERROR_DISCOUNT_1','缺少优惠规则ID参数'),('ERROR_DISCOUNT_2','该优惠规则记录不存在'),('ERROR_DISCOUNT_3','缺少优惠规则名称参数'),('ERROR_DISCOUNT_4','缺少优惠减免数值参数'),('ERROR_DISCOUNT_5','缺少优惠开始日期参数'),('ERROR_DISCOUNT_6','缺少优惠结束日期参数'),('ERROR_DISCOUNT_7','缺少限制清单ID参数'),('ERROR_DISCOUNT_8','优惠开始日期不能大于结束日期'),('ERROR_DISCOUNT_9','该优惠规则已有票在使用'),('ERROR_NAMELIST_1','缺少限制清单名称参数'),('ERROR_NAMELIST_2','缺少分销商ID参数'),('ERROR_NAMELIST_3','缺少限制清单ID参数'),('ERROR_NAMELIST_4','该限制清单记录不存在'),('ERROR_NAMELIST_5','该限制清单已有优惠规则在使用'),('ERROR_NAMELIST_6','该限制清单已有票在使用'),('ERROR_OPERATE_0','操作成功'),('ERROR_OPERATE_1','操作失败'),('ERROR_OPERATOR_1','缺少操作者user_id参数'),('ERROR_OPERATOR_2','缺少操作者user_name参数'),('ERROR_PRICE_1','价格不能为空'),('ERROR_PRICE_2','价格必须是数字，且小数位不能超过2位'),('ERROR_RESERVE_1','库存必须是大于0的整数'),('ERROR_SIGN_1','缺少签名参数'),('ERROR_SIGN_2','签名参数错误'),('ERROR_SUPPLIER_1','缺少供应商ID参数'),('ERROR_TICKET_1','缺少票ID参数'),('ERROR_TKT_RULE_1','价格规则名称不能为空'),('ERROR_TKT_RULE_2','缺少规则ID参数'),('ERROR_TKT_RULE_3','缺少供应商ID参数'),('ERROR_TKT_RULE_4','该价格规则记录不存在'),('ERROR_TKT_RULE_5','请设置规则的日价格或库存'),('ERROR_TKT_RULE_6','购票张数不能超出当日库存剩余数'),('ERROR_TKT_RULE_7','该价格规则已有票在使用'),('ERROR_YM_1','年月格式必须是xxxx-xx'),('ERRO_TICKET_10','门票有效期不能为空'),('ERRO_TICKET_11','修改失败'),('ERRO_TICKET_12','状态不能为空'),('ERRO_TICKET_13','分销商价格不能为空'),('ERRO_TICKET_14','票类型type参数：1团客，0散客'),('ERRO_TICKET_15','这张票已经下架'),('ERRO_TICKET_2','没有景区ID参数'),('ERRO_TICKET_3','没有机构ID参数'),('ERRO_TICKET_4','你没有修改权限'),('ERRO_TICKET_5','票名不能为空'),('ERRO_TICKET_6','散客价不能为空'),('ERRO_TICKET_7','团客价不能为空'),('ERRO_TICKET_8','销售价不能为空'),('ERRO_TICKET_9','挂牌价不能为空'),('INFO_DAY_PRICE_1','设置了票的日价格'),('INFO_DAY_PRICE_2','删除了票的日价格'),('INFO_DAY_RESERVE_1','设置了票的日库存'),('INFO_DAY_RESERVE_2','删除了票的日库存'),('INFO_DISCOUNT_1','添加了优惠规则记录'),('INFO_DISCOUNT_2','更新了优惠规则记录'),('INFO_DISCOUNT_3','删除了优惠规则记录'),('INFO_NAMELIST_1','添加了分销商限制清单记录'),('INFO_NAMELIST_2','更新了分销商限制清单记录'),('INFO_NAMELIST_3','删除了价格规则记录'),('INFO_TKT_RULE_1','添加了价格规则记录'),('INFO_TKT_RULE_2','更新了价格规则记录'),('INFO_TKT_RULE_3','删除了价格规则记录'),('INFO_TKT_RULE_4','设置了价格规则的日价格和库存记录'),('INFO_TKT_RULE_5','删除了价格规则的日价格和库存记录');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
