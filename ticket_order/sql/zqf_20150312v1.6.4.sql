use `ticket_order`;

ALTER TABLE `orders` ADD `use_time` INT(10)  UNSIGNED  NULL  DEFAULT '0'  COMMENT '最后使用时间'  AFTER `deleted_at`;

ALTER TABLE `order_items` ADD `visitor_name` VARCHAR(30)  NULL  DEFAULT NULL  COMMENT '游客姓名',
 ADD `visitor_mobile` VARCHAR(20)  NULL  DEFAULT NULL  COMMENT '游客手机号',
 ADD `visitor_card` VARCHAR(20)  NULL  DEFAULT NULL  COMMENT '游客身份证';