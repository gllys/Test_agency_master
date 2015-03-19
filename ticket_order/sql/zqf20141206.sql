USE `ticket_order`;
RENAME TABLE `ticket_group` TO `ticket_code`;
ALTER TABLE `ticket_code` CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  COMMENT '票模版票码';
