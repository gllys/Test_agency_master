# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 192.168.1.14 (MySQL 5.5.40)
# Database: openapi_ticket
# Generation Time: 2015-01-10 09:04:57 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `config_key` varchar(100) NOT NULL COMMENT '设置的key',
  `config_value` varchar(512) NOT NULL COMMENT '设置的value',
  PRIMARY KEY (`config_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table language_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `language_config`;

CREATE TABLE `language_config` (
  `id` char(20) NOT NULL COMMENT '编号',
  `zh` varchar(256) NOT NULL COMMENT '简体中文',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `language_config` WRITE;
/*!40000 ALTER TABLE `language_config` DISABLE KEYS */;

INSERT INTO `language_config` (`id`, `zh`)
VALUES
	('EOOR_API_1','{error}'),
	('EOOR_SB_10','绑定类型不对'),
	('ERROR_AddGenerate_1','门票名称缺失'),
	('ERROR_AddGenerate_10','是否允许退票缺失'),
	('ERROR_AddGenerate_11','发布供应商缺失'),
	('ERROR_AddGenerate_12','门票说明不能为空'),
	('ERROR_AddGenerate_13','是否退款参数错误'),
	('ERROR_AddGenerate_14','是否全平台散客分销参数错误'),
	('ERROR_AddGenerate_15','是否全平台团客分销参数错误'),
	('ERROR_AddGenerate_16','最少订票不得少于1张'),
	('ERROR_AddGenerate_17','最少订票数必须为整数'),
	('ERROR_AddGenerate_18','最多订票数必须为整数'),
	('ERROR_AddGenerate_19','最少订票不得大于最多订票张数'),
	('ERROR_AddGenerate_2','散客价缺失'),
	('ERROR_AddGenerate_20','不是全平台散客分销例外不得为空'),
	('ERROR_AddGenerate_21','不是全平台团客分销例外不得为空'),
	('ERROR_AddGenerate_22','用户id不得为空'),
	('ERROR_AddGenerate_23','散客价团客价至少填写一个'),
	('ERROR_AddGenerate_24','电子单添加失败'),
	('ERROR_AddGenerate_3','团体价缺失'),
	('ERROR_AddGenerate_4','游玩有效期缺失'),
	('ERROR_AddGenerate_5','景区缺失'),
	('ERROR_AddGenerate_6','景点缺失'),
	('ERROR_AddGenerate_7','需提前天数缺失'),
	('ERROR_AddGenerate_8','可玩日期缺失'),
	('ERROR_AddGenerate_9','可用星期缺失'),
	('ERROR_ADDRESS_1','详细地址不能为空'),
	('ERROR_ADD_0','添加记录成功'),
	('ERROR_ADD_1','添加记录失败'),
	('ERROR_ApplyList_1','时间格式不正确'),
	('ERROR_BILL_1','参数错误'),
	('ERROR_BILL_2','账单不存在'),
	('ERROR_BIOGRAPHY_1','景区介绍不能为空'),
	('ERROR_BUYER_1','缺少分销商ID参数'),
	('ERROR_CANCELCODE_1','参数错误'),
	('ERROR_CANCELCODE_2','码已使用，退码失败'),
	('ERROR_CANCELCODE_3','已退码，请不要重复操作'),
	('ERROR_CANCELCODE_4','码已过期，退码失败'),
	('ERROR_CANCELCODE_5','无效的码'),
	('ERROR_CANCELCODE_6','已无票可退'),
	('ERROR_CANCELCODE_7','退票数不能大于未使用票数'),
	('ERROR_CheckRefund_1','申请退款id不能为空'),
	('ERROR_CheckRefund_2','操作用户id不能为空'),
	('ERROR_CheckRefund_3','审核状态不正确'),
	('ERROR_CheckRefund_4','拒绝理由不能为空'),
	('ERROR_CheckRefund_5','申请退款记录不存在'),
	('ERROR_CheckRefund_6','该申请单已被审核'),
	('ERROR_CheckRefund_7','审核失败'),
	('ERROR_CHECKSTATUS_1','参数错误'),
	('ERROR_CHECKSTATUS_2','无效的码'),
	('ERROR_CHECKTYPE_1','地址缺失'),
	('ERROR_CHECKTYPE_10','经营许可证不能为空'),
	('ERROR_CHECKTYPE_11','机构类型不正确'),
	('ERROR_CHECKTYPE_2','营业执照缺失'),
	('ERROR_CHECKTYPE_3','是否旅行社不能为空'),
	('ERROR_CHECKTYPE_4','是否开通全平台散客票不能为空'),
	('ERROR_CHECKTYPE_5','是否开通全平台团体票不能为空'),
	('ERROR_CHECKTYPE_6','是否旅行社类型不正确'),
	('ERROR_CHECKTYPE_7','类型出错'),
	('ERROR_CHECKTYPE_8','类型出错'),
	('ERROR_CHECKTYPE_9','税务登记证不能为空'),
	('ERROR_CHECK_1','票数量不足'),
	('ERROR_CREDIT_1','没有分销商ID'),
	('ERROR_CREDIT_10','没有结算天算'),
	('ERROR_CREDIT_11','没有类型'),
	('ERROR_CREDIT_12','添加失败'),
	('ERROR_CREDIT_13','没有分销商名字'),
	('ERROR_CREDIT_14','已存在绑定关系'),
	('ERROR_CREDIT_15','删除失败'),
	('ERROR_CREDIT_16','没有这条数据 '),
	('ERROR_CREDIT_17','没有输入额度'),
	('ERROR_CREDIT_18','没有供应商ID'),
	('ERROR_CREDIT_19','没有支付金额'),
	('ERROR_CREDIT_2','没有操作TYPE类型'),
	('ERROR_CREDIT_20','帐户余额不足'),
	('ERROR_CREDIT_21','没有流水号'),
	('ERROR_CREDIT_22','支付失败！'),
	('ERROR_CREDIT_23','没有绑定供应商'),
	('ERROR_CREDIT_24','查询条件不能为空'),
	('ERROR_CREDIT_25','供应商类型不正确'),
	('ERROR_CREDIT_26','已经存在的关系，不需要再绑定'),
	('ERROR_CREDIT_27','额度不能为负数'),
	('ERROR_CREDIT_28','订单已经支付成功，不需要重复提交'),
	('ERROR_CREDIT_3','没有操作num'),
	('ERROR_CREDIT_4','没有操作原因'),
	('ERROR_CREDIT_5','修改成功'),
	('ERROR_CREDIT_6','没有ID'),
	('ERROR_CREDIT_7','修改失败'),
	('ERROR_CREDIT_8','没有USER_ID'),
	('ERROR_CREDIT_9','没有supplier_id'),
	('ERROR_DATE_1','请选择日期'),
	('ERROR_DATE_2','日期格式必须是xxxx-xx-xx'),
	('ERROR_DEL_0','记录删除成功'),
	('ERROR_DEL_1','记录删除失败'),
	('ERROR_DEL_2','缺少参数ID'),
	('ERROR_DETAIL_1','缺少参数ID'),
	('ERROR_DETAIL_2','该景区记录不存在'),
	('ERROR_DEVICE_1','设备号不能为空'),
	('ERROR_DEVICE_10','没有历史记录'),
	('ERROR_DEVICE_11','无效的码'),
	('ERROR_DEVICE_2','缺少二维码、身份证号或手机号'),
	('ERROR_DEVICE_3','设备不存在'),
	('ERROR_DEVICE_4','设备未绑定'),
	('ERROR_DEVICE_5','该身份证没有可使用的门票'),
	('ERROR_DEVICE_6','该手机号没有可使用的门票'),
	('ERROR_DEVICE_7','没有可使用的门票'),
	('ERROR_DEVICE_8','可用门票数量不足'),
	('ERROR_DEVICE_9','操作失败'),
	('ERROR_DISCOUNT_1','缺少优惠规则ID参数'),
	('ERROR_DISCOUNT_2','该优惠规则记录不存在'),
	('ERROR_DISCOUNT_3','缺少优惠规则名称参数'),
	('ERROR_DISCOUNT_4','缺少优惠减免数值参数'),
	('ERROR_DISCOUNT_5','缺少优惠开始日期参数'),
	('ERROR_DISCOUNT_6','缺少优惠结束日期参数'),
	('ERROR_DISCOUNT_7','缺少限制清单ID参数'),
	('ERROR_DISCOUNT_8','优惠开始日期不能大于结束日期'),
	('ERROR_DISCOUNT_9','该优惠规则已有票在使用'),
	('ERROR_DISTRICT_1','请选择省份'),
	('ERROR_DISTRICT_2','请选择城市'),
	('ERROR_DISTRICT_3','请选择区县'),
	('ERROR_EDIT_1','机构id不存在'),
	('ERROR_EDIT_10','营业执照不得修改为空'),
	('ERROR_EDIT_11','是否旅行社修改出错'),
	('ERROR_EDIT_12','税务登记证不得修改为空'),
	('ERROR_EDIT_13','经营许可证不得修改为空'),
	('ERROR_EDIT_14','平台散客票分销权限修改出错'),
	('ERROR_EDIT_15','平台团体票分销权限修改出错'),
	('ERROR_EDIT_16','详细地址不得修改为空'),
	('ERROR_EDIT_17','删除字段出错'),
	('ERROR_EDIT_18','更新失败'),
	('ERROR_EDIT_2','不存在该机构'),
	('ERROR_EDIT_3','手机号不正确'),
	('ERROR_EDIT_4','联系人不得为空'),
	('ERROR_EDIT_5','已存在该机构名字'),
	('ERROR_EDIT_6','机构名字不得修改为空'),
	('ERROR_EDIT_7','用户id不能为空'),
	('ERROR_EDIT_8','审核状态不正确'),
	('ERROR_EDIT_9','启用状态不正确'),
	('ERROR_ENCASH_1','请输入提现额度'),
	('ERROR_ENCASH_2','平台资金不足，无法提现'),
	('ERROR_ENCASH_3','缺少提现审核人UID'),
	('ERROR_END_DAY_1','结束日期格式为xxxx-xx-xx'),
	('ERROR_EXADDRESS_1','取票地址不能为空'),
	('ERROR_GENCODE_1','购票数量不能为空'),
	('ERROR_GENCODE_2','客人姓名不能为空'),
	('ERROR_GENCODE_3','无效的手机号码'),
	('ERROR_GENCODE_4','无效的身份证号码'),
	('ERROR_GEN_BILL_0','成功生成了{n}张结款单'),
	('ERROR_GEN_BILL_1','已经结算'),
	('ERROR_GLOBAL_1','参数错误'),
	('ERROR_GLOBAL_2','操作成功'),
	('ERROR_GLOBAL_3','操作失败'),
	('ERROR_GLOBEL_1','操作失败'),
	('ERROR_GLOBEL_2','用户帐号不能为空'),
	('ERROR_GLOBEL_3','缺少记录ID'),
	('ERROR_HOURS_1','开放时间不能为空'),
	('ERROR_LANDIMG_1','缺少景区ID参数'),
	('ERROR_LANDSCAPE_1','缺少景区ID参数'),
	('ERROR_LANDSCAPE_2','景区名称不能为空'),
	('ERROR_LANDSCAPE_3','请选择景区级别'),
	('ERROR_LANDSCAPE_4','已存在此名称的景区，景区名称不能重复'),
	('ERROR_LAND_ORG_1','此景区已绑定该供应商'),
	('ERROR_LAND_ORG_2','缺少参数：供应商ID或景区ID'),
	('ERROR_LAND_ORG_3','缺少景区ID参数'),
	('ERROR_LAND_ORG_4','缺少供应商ID参数'),
	('ERROR_LAND_ORG_5','该绑定记录不存在'),
	('ERROR_LAND_ORG_6','该景区管理权限已分配给其他供应商，不能重复分配'),
	('ERROR_LIST_1','时间格式不正确'),
	('ERROR_NAMELIST_1','缺少限制清单名称参数'),
	('ERROR_NAMELIST_2','缺少分销商ID参数'),
	('ERROR_NAMELIST_3','缺少限制清单ID参数'),
	('ERROR_NAMELIST_4','该限制清单记录不存在'),
	('ERROR_NAMELIST_5','该限制清单已有优惠规则在使用'),
	('ERROR_NAMELIST_6','该限制清单已有票在使用'),
	('ERROR_NOTE_1','购票须知不能为空'),
	('ERROR_NO_BUY_RIGHT','您没有权限购买该票【{ticket_name}】'),
	('ERROR_OPERATE_0','操作成功'),
	('ERROR_OPERATE_1','操作失败'),
	('ERROR_OPERATOR_1','缺少操作者user_id参数'),
	('ERROR_OPERATOR_2','缺少操作者user_name参数'),
	('ERROR_ORDER_1','没有订单编号'),
	('ERROR_ORDER_10','操作成功'),
	('ERROR_ORDER_11','操作失败'),
	('ERROR_ORDER_12','同有USER_ID'),
	('ERROR_ORDER_13','没有ID'),
	('ERROR_ORDER_14','购买人数不正确'),
	('ERROR_ORDER_2','没有取票人'),
	('ERROR_ORDER_3','没有手机号码'),
	('ERROR_ORDER_4','没有票ID'),
	('ERROR_ORDER_5','游玩日期不能为空'),
	('ERROR_ORDER_6','支付方式不能为空'),
	('ERROR_ORDER_7','门票类型不能为空'),
	('ERROR_ORDER_8','门票价格不能为空'),
	('ERROR_ORDER_9','门票数量不能为空'),
	('ERROR_ORDER_INFO_1','缺少订单号ID参数'),
	('ERROR_ORDER_INFO_2','缺少参数：分销商ID、供应商ID或景区ID'),
	('ERROR_ORDER_INFO_3','该订单记录不存在'),
	('ERROR_OTA_1','OTA账号或账户名称错误'),
	('ERROR_OWNER_1','取票人姓名不能为空'),
	('ERROR_OWNER_2','取票人手机号不能为空'),
	('ERROR_OWNER_3','取票人身份证号不能为空'),
	('ERROR_PAYMENT_1','缺少支付单号参数'),
	('ERROR_PAYMENT_2','该支付单记录不存在'),
	('ERROR_PAYMENT_3','不支持该支付方式({payment})'),
	('ERROR_PAYMENT_4','订单[{order_id}]中的门票【{ticket_name}】不支持支付方式：{payment}'),
	('ERROR_PAYMENT_5','该订单[{order_id}]已有支付单号[{payment_id}]'),
	('ERROR_PAYMENT_6','该支付单[{payment_id}]已支付成功，不能再操作'),
	('ERROR_PAYMENT_7','支付方式不能为空'),
	('ERROR_PAYMENT_8','支付单生成失败'),
	('ERROR_PHONE_1','联系电话不能为空'),
	('ERROR_PLATFORM_1','交易类型参数有误'),
	('ERROR_PLATFORM_2','角色类型参数有误'),
	('ERROR_POI_1','缺少参数：供应商ID、景区ID或景点ID'),
	('ERROR_POI_2','缺少参数：供应商ID、景区ID'),
	('ERROR_POI_3','缺少参数：景点ID'),
	('ERROR_POI_4','景点名称不能为空'),
	('ERROR_POI_5','请选择景点所属供应商'),
	('ERROR_POI_6','请选择景点所属景区'),
	('ERROR_POI_7','有含该景点的门票未下架，请先下架门票再删除'),
	('ERROR_POI_8','该景区下已存在此名称的景点'),
	('ERROR_PRICE_1','价格不能为空'),
	('ERROR_PRICE_2','价格必须是数字，且小数位不能超过2位'),
	('ERROR_PRICE_TPL_1','价格模版名称不能为空'),
	('ERROR_PRICE_TPL_2','请设置票的合作价'),
	('ERROR_PRICE_TPL_3','缺少价格模版ID参数'),
	('ERROR_PRICE_TPL_4','该价格模版记录不存在'),
	('ERROR_PRICE_TPL_5','删除失败！有合作机构在使用此模版，请解除合作后再删除。'),
	('ERROR_PRODUCT_1','该产品不存在'),
	('ERROR_PRODUCT_2','该产品价格不符，无法购买'),
	('ERROR_RECHARGE_1','请输入充值额度'),
	('ERROR_RECHARGE_2','本次充值已成功'),
	('ERROR_RECODE_NULL','记录不存在'),
	('ERROR_REFUNDAPPLY_1','票张数不能为空'),
	('ERROR_REFUNDAPPLY_2','订单id不能为空'),
	('ERROR_REFUNDAPPLY_3','用户id不能为空'),
	('ERROR_REFUNDAPPLY_4','订单不存在'),
	('ERROR_REFUNDAPPLY_5','当前订单状态不能退款'),
	('ERROR_REFUNDAPPLY_6','退票张数大于可退张数'),
	('ERROR_REFUNDAPPLY_7','该票不能退款'),
	('ERROR_REFUNDAPPLY_8','申请退票失败'),
	('ERROR_REG_1','类型缺失'),
	('ERROR_REG_2','机构名称缺失'),
	('ERROR_REG_3','手机号不正确'),
	('ERROR_REG_4','联系人缺失'),
	('ERROR_REG_5','状态类型出错'),
	('ERROR_REG_6','机构名称已被注册'),
	('ERROR_REG_7','地区出错'),
	('ERROR_REG_8','注册失败'),
	('ERROR_RESERVE_1','库存必须是大于0的整数'),
	('ERROR_RESERVE_2','该日不存在日库存设置'),
	('ERROR_SALER_1','缺少供应商ID参数'),
	('ERROR_SB_1','设置类型不正确'),
	('ERROR_SB_10','删除失败'),
	('ERROR_SB_11','绑定类型不正确'),
	('ERROR_SB_12','绑定状态不正确'),
	('ERROR_SB_13','缺少机构ID'),
	('ERROR_SB_14','绑定的参数ID没找到'),
	('ERROR_SB_15','CODE已经存在'),
	('ERROR_SB_2','设备编号不能为空'),
	('ERROR_SB_3','缺少创建人员电话'),
	('ERROR_SB_4','添加设备成功'),
	('ERROR_SB_5','添加设备失败'),
	('ERROR_SB_6','修改成功'),
	('ERROR_SB_7','修改失败'),
	('ERROR_SB_8','缺少ID'),
	('ERROR_SB_9','删除成功'),
	('ERROR_SEARCH_1','该属性不支持搜索'),
	('ERROR_SEARCH_2','缺少搜索关键词'),
	('ERROR_SHOW_1','机构id不能为空'),
	('ERROR_SHOW_2','该机构不存在'),
	('ERROR_SIGN_1','缺少签名参数'),
	('ERROR_SIGN_2','签名参数错误'),
	('ERROR_SIGN_3','ACCOUNT错误'),
	('ERROR_SIGN_4','发起请求时的时间戳'),
	('ERROR_SMSSEND_1','手机号码格式不对'),
	('ERROR_SMSSEND_2','内容不能为空'),
	('ERROR_SMSSEND_3','短信发送失败'),
	('ERROR_START_DAY_1','开始日期格式为xxxx-xx-xx'),
	('ERROR_SUPPLIER_1','缺少供应商ID参数'),
	('ERROR_SUPPLY_ORG_1','缺少供应商ID参数'),
	('ERROR_TICKET_1','缺少票ID参数'),
	('ERROR_TKT_1','缺少票种ID参数'),
	('ERROR_TKT_2','票种记录不存在！'),
	('ERROR_TKT_3','请选择要订购的票'),
	('ERROR_TKT_4','门票【{ticket_name}】记录不存在'),
	('ERROR_TKT_RULE_1','价格规则名称不能为空'),
	('ERROR_TKT_RULE_2','缺少规则ID参数'),
	('ERROR_TKT_RULE_3','缺少供应商ID参数'),
	('ERROR_TKT_RULE_4','该价格规则记录不存在'),
	('ERROR_TKT_RULE_5','请设置规则的日价格或库存'),
	('ERROR_TKT_RULE_6','购票张数不能超出当日库存剩余数'),
	('ERROR_TKT_RULE_7','该价格规则已有票在使用'),
	('ERROR_TK_NUMS_1','订购票数不能少于1'),
	('ERROR_TK_RESERVE','该票预订的数量[{nums}]超过了票【{ticket_name}】的日库存[{day_reserve}]'),
	('ERROR_TK_USE_DAY','您设置的游玩日期[{use_day}]该票【{ticket_name}】不能使用'),
	('ERROR_TOKEN_1','操作失败'),
	('ERROR_TRANSIT_1','交通指南不能为空'),
	('ERROR_UNION_1','缺少平台资金变动额度'),
	('ERROR_UNION_2','平台资金余额不足，无法继续操作'),
	('ERROR_UNION_3','缺少支付方式'),
	('ERROR_UNION_4','缺少交易类型'),
	('ERROR_UNION_5','该机构不支持该交易类型'),
	('ERROR_UNION_6','用户银行账户信息不完整'),
	('ERROR_UNION_7','该提现申请已打款'),
	('ERROR_UNION_8','请上传打款凭证图片'),
	('ERROR_UNION_9','该提现申请已驳回'),
	('ERROR_UPDATE_0','记录更新成功'),
	('ERROR_UPDATE_1','记录更新失败'),
	('ERROR_UPDATE_2','状态参数有错'),
	('ERROR_UPDATE_3','缺少审核人参数'),
	('ERROR_USEDAY_1','游玩日期不能为空，且格式为xxxx-xx-xx'),
	('ERROR_USEDAY_2','游玩日期格式为xxxx-xx-xx'),
	('ERROR_USEDAY_3','游玩时间不得低于预定时间'),
	('ERROR_VERIFY_1','请选择检票方式'),
	('ERROR_VERIFY_2','请输入要检票的手机号、身份证号或订单号'),
	('ERROR_VERIFY_3','不存在可用的门票'),
	('ERROR_VERIFY_4','缺少景点参数'),
	('ERROR_VERIFY_5','景点不存在'),
	('ERROR_VERIFY_6','可用门票数量不足'),
	('ERROR_YM_1','年月格式必须是xxxx-xx'),
	('ERRO_TICKET_10','门票有效期不能为空'),
	('ERRO_TICKET_11','修改失败'),
	('ERRO_TICKET_12','状态不能为空'),
	('ERRO_TICKET_13','分销商价格不能为空'),
	('ERRO_TICKET_14','票类型type参数：1团客，0散客'),
	('ERRO_TICKET_15','这张票已经下架'),
	('ERRO_TICKET_2','没有景区ID参数'),
	('ERRO_TICKET_3','没有机构ID参数'),
	('ERRO_TICKET_4','你没有修改权限'),
	('ERRO_TICKET_5','票名不能为空'),
	('ERRO_TICKET_6','散客价不能为空'),
	('ERRO_TICKET_7','团客价不能为空'),
	('ERRO_TICKET_8','销售价不能为空'),
	('ERRO_TICKET_9','挂牌价不能为空'),
	('ERR_DOPAY_1','扣款失败'),
	('ERR_TKT_POLICY_1','分销策略名称不能为空'),
	('ERR_TKT_POLICY_2','请设置分销策略明细'),
	('ERR_TKT_POLICY_3','缺少分销策略ID参数'),
	('ERR_TKT_POLICY_4','该分销策略记录不存在'),
	('ERR_TKT_POLICY_5','删除失败！有产品票在使用此分销策略，请取消后再删除'),
	('INFO_DAY_PRICE_1','设置了票的日价格'),
	('INFO_DAY_PRICE_2','删除了票的日价格'),
	('INFO_DAY_RESERVE_1','设置了票的日库存'),
	('INFO_DAY_RESERVE_2','删除了票的日库存'),
	('INFO_DISCOUNT_1','添加了优惠规则记录'),
	('INFO_DISCOUNT_2','更新了优惠规则记录'),
	('INFO_DISCOUNT_3','删除了优惠规则记录'),
	('INFO_GEN_BILL_0','立即结算'),
	('INFO_GLOBEL_0','操作成功'),
	('INFO_LANDIMG_1','添加了景区图片'),
	('INFO_LANDIMG_2','更新了景区图片'),
	('INFO_LANDIMG_3','删除了景区图片'),
	('INFO_LANDORG_1','添加了景区和供应商绑定记录'),
	('INFO_LANDORG_2','更新了景区和供应商绑定记录'),
	('INFO_LANDORG_3','删除了景区和供应商绑定记录'),
	('INFO_LANDSCAPE_1','添加了景区记录'),
	('INFO_LANDSCAPE_2','更新了景区记录'),
	('INFO_LANDSCAPE_3','删除了景区记录'),
	('INFO_LANDSCAPE_4','审核通过了景区记录'),
	('INFO_LANDSCAPE_5','审核未通过景区记录'),
	('INFO_LANDSCAPE_6','把景区设为未审核状态'),
	('INFO_NAMELIST_1','添加了分销商限制清单记录'),
	('INFO_NAMELIST_2','更新了分销商限制清单记录'),
	('INFO_NAMELIST_3','删除了价格规则记录'),
	('INFO_ORDER_1','添加了订单'),
	('INFO_ORDER_2','更新了订单'),
	('INFO_ORDER_3','删除了订单'),
	('INFO_PAYMENT_1','添加了支付单(支付单号：{id}；订单号：{order_ids})'),
	('INFO_PAYMENT_2','更新了支付单'),
	('INFO_PAYMENT_3','删除了支付单'),
	('INFO_POI_1','添加了景点记录'),
	('INFO_POI_2','更新了景点记录'),
	('INFO_POI_3','上架了景点记录'),
	('INFO_POI_4','下架了景点记录'),
	('INFO_POI_5','删除了景点记录'),
	('INFO_PRICE_TPL_1','添加了价格模版'),
	('INFO_PRICE_TPL_2','更新了价格模版'),
	('INFO_PRICE_TPL_3','删除了价格模版'),
	('INFO_TKT_1','添加了票模板'),
	('INFO_TKT_2','更新了票模板'),
	('INFO_TKT_3','删除了票模板'),
	('INFO_TKT_POLICY_1','添加了分销策略记录'),
	('INFO_TKT_POLICY_3','删除了分销策略记录'),
	('INFO_TKT_RULE_1','添加了价格规则记录'),
	('INFO_TKT_RULE_2','更新了价格规则记录'),
	('INFO_TKT_RULE_3','删除了价格规则记录'),
	('INFO_TKT_RULE_4','设置了价格规则的日价格和库存记录'),
	('INFO_TKT_RULE_5','删除了价格规则的日价格和库存记录');

/*!40000 ALTER TABLE `language_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ota_account
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ota_account`;

CREATE TABLE `ota_account` (
  `id` varchar(32) NOT NULL,
  `name` varchar(100) NOT NULL,
  `pwd` varchar(64) NOT NULL,
  `salt` varchar(64) NOT NULL,
  `secret` varchar(64) DEFAULT NULL COMMENT '密钥',
  `distributor_id` int(11) DEFAULT '0' COMMENT '绑定的分销售id',
  `notify_url` varchar(128) DEFAULT NULL COMMENT '异步通知地址',
  `token` varchar(64) DEFAULT NULL COMMENT 'token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `ota_account` WRITE;
/*!40000 ALTER TABLE `ota_account` DISABLE KEYS */;

INSERT INTO `ota_account` (`id`, `name`, `pwd`, `salt`, `secret`, `distributor_id`, `notify_url`, `token`)
VALUES
	('61','同程','3ef09242e949e1d49e5977f2e252217b','d237666adecc','7E94A0BBD5517A255821C6E417C2D643',0,'',''),
	('62','途牛','3ef09242e949e1d49e5977f2e252217b','d237666adecc','646D527EC7861AD9043F33C162A79D49',0,'',''),
	('63','驴妈妈','3ef09242e949e1d49e5977f2e252217b','d237666adecc','60C667AE874A8393872724C3FBE91B8F',0,'',''),
	('64','携程','3ef09242e949e1d49e5977f2e252217b','d237666adecc','88C725BF15616B491C1255043C9877CE',0,'',''),
	('65','天猫','3ef09242e949e1d49e5977f2e252217b','d237666adecc','602740386C5A08D35FE02DF0242B4BEF',0,'',''),
	('66','景点通','3ef09242e949e1d49e5977f2e252217b','d237666adecc','791BB6065F732B5BBDBC77ACEDB2574A',0,'',''),
	('67','度周末','3ef09242e949e1d49e5977f2e252217b','d237666adecc','83E7D8B8847CABE685D74B29D8F12676',0,'',''),
	('68','途家','3ef09242e949e1d49e5977f2e252217b','d237666adecc','3A4EBE615AC4E39196C4EDC05076CA21',0,'',''),
	('1','test','3ef09242e949e1d49e5977f2e252217b','d237666adecc','5AFEC979740449D522E694CA82F6D9CF',167,'http://ticket-api-order.demo.org.cn','5CBB27FEF29A5AF4AD7731F7A4FA26CC');

/*!40000 ALTER TABLE `ota_account` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table process_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `process_config`;

CREATE TABLE `process_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(30) NOT NULL COMMENT '名称',
  `path` varchar(128) NOT NULL COMMENT '脚本',
  `num` tinyint(4) DEFAULT '0' COMMENT '数量 0等同CPU数量',
  `state` tinyint(4) DEFAULT '0' COMMENT '状态 0不运行 1运行',
  `run_type` tinyint(4) DEFAULT '0' COMMENT '0不限 1主服务器',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `process_config` WRITE;
/*!40000 ALTER TABLE `process_config` DISABLE KEYS */;

INSERT INTO `process_config` (`id`, `name`, `path`, `num`, `state`, `run_type`)
VALUES
	(2,'queue','Queue.php',1,1,0);

/*!40000 ALTER TABLE `process_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table scenic_difference
# ------------------------------------------------------------

DROP TABLE IF EXISTS `scenic_difference`;

CREATE TABLE `scenic_difference` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `scenic_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `scenic_difference` WRITE;
/*!40000 ALTER TABLE `scenic_difference` DISABLE KEYS */;

INSERT INTO `scenic_difference` (`id`, `created_at`, `scenic_id`)
VALUES
	(1,'2015-01-09 21:21:21',1),
	(2,'2015-01-08 21:21:21',2),
	(3,'2015-01-10 21:21:21',1);

/*!40000 ALTER TABLE `scenic_difference` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table version
# ------------------------------------------------------------

DROP TABLE IF EXISTS `version`;

CREATE TABLE `version` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(20) NOT NULL COMMENT '版本',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='版本表';

LOCK TABLES `version` WRITE;
/*!40000 ALTER TABLE `version` DISABLE KEYS */;

INSERT INTO `version` (`id`, `version`)
VALUES
	(1,'20140830'),
	(2,'20141101'),
	(3,'20141107'),
	(4,'20141127');

/*!40000 ALTER TABLE `version` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
