-- 2014-07-22
DROP TABLE IF EXISTS `user_suggest_report`;
CREATE TABLE IF NOT EXISTS `user_suggest_report`(
  `id` int NOT NULL auto_increment COMMENT '信息回复编号',
  `suggest_id` int NOT NULL COMMENT '意见编号',
  `content` varchar(512) COMMENT '正文',
  `organization_id` int NOT NULL COMMENT '机构',
  `user_id` int COMMENT '操作人ID',
  `user_account`  varchar(100) COMMENT '操作人',
  `user_name`  varchar(50) COMMENT '操作人名',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '记录删除时间',
  `last_updated_source` tinyint(4) NOT NULL DEFAULT '1' COMMENT '记录最后更新地',
  PRIMARY KEY (`id`),
  key suggest_id(suggest_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
