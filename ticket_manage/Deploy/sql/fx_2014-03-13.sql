
/************** 新增设备管理数据表 ****************/
CREATE TABLE `equipment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('gate','andriod') DEFAULT 'gate' COMMENT '设备类型：闸机、手持',
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '设备编号',
  `name` varchar(100) DEFAULT '' COMMENT '名称（预留）',
  `landscape_id` int(11) DEFAULT '0' COMMENT '景区id',
  `poi_id` int(11) DEFAULT '0' COMMENT '子景点id',
  `create_by` int(11) DEFAULT '0' COMMENT '添加人员（admin_id）',
  `update_by` int(11) DEFAULT '0' COMMENT '修改人员',
  `update_from` enum('admin','users') DEFAULT 'admin' COMMENT '修改人员来源（后台、前台）',
  `created_at` int(11) DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;