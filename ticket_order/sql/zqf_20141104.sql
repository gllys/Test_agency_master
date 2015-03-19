ALTER TABLE `ticket_order`.`tickets201410`
  ADD COLUMN `poi_list` VARCHAR(500) NOT NULL COMMENT '景点id,多个逗号隔开' AFTER `ticket_template_id`,
  ADD COLUMN `poi_num` TINYINT(3) UNSIGNED DEFAULT 1 NOT NULL COMMENT '景点数' AFTER `poi_list`,
  ADD COLUMN `poi_used` VARCHAR(500) NULL COMMENT '已游玩景点，多个逗号隔开' AFTER `poi_num`,
  ADD COLUMN `poi_used_num` TINYINT(3) UNSIGNED DEFAULT 0 NULL COMMENT '已游玩景点数' AFTER `poi_used`;

  ALTER TABLE `ticket_order`.`tickets201411`
  ADD COLUMN `poi_list` VARCHAR(500) NOT NULL COMMENT '景点id,多个逗号隔开' AFTER `ticket_template_id`,
  ADD COLUMN `poi_num` TINYINT(3) UNSIGNED DEFAULT 1 NOT NULL COMMENT '景点数' AFTER `poi_list`,
  ADD COLUMN `poi_used` VARCHAR(500) NULL COMMENT '已游玩景点，多个逗号隔开' AFTER `poi_num`,
  ADD COLUMN `poi_used_num` TINYINT(3) UNSIGNED DEFAULT 0 NULL COMMENT '已游玩景点数' AFTER `poi_used`;

  ALTER TABLE `ticket_order`.`tickets201412`
  ADD COLUMN `poi_list` VARCHAR(500) NOT NULL COMMENT '景点id,多个逗号隔开' AFTER `ticket_template_id`,
  ADD COLUMN `poi_num` TINYINT(3) UNSIGNED DEFAULT 1 NOT NULL COMMENT '景点数' AFTER `poi_list`,
  ADD COLUMN `poi_used` VARCHAR(500) NULL COMMENT '已游玩景点，多个逗号隔开' AFTER `poi_num`,
  ADD COLUMN `poi_used_num` TINYINT(3) UNSIGNED DEFAULT 0 NULL COMMENT '已游玩景点数' AFTER `poi_used`;