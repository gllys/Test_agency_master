-- 退款表
CREATE TABLE IF NOT EXISTS `refund_apply`(
  `id` bigint(20) unsigned NOT NULL COMMENT '退款申请单号',
  `order_id` varchar(20) NOT NULL COMMENT '订单号',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '退款理由',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '门票名称',
  `money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `nums` int(10) unsigned NOT NULL COMMENT '退票张数',
  `u_id` int(10) unsigned NOT NULL COMMENT '收款用户id',
  `op_id` int(10) unsigned NOT NULL COMMENT '操作人员',
  `account` varchar(50) DEFAULT NULL COMMENT '收款账号',
  `bank` varchar(50) DEFAULT NULL COMMENT '收款银行',
  `pay_account` varchar(50) DEFAULT NULL COMMENT '支付账户',
  `reject_reason` text COMMENT '拒绝原因',
  `payment_bn` varchar(30) DEFAULT NULL COMMENT '交易流水，即支付时的交易流水',
  `pay_type` enum('online','offline','credit','advance','union') NOT NULL DEFAULT 'offline' COMMENT '支付方式类型：线上、线下、信用支付、储值支付',
  `ip` varchar(20) DEFAULT NULL COMMENT 'ip地址',
  `allow_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0未审核1已审核2未操作3驳回',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0退款中1退款成功2退款失败',
  `pay_app_id` enum('cash','offline','credit','pos','alipay','advance','union','kuaiqian','taobao') NOT NULL DEFAULT 'credit' COMMENT '支付渠道:cash,offline,credit,pos,alipay,advance,union,kuaiqian,taobao',
  `batch_no` varchar(40) DEFAULT NULL COMMENT '退款批次号',
  `order_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '交易日期',
  `distributor_id` int(10) unsigned DEFAULT '0' COMMENT '分销商ID',
  `supplier_id` int(10) unsigned DEFAULT '0' COMMENT '供应商ID',
  `landscape_id` int(10) unsigned DEFAULT NULL COMMENT '景区id',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `audited_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核员',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '退款';

CREATE TABLE IF NOT EXISTS `refund_apply_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refund_apply_id` varchar(20) NOT NULL COMMENT '申请单号',
  `ticket_id` varchar(20) NOT NULL COMMENT '票号',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退款申请关联票号';