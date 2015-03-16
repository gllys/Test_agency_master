ALTER TABLE `ticket_info`.`ticket_discount_rule` CHANGE `discount` `fat_discount` DECIMAL(10,2) DEFAULT 0.00 COMMENT '散客优惠减免',
	ADD COLUMN `group_discount` DECIMAL(10,2) DEFAULT 0.00 COMMENT '团客优惠减免' AFTER `fat_discount`;
