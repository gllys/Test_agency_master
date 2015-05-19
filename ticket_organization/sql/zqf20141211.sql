USE `ticket_organization`;
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_RECODE_NULL','记录不存在');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_DEL_1','删除失败');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_RECHARGE_1','请输入充值额度');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_RECHARGE_2','本次充值已成功');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_ENCASH_1','请输入提现额度');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_ENCASH_2','平台资金不足，无法提现');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_ENCASH_3','缺少提现审核人UID');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_GLOBEL_1','操作失败');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_GLOBEL_2','用户帐号不能为空');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_GLOBEL_3','缺少记录ID');
INSERT INTO `language_config` (id,zh) VALUES ('INFO_GLOBEL_0','操作成功');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_UNION_1','缺少平台资金变动额度');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_UNION_2','平台资金余额不足，无法继续操作');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_UNION_3','缺少支付方式');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_UNION_4','缺少交易类型');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_UNION_5','该机构不支持该交易类型');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_UNION_6','用户银行账户信息不完整');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_UNION_7','该提现申请已打款');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_UNION_8','请上传打款凭证图片');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_UNION_9','该提现申请已驳回');

CREATE TABLE `union_money` (
  `org_id` int(11) unsigned NOT NULL COMMENT '机构ID',
  `union_money` decimal(10,2) DEFAULT '0.00' COMMENT '总可用余额',
  `online_paid_money` decimal(10,2) DEFAULT '0.00' COMMENT '订单通过在线支付的总金额',
  `frozen_money` decimal(10,2) DEFAULT '0.00' COMMENT '冻结金额',
  `credit_money` decimal(10,2) DEFAULT '0.00' COMMENT '来自信用卡充值的额度，不能提现额度',
  `balance_type` tinyint(1) unsigned DEFAULT '0' COMMENT '结算类型：0不限1周2月',
  `balance_cycle` tinyint(3) unsigned DEFAULT '0' COMMENT '结算周期：周0～6，月1-31日',
  `op_uid` int(11) unsigned DEFAULT '0' COMMENT '操作者UID',
  `admin_uid` int(11) DEFAULT '0' COMMENT '后台编辑者UID',
  `admin_name` varchar(100) NOT NULL DEFAULT '' COMMENT '后台编辑者名称',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `updated_at` int(10) unsigned DEFAULT '0' COMMENT '记录更新时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '记录删除时间',
  PRIMARY KEY (`org_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构平台资金';

CREATE TABLE `union_money_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '平台资金变动记录ID',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `org_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `org_role` tinyint(1) unsigned DEFAULT '0' COMMENT '机构角色：0分销售，1供应商',
  `op_uid` int(11) unsigned DEFAULT '0' COMMENT '操作者UID',
  `op_account` varchar(100) DEFAULT NULL COMMENT '操作者账号',
  `op_username` varchar(100) DEFAULT NULL COMMENT '操作者姓名',
  `money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '金额变动',
  `in_out` tinyint(1) unsigned DEFAULT '0' COMMENT '收支：0支出，1收入',
  `trade_type` tinyint(1) unsigned DEFAULT '1' COMMENT '交易类型:1支付,2退款,3充值,4提现,5应收账款',
  `pay_type` tinyint(1) DEFAULT '0' COMMENT '支付方式：0平台1块钱2支付宝',
  `used_credit` decimal(10,2) DEFAULT '0.00' COMMENT '交易用到的信用卡资金',
  `union_money` decimal(10,2) DEFAULT '0.00' COMMENT '平台可提现余额',
  `frozen_money` decimal(10,2) DEFAULT '0.00' COMMENT '平台冻结余额',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `org_id` (`org_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构平台资金变动记录';

CREATE TABLE `union_money_encash` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '平台资金变动记录ID',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `org_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `org_role` tinyint(1) unsigned DEFAULT '0' COMMENT '机构角色：0分销售，1供应商',
  `money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '提现金额',
  `union_money` decimal(10,2) DEFAULT '0.00' COMMENT '平台资金余额',
  `apply_uid` int(11) unsigned DEFAULT '0' COMMENT '申请者UID',
  `apply_account` varchar(100) DEFAULT NULL COMMENT '申请者账号',
  `apply_username` varchar(100) DEFAULT NULL COMMENT '申请者名称',
  `apply_phone` varchar(30) DEFAULT NULL COMMENT '申请者电话',
  `bank_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '银行id',
  `bank_name` varchar(100) NOT NULL COMMENT '银行名称',
  `open_bank` varchar(100) DEFAULT NULL COMMENT '开户行',
  `account` varchar(100) NOT NULL COMMENT '账号/卡号',
  `account_name` varchar(50) NOT NULL COMMENT '账户名',
  `check_uid` int(11) unsigned DEFAULT '0' COMMENT '审核者UID',
  `paid_at` int(10) DEFAULT '0' COMMENT '打款时间',
  `paid_img` varchar(255) DEFAULT NULL COMMENT '打款凭证图片url',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态：0未打款，1已打款，2驳回',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `updated_at` int(10) DEFAULT '0' COMMENT '记录修改时间',
  PRIMARY KEY (`id`),
  KEY `org_id` (`org_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构平台资金提现申请记录';


CREATE TABLE `union_money_recharge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '平台资金充值记录ID',
  `org_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '充值金额',
  `pay_type` tinyint(1) DEFAULT '0' COMMENT '支付方式：1块钱2支付宝',
  `is_credit` tinyint(1) DEFAULT '0' COMMENT '是否信用卡充值',
  `paid_at` int(10) unsigned DEFAULT '0' COMMENT '打款时间',
  `op_uid` int(11) unsigned DEFAULT '0' COMMENT '操作者UID',
  `op_account` varchar(100) DEFAULT NULL COMMENT '操作者账号',
  `op_username` varchar(100) DEFAULT NULL COMMENT '操作者姓名',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  PRIMARY KEY (`id`),
  KEY `org_id` (`org_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构平台资金充值记录';