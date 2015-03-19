#新增用户角色
ALTER TABLE `users` ADD `sell_role` ENUM('scenic', 'whole')  NOT NULL  DEFAULT 'whole'  COMMENT '角色'  AFTER `name`;
