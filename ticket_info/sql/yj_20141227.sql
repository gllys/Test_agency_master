ALTER TABLE `ticket_template` ADD `is_infinite` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否无限0否1是';
ALTER TABLE `ticket_template_base` ADD `is_infinite` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否无限0否1是';
ALTER TABLE `ticket_template` ADD `ticket_template_base_ids` text NOT NULL COMMENT '基础票ID，多个逗号隔开';