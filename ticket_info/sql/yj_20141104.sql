ALTER TABLE `ticket_template` CHANGE `scenic_id` `scenic_id` VARCHAR(255) NOT NULL DEFAULT '0' COMMENT '景区ID，逗号分隔';
ALTER TABLE `ticket_template` ADD `is_union` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否联票';
