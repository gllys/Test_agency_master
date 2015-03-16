use `ticket_info`;
alter table `ticket_template` add `user_id` int(11) DEFAULT '0' COMMENT '操作人uid';
alter table `ticket_template` add `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人账号';
alter table `ticket_template` add `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人名称';