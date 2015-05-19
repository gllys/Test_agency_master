-- 平台按照供应商统计
CREATE TABLE `day_supply_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `day` date NOT NULL COMMENT '每天',
  `supplier_id` int(10) unsigned NOT NULL COMMENT '供应商id',
  `order_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单数量',
  `person_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订购人数',
  `used_person_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已使用人数',
  `unused_person_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '未使用人数',
  `refunded_person_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退款人数',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `receive_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '收入金额',
  `refunded` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `date_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1预定2游玩3入园',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录更新时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '记录删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='日供应统计表';

-- 平台按照单景区统计
CREATE TABLE `day_scenic_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `day` date NOT NULL COMMENT '每天',
  `landscape_ids` int(10) unsigned NOT NULL COMMENT '景区id',
  `order_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单数量',
  `person_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订购人数',
  `used_person_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已使用人数',
  `unused_person_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '未使用人数',
  `refunded_person_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退款人数',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `receive_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '收入金额',
  `refunded` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `date_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1预定2游玩3入园',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录更新时间',
  `deleted_at` int(10) unsigned DEFAULT '0' COMMENT '记录删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='日景区统计表';