USE `ticket_order`;
INSERT INTO `ticket_order`.`language_config` (`id`, `zh`) VALUES ('ERROR_CANCELCODE_6', '已无票可退');
INSERT INTO `ticket_order`.`language_config` (`id`, `zh`) VALUES ('ERROR_CANCELCODE_7', '退票数不能大于未使用票数');

ALTER TABLE `ticket_order`.`orders201410` ADD COLUMN `ota_type` VARCHAR(20) NOT NULL DEFAULT 'system' COMMENT '票来源' AFTER `price_type`;
ALTER TABLE `ticket_order`.`orders201411` ADD COLUMN `ota_type` VARCHAR(20) NOT NULL DEFAULT 'system' COMMENT '票来源' AFTER `price_type`;
ALTER TABLE `ticket_order`.`orders201412` ADD COLUMN `ota_type` VARCHAR(20) NOT NULL DEFAULT 'system' COMMENT '票来源' AFTER `price_type`;

UPDATE `ticket_order`.`language_config` SET `zh` = '已经结算' WHERE `id` = 'ERROR_GEN_BILL_1';