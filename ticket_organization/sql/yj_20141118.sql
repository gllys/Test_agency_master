-- 消息表
CREATE TABLE IF NOT EXISTS `message`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `sms_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0系统1订单',
  `sys_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0默认全局1订阅2机构3提醒4收藏',
  `send_source` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发送来源0默认1机构2后台3个人',
  `send_user` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送人',
  `send_organization` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送机构',
  `send_backend` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送后台',
  `receiver_organization` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '接收机构',
  `op_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户id',
  `read_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '阅读时间',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '消息';