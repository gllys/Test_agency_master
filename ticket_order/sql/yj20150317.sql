ALTER TABLE `orders` ADD `code` BIGINT(20) UNSIGNED NOT NULL COMMENT '码号' ;
UPDATE `orders` SET `code` = `id` ;
ALTER TABLE `ticket_order`.`orders` ADD UNIQUE `code` (`code`);
