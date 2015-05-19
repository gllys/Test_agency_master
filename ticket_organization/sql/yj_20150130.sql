ALTER TABLE `message` ADD `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否阅读';
UPDATE `message` set `is_read` = 1 where read_time>0;