USE `ticket_order`;
alter table `bankcard_account` add `open_bank`  varchar(100) Null COMMENT '开户行' after `bank_name`;