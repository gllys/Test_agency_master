ALTER TABLE `ticket_order`.`order_items201410` CHANGE `landscape_id` `landscape_ids` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '景区id,多个逗号分隔';
ALTER TABLE `ticket_order`.`order_items201411` CHANGE `landscape_id` `landscape_ids` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '景区id,多个逗号分隔';
ALTER TABLE `ticket_order`.`order_items201412` CHANGE `landscape_id` `landscape_ids` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '景区id,多个逗号分隔';

ALTER TABLE `ticket_order`.`orders201410` CHANGE `landscape_id` `landscape_ids` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '景区id,多个逗号分隔';
ALTER TABLE `ticket_order`.`orders201411` CHANGE `landscape_id` `landscape_ids` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '景区id,多个逗号分隔';
ALTER TABLE `ticket_order`.`orders201412` CHANGE `landscape_id` `landscape_ids` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '景区id,多个逗号分隔';

ALTER TABLE `ticket_order`.`orders201410` ADD COLUMN `kind` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL COMMENT '种类:1单票2联票3套票' AFTER `id`;
ALTER TABLE `ticket_order`.`orders201411` ADD COLUMN `kind` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL COMMENT '种类:1单票2联票3套票' AFTER `id`;
ALTER TABLE `ticket_order`.`orders201412` ADD COLUMN `kind` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL COMMENT '种类:1单票2联票3套票' AFTER `id`;

ALTER TABLE `ticket_order`.`order_items201410` ADD COLUMN `kind` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL COMMENT '种类:1单票2联票3套票' AFTER `id`;
ALTER TABLE `ticket_order`.`order_items201411` ADD COLUMN `kind` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL COMMENT '种类:1单票2联票3套票' AFTER `id`;
ALTER TABLE `ticket_order`.`order_items201412` ADD COLUMN `kind` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL COMMENT '种类:1单票2联票3套票' AFTER `id`;