insert into `language_config` (id,zh) values ('ERROR_CHECKTYPE_12','请选择是否景区角色');

alter table `organizations` add `supply_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否景区角色,0否1是' after `agency_type`;

alter table `credit_log`  change `credit_moeny` `credit_moeny` decimal(10,2) NOT NULL COMMENT '信用余额', change `balance_money` `balance_money` decimal(10,2) NOT NULL COMMENT '信用余额';