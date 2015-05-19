USE `ticket_organization`;
ALTER TABLE `organizations` ADD `supply_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否景区角色,0否1是';