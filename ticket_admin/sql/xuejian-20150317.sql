#ticket_admin users增加是否删除字段
ALTER TABLE `users` Add `is_delete` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否删除' AFTER `identity`;
#ticket_admin role_user增加是否删除字段
ALTER TABLE `role_user` Add `is_delete` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否删除' AFTER `uid`; 