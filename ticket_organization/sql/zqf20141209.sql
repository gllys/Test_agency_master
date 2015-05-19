USE `ticket_organization`;
ALTER TABLE `organizations` ADD `is_credit` TINYINT NOT NULL DEFAULT '1' COMMENT '信用支付 0:是 1：否' AFTER `is_distribute_group`;
ALTER TABLE `organizations` ADD `is_balance` TINYINT NOT NULL DEFAULT '1' COMMENT '储值支付 0:是 1：否' AFTER `is_credit`;

ALTER TABLE `credit_log` ADD `action_type` tinyint(1) unsigned DEFAULT '0' COMMENT '类型：0供应商调整，1支付，2退款' AFTER `id`;