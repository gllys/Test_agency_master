use `ticket_order`;
ALTER TABLE `ticket_record` ADD `distributor_id` INT(11)  NULL  DEFAULT NULL  COMMENT '分销商ID'  AFTER `supplier_id`;
