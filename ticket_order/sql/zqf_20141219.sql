use `ticket_order`;
alter table `orders` add `user_id` int(11) DEFAULT '0' COMMENT '操作人uid';
alter table `orders` add `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人账号';
alter table `orders` add `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人名称';

alter table `orders201410` add `user_id` int(11) DEFAULT '0' COMMENT '操作人uid';
alter table `orders201410` add `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人账号';
alter table `orders201410` add `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人名称';

alter table `orders201411` add `user_id` int(11) DEFAULT '0' COMMENT '操作人uid';
alter table `orders201411` add `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人账号';
alter table `orders201411` add `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人名称';

alter table `orders201412` add `user_id` int(11) DEFAULT '0' COMMENT '操作人uid';
alter table `orders201412` add `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人账号';
alter table `orders201412` add `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人名称';

alter table `orders201501` add `user_id` int(11) DEFAULT '0' COMMENT '操作人uid';
alter table `orders201501` add `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人账号';
alter table `orders201501` add `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人名称';


alter table `order_items201410` add `user_id` int(11) DEFAULT '0' COMMENT '操作人uid';
alter table `order_items201410` add `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人账号';
alter table `order_items201410` add `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人名称';

alter table `order_items201411` add `user_id` int(11) DEFAULT '0' COMMENT '操作人uid';
alter table `order_items201411` add `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人账号';
alter table `order_items201411` add `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人名称';

alter table `order_items201412` add `user_id` int(11) DEFAULT '0' COMMENT '操作人uid';
alter table `order_items201412` add `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人账号';
alter table `order_items201412` add `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人名称';

alter table `order_items201501` add `user_id` int(11) DEFAULT '0' COMMENT '操作人uid';
alter table `order_items201501` add `user_account` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人账号';
alter table `order_items201501` add `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作人名称';