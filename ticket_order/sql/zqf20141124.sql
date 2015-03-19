USE `ticket_order`;
insert  into `process_config`(`name`,`path`,`num`,`state`) values ('AgencyTkStat','AgencyTkStat.php',1,1);

INSERT INTO `ticket_order`.`language_config` (`id`, `zh`) VALUES ('INFO_GEN_BILL_0', '立即结算');
INSERT INTO `ticket_order`.`language_config` (`id`, `zh`) VALUES ('ERROR_GEN_BILL_0', '成功生成了{n}张结款单');
INSERT INTO `ticket_order`.`language_config` (`id`, `zh`) VALUES ('ERROR_GEN_BILL_1', '未生成任何结款单');
