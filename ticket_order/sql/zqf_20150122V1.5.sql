use `ticket_order`;
INSERT INTO `language_config` (id,zh) VALUES ('ERR_COUPON_1', '抵用券金额不足');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_ORDER_16', '订单备注只能在未支付状态时才能更改');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_ORDER_17', '该订单已确认');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_PAYMENT_9', '订单［{order_id}］正在确认中，请在确认通过后再支付');

ALTER TABLE `order_items` ADD `bill_time` int(10) NOT NULL DEFAULT '0' COMMENT '结算时间';
ALTER TABLE `orders` CHANGE `status` `status` enum('unaudited','reject','unpaid','cancel','paid','finish','billed') NOT NULL DEFAULT 'unpaid' COMMENT '订单状态：待确认|未支付|已取消|已支付|已结束|已结款';
ALTER TABLE `orders` CHANGE `source` `source` TINYINT(1)  NULL  DEFAULT '0'  COMMENT '外部来源 0默认 1淘宝';
ALTER TABLE `orders` add `source_token` varchar(100) DEFAULT '' COMMENT '来源token';
/*
ALTER TABLE `order_items201410` change `ticket_template_id` `product_id` int(11) unsigned NOT NULL COMMENT '产品ID';
ALTER TABLE `order_items201411` change `ticket_template_id` `product_id` int(11) unsigned NOT NULL COMMENT '产品ID';
ALTER TABLE `order_items201412` change `ticket_template_id` `product_id` int(11) unsigned NOT NULL COMMENT '产品ID';
ALTER TABLE `order_items201501` change `ticket_template_id` `product_id` int(11) unsigned NOT NULL COMMENT '产品ID';
ALTER TABLE `order_items201502` change `ticket_template_id` `product_id` int(11) unsigned NOT NULL COMMENT '产品ID';

ALTER TABLE `order_items201410` CHANGE `base_num` `base_num_total` MEDIUMINT(8)  UNSIGNED  NOT NULL  DEFAULT '1'  COMMENT '单个产品包含门票可玩的总人数';
ALTER TABLE `order_items201411` CHANGE `base_num` `base_num_total` MEDIUMINT(8)  UNSIGNED  NOT NULL  DEFAULT '1'  COMMENT '单个产品包含门票可玩的总人数';
ALTER TABLE `order_items201412` CHANGE `base_num` `base_num_total` MEDIUMINT(8)  UNSIGNED  NOT NULL  DEFAULT '1'  COMMENT '单个产品包含门票可玩的总人数';
ALTER TABLE `order_items201501` CHANGE `base_num` `base_num_total` MEDIUMINT(8)  UNSIGNED  NOT NULL  DEFAULT '1'  COMMENT '单个产品包含门票可玩的总人数';
ALTER TABLE `order_items201502` CHANGE `base_num` `base_num_total` MEDIUMINT(8)  UNSIGNED  NOT NULL  DEFAULT '1'  COMMENT '单个产品包含门票可玩的总人数';

ALTER TABLE `order_items201410` drop `ticket_template_base_ids`;
ALTER TABLE `order_items201411` drop `ticket_template_base_ids`;
ALTER TABLE `order_items201412` drop `ticket_template_base_ids`;
ALTER TABLE `order_items201501` drop `ticket_template_base_ids`;
ALTER TABLE `order_items201502` drop `ticket_template_base_ids`;

alter table `order_items201410` change `payment` `payment` varchar(30) DEFAULT NULL COMMENT '支付渠道:cash,offline,credit,pos,alipay,advance,union,kuaiqian,taobao';
alter table `order_items201411` change `payment` `payment` varchar(30) DEFAULT NULL COMMENT '支付渠道:cash,offline,credit,pos,alipay,advance,union,kuaiqian,taobao';
alter table `order_items201412` change `payment` `payment` varchar(30) DEFAULT NULL COMMENT '支付渠道:cash,offline,credit,pos,alipay,advance,union,kuaiqian,taobao';
alter table `order_items201501` change `payment` `payment` varchar(30) DEFAULT NULL COMMENT '支付渠道:cash,offline,credit,pos,alipay,advance,union,kuaiqian,taobao';
alter table `order_items201502` change `payment` `payment` varchar(30) DEFAULT NULL COMMENT '支付渠道:cash,offline,credit,pos,alipay,advance,union,kuaiqian,taobao';

ALTER TABLE `orders201410` ADD `product_payment` VARCHAR(30) NULL DEFAULT '1,4' COMMENT '支付方式：1在线支付，2信用支付，3储值支付，4平台储值支付;
ALTER TABLE `orders201411` ADD `product_payment` VARCHAR(30) NULL DEFAULT '1,4' COMMENT '支付方式：1在线支付，2信用支付，3储值支付，4平台储值支付;
ALTER TABLE `orders201412` ADD `product_payment` VARCHAR(30) NULL DEFAULT '1,4' COMMENT '支付方式：1在线支付，2信用支付，3储值支付，4平台储值支付;
ALTER TABLE `orders201501` ADD `product_payment` VARCHAR(30) NULL DEFAULT '1,4' COMMENT '支付方式：1在线支付，2信用支付，3储值支付，4平台储值支付;
ALTER TABLE `orders201502` ADD `product_payment` VARCHAR(30) NULL DEFAULT '1,4' COMMENT '支付方式：1在线支付，2信用支付，3储值支付，4平台储值支付;

ALTER TABLE `orders201410` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `payed`;
ALTER TABLE `orders201411` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `payed`;
ALTER TABLE `orders201412` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `payed`;
ALTER TABLE `orders201501` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `payed`;
ALTER TABLE `orders201502` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `payed`;

ALTER TABLE `payments201410` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `amount`;
ALTER TABLE `payments201411` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `amount`;
ALTER TABLE `payments201412` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `amount`;
ALTER TABLE `payments201501` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `amount`;
ALTER TABLE `payments201502` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `amount`;

ALTER TABLE `payment_orders201410` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `money`;
ALTER TABLE `payment_orders201411` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `money`;
ALTER TABLE `payment_orders201412` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `money`;
ALTER TABLE `payment_orders201501` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `money`;
ALTER TABLE `payment_orders201502` ADD `activity_paid` DECIMAL(10,2)  NULL  DEFAULT '0.00'  COMMENT '抵用券金额'  AFTER `money`;

ALTER TABLE `tickets201410` ADD `landscape_id` int(10) unsigned NOT NULL COMMENT '景区id' after `ticket_template_id`;
ALTER TABLE `tickets201411` ADD `landscape_id` int(10) unsigned NOT NULL COMMENT '景区id' after `ticket_template_id`;
ALTER TABLE `tickets201412` ADD `landscape_id` int(10) unsigned NOT NULL COMMENT '景区id' after `ticket_template_id`;
ALTER TABLE `tickets201501` ADD `landscape_id` int(10) unsigned NOT NULL COMMENT '景区id' after `ticket_template_id`;
ALTER TABLE `tickets201502` ADD `landscape_id` int(10) unsigned NOT NULL COMMENT '景区id' after `ticket_template_id`;
*/










