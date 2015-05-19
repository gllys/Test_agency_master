alter table `order_items201410` add `ticket_template_base_ids` text NOT NULL COMMENT '基础票ID，多个逗号隔开' after `ticket_template_id`;
alter table `order_items201411` add `ticket_template_base_ids` text NOT NULL COMMENT '基础票ID，多个逗号隔开' after `ticket_template_id`;
alter table `order_items201412` add `ticket_template_base_ids` text NOT NULL COMMENT '基础票ID，多个逗号隔开' after `ticket_template_id`;
alter table `order_items201501` add `ticket_template_base_ids` text NOT NULL COMMENT '基础票ID，多个逗号隔开' after `ticket_template_id`;



DROP TABLE IF EXISTS `process_config`;

CREATE TABLE `process_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(30) NOT NULL COMMENT '名称',
  `path` varchar(128) NOT NULL COMMENT '脚本',
  `num` tinyint(4) DEFAULT '0' COMMENT '数量 0等同CPU数量',
  `state` tinyint(4) DEFAULT '0' COMMENT '状态 0不运行 1运行',
  `run_type` tinyint(4) DEFAULT '0' COMMENT '0不限 1主服务器',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `process_config` WRITE;
/*!40000 ALTER TABLE `process_config` DISABLE KEYS */;

INSERT INTO `process_config` (`id`, `name`, `path`, `num`, `state`, `run_type`)
VALUES
	(2,'queue','Queue.php',1,1,0),
	(3,'share','Share.php',1,1,1),
	(4,'bill','Bill.php',1,1,1),
	(5,'AgencyTkStat','AgencyTkStat.php',1,1,1),
	(6,'Refund','Refund.php',1,1,1);

/*!40000 ALTER TABLE `process_config` ENABLE KEYS */;
UNLOCK TABLES;

