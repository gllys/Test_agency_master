ALTER TABLE `message` CHANGE `sys_type` `sys_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0公告1订阅2机构3提醒4收藏5退款';
ALTER TABLE `message` CHANGE `send_source` `send_source` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发送来源0运营系统1供应系统2分销系统';
ALTER TABLE `message` ADD `send_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发送状态：0待发送 1已发送 2已驳回' AFTER `read_time`;
ALTER TABLE `message` ADD `receiver_organization_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '公告接收类型0单机构1全平台2全分销3全供应' AFTER `receiver_organization`;
ALTER TABLE `message` ADD `is_allow` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0待审核1已审核2驳回';
ALTER TABLE `message` ADD `remark` text NOT NULL;
ALTER TABLE `message` ADD `organization_name` varchar(100) NOT NULL COMMENT '机构名称';
ALTER TABLE `message` ADD `time_flag` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '标记时间';