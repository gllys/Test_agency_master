ALTER TABLE `ticket_order`.`order_items201410`
ADD COLUMN `max_buy` INT(11) UNSIGNED NULL COMMENT '购买上限' AFTER `valid`,
ADD COLUMN `mini_buy` INT(11) UNSIGNED DEFAULT 1 NULL COMMENT '购买下限' AFTER `max_buy`;

ALTER TABLE `ticket_order`.`order_items201411`
ADD COLUMN `max_buy` INT(11) UNSIGNED NULL COMMENT '购买上限' AFTER `valid`,
ADD COLUMN `mini_buy` INT(11) UNSIGNED DEFAULT 1 NULL COMMENT '购买下限' AFTER `max_buy`;

ALTER TABLE `ticket_order`.`order_items201412`
ADD COLUMN `max_buy` INT(11) UNSIGNED NULL COMMENT '购买上限' AFTER `valid`,
ADD COLUMN `mini_buy` INT(11) UNSIGNED DEFAULT 1 NULL COMMENT '购买下限' AFTER `max_buy`;