ALTER TABLE `orders` ADD `checked_open` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT "是否核销";
ALTER TABLE `orders` ADD `message_open` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT "是否发短信";