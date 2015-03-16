ALTER TABLE `ticket_info`.`ticket_template` DROP COLUMN `fit_platform`,
  DROP COLUMN `fit_platform_list`,
  DROP COLUMN `full_platform`,
  DROP COLUMN `full_platform_list`;


ALTER TABLE `ticket_info`.`ticket_template`
  ADD COLUMN `namelist_id` BIGINT(20) UNSIGNED DEFAULT 0 NOT NULL COMMENT '限制清单ID' AFTER `rule_id`,
  ADD COLUMN `discount_id` BIGINT(20) UNSIGNED DEFAULT 0 NOT NULL COMMENT '优惠规则ID' AFTER `namelist_id`;

