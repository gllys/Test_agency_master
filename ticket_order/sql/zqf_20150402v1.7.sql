use `ticket_order`;
ALTER TABLE `ticket_record` ADD `local_source` tinyint(1) NOT NULL DEFAULT '0' COMMENT '内部来源 0分销系统 1OPENAPI 2浙风自由行 3微信';
