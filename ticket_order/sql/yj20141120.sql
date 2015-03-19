CREATE TABLE `transaction_flow` (
  `id` bigint(20) NOT NULL COMMENT '交易流水号',
  `mode` enum('cash','offline','credit','pos','alipay','advance','union','kuaiqian','taobao') NOT NULL DEFAULT 'credit' COMMENT '交易方式:cash,offline,credit,pos,alipay,advance,union,kuaiqian,taobao',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '交易类型:1pay,2refund,3recharge,4enchashment',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '支付金额',
  `supplier_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `agency_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商ID',
  `ip` varchar(20) DEFAULT NULL COMMENT 'ip地址',
  `op_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作人员uid',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '记录删除时间',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `agency_id` (`agency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='交易流水记录表';