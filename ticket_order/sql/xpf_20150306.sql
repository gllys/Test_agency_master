alter table orders modify `landscape_ids` varchar(1024) NOT NULL DEFAULT '' COMMENT '景区id,多个逗号分隔';
alter table order_items modify `landscape_ids` varchar(1024) NOT NULL DEFAULT '' COMMENT '景区id,多个逗号分隔';
