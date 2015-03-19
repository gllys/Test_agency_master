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
/*Table structure for table `language_config` */

DROP TABLE IF EXISTS `language_config`;

CREATE TABLE `language_config` (
  `id` char(20) NOT NULL COMMENT '编号',
  `zh` varchar(256) NOT NULL COMMENT '简体中文',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `language_config` */

insert  into `language_config`(`id`,`zh`) values ('ERROR_ApplyList_1','时间格式不正确'),('ERROR_BILL_1','参数错误'),('ERROR_BILL_2','账单不存在'),('ERROR_BUYER_1','缺少分销商ID参数'),('ERROR_CheckRefund_1','申请退款id不能为空'),('ERROR_CheckRefund_2','操作用户id不能为空'),('ERROR_CheckRefund_3','审核状态不正确'),('ERROR_CheckRefund_4','拒绝理由不能为空'),('ERROR_CheckRefund_5','申请退款记录不存在'),('ERROR_CheckRefund_6','该申请单已被审核'),('ERROR_CheckRefund_7','审核失败'),('ERROR_CHECK_1','票数量不足'),('ERROR_END_DAY_1','结束日期格式为xxxx-xx-xx'),('ERROR_GLOBAL_1','参数错误'),('ERROR_GLOBAL_2','操作成功'),('ERROR_GLOBAL_3','操作失败'),('ERROR_LANDSCAPE_1','缺少景区ID参数'),('ERROR_NO_BUY_RIGHT','您没有权限购买该票【{ticket_name}】'),('ERROR_OPERATE_0','操作成功'),('ERROR_OPERATE_1','操作失败'),('ERROR_OPERATOR_1','缺少操作者user_id参数'),('ERROR_OPERATOR_2','缺少操作者user_name参数'),('ERROR_ORDER_1','没有订单编号'),('ERROR_ORDER_10','操作成功'),('ERROR_ORDER_11','操作失败'),('ERROR_ORDER_12','同有USER_ID'),('ERROR_ORDER_13','没有ID'),('ERROR_ORDER_14','购买人数不正确'),('ERROR_ORDER_2','没有取票人'),('ERROR_ORDER_3','没有手机号码'),('ERROR_ORDER_4','没有票ID'),('ERROR_ORDER_5','游玩日期不能为空'),('ERROR_ORDER_6','支付方式不能为空'),('ERROR_ORDER_7','门票类型不能为空'),('ERROR_ORDER_8','门票价格不能为空'),('ERROR_ORDER_9','门票数量不能为空'),('ERROR_ORDER_INFO_1','缺少订单号ID参数'),('ERROR_ORDER_INFO_2','缺少参数：分销商ID、供应商ID或景区ID'),('ERROR_ORDER_INFO_3','该订单记录不存在'),('ERROR_OWNER_1','取票人姓名不能为空'),('ERROR_OWNER_2','取票人手机号不能为空'),('ERROR_OWNER_3','取票人身份证号不能为空'),('ERROR_PAYMENT_1','缺少支付单号参数'),('ERROR_PAYMENT_2','该支付单记录不存在'),('ERROR_PAYMENT_3','不支持该支付方式({payment})'),('ERROR_PAYMENT_4','订单[{order_id}]中的门票【{ticket_name}】不支持支付方式：{payment}'),('ERROR_PAYMENT_5','该订单[{order_id}]已有支付单号[{payment_id}]'),('ERROR_PAYMENT_6','该支付单[{payment_id}]已支付成功，不能再操作'),('ERROR_REFUNDAPPLY_1','票张数不能为空'),('ERROR_REFUNDAPPLY_2','订单id不能为空'),('ERROR_REFUNDAPPLY_3','用户id不能为空'),('ERROR_REFUNDAPPLY_4','订单不存在'),('ERROR_REFUNDAPPLY_5','当前订单状态不能退款'),('ERROR_REFUNDAPPLY_6','退票张数大于可退张数'),('ERROR_REFUNDAPPLY_7','该票不能退款'),('ERROR_REFUNDAPPLY_8','申请退票失败'),('ERROR_SALER_1','缺少供应商ID参数'),('ERROR_SIGN_1','缺少签名参数'),('ERROR_SIGN_2','签名参数错误'),('ERROR_SMSSEND_1','手机号码格式不对'),('ERROR_SMSSEND_2','内容不能为空'),('ERROR_SMSSEND_3','短信发送失败'),('ERROR_TKT_1','缺少票种ID参数'),('ERROR_TKT_2','票种记录不存在'),('ERROR_TKT_3','请选择要订购的票'),('ERROR_TKT_4','门票【{ticket_name}】记录不存在'),('ERROR_TK_NUMS_1','订购票数不能少于1'),('ERROR_TK_RESERVE','该票预订的数量[{nums}]超过了票【{ticket_name}】的剩余日库存[{remain_reserve}]'),('ERROR_TK_USE_DAY','您设置的游玩日期[{use_day}]该票【{ticket_name}】不能使用'),('ERROR_UPDATE_0','记录更新成功'),('ERROR_UPDATE_2','状态参数有错'),('ERROR_USEDAY_1','游玩日期不能为空，且格式为xxxx-xx-xx'),('ERROR_USEDAY_2','游玩日期格式为xxxx-xx-xx'),('ERROR_USEDAY_3','游玩时间不得低于预定时间'),('ERROR_VERIFY_1','请选择检票方式'),('ERROR_VERIFY_2','请输入要检票的手机号、身份证号或订单号'),('ERROR_VERIFY_3','不存在可用的门票'),('ERROR_VERIFY_4','缺少景点参数'),('ERROR_VERIFY_5','景点不存在'),('ERROR_VERIFY_6','可用门票数量不足'),('INFO_ORDER_1','添加了订单'),('INFO_ORDER_2','更新了订单'),('INFO_ORDER_3','删除了订单'),('INFO_PAYMENT_1','添加了支付单(支付单号：{id}；订单号：{order_ids})'),('INFO_PAYMENT_2','更新了支付单'),('INFO_PAYMENT_3','删除了支付单');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
