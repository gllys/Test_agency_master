use `ticket_info`;

CREATE TABLE `agency_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `agency_id` int(11) unsigned NOT NULL COMMENT '经销商ID',
  `product_id` int(11) unsigned NOT NULL COMMENT '商品ID',
  `product_name` varchar(250) NOT NULL COMMENT '商品名称',
  `price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `source` tinyint(3) NOT NULL COMMENT '来源',
  `code` varchar(200) NOT NULL COMMENT '对接码',
  `payment` varchar(50) NOT NULL COMMENT '支付方式',
  `payment_list` varchar(250) NOT NULL COMMENT '可用支付列表',
  `create_at` int(11) NOT NULL,
  `update_at` int(11) NOT NULL COMMENT '上次更新时间',
  `delete_at` int(11) NOT NULL COMMENT '删除日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='分销商商品'