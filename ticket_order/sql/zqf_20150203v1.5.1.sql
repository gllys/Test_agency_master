use `ticket_order`;

INSERT INTO `language_config` (id,zh) VALUES ('ERROR_ORDER_18', '该订单已驳回');
INSERT INTO `language_config` (id,zh) VALUES ('ERROR_ORDER_19', '缺少驳回理由');

alter table `orders` add `reason` text COMMENT '理由';