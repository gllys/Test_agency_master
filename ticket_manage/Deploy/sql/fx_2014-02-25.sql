/*
 * add admin 、admin_role table
 * 
 * 后台添加 管理员表 和  管理员角色表，管理员表默认超级用户为admin，密码为2014
 */


/* admin */
CREATE TABLE `admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `salt` varchar(64) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `gender` enum('male','female') NOT NULL DEFAULT 'male',
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(100) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `identity` varchar(100) DEFAULT NULL,
  `role_id` int(10) unsigned DEFAULT NULL COMMENT '角色id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0-弃用，1-启用',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_super` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否超级管理员 0：否 1：是',
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_account_unique` (`account`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='后台用户';

INSERT INTO `admin` (`id`, `account`, `password`, `salt`, `name`, `gender`, `email`, `mobile`,`birthday`, `identity`, `status`, `created_by`, `created_at`, `updated_at`, `is_super`)
VALUES (1,'admin','c754c7ade5bb24bc5cfa623af8c687f904f5440f', 'e7cce229aa523306fee888a14a1115f4', '超级管理员', 'male','admin@ihuilian.com','18621527275', '1987-08-18','870105198708182098', 1, 1,'2014-02-25 17:21:31', '2014-02-25 18:32:44', '1');

/* admin_role */
CREATE TABLE `admin_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '角色名',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '角色说明',
  `permissions` text NOT NULL COMMENT '拥有权限',
  `disabled` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='后台角色';