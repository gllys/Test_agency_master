#账号机构增加景区绑定
ALTER TABLE `users` ADD `landscape_id` INT NOT NULL DEFAULT '0' AFTER `organization_id`;
ALTER TABLE `users` ADD `password_str` VARCHAR(100) NOT NULL DEFAULT '' AFTER `password`;