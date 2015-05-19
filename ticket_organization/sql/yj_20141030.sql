ALTER TABLE `supply_agency` ADD `credit_value` DECIMAL(20,2) NOT NULL DEFAULT '0' COMMENT '信用余额';
ALTER TABLE `supply_agency` ADD `store_value` DECIMAL(20,2) NOT NULL DEFAULT '0' COMMENT '信用余额';
ALTER TABLE `supply_agency` ADD `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间';
ALTER TABLE `supply_agency` ADD `is_del` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除';
ALTER TABLE `supply_agency` ADD `agency_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '分销商名字';


-- 供应商绑定分销商关联表
CREATE TABLE IF NOT EXISTS `supply_agency_history`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supply_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '供应商id',
  `agency_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分销商id',
  `agency_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '分销商名字',
  `is_bind` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否绑定',
  `unbind_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '解除时间',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '供应分销关联';