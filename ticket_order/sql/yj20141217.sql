ALTER TABLE `transaction_flow` ADD `user_name` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '用户名';
ALTER TABLE `transaction_flow` ADD `balance` DECIMAL(20,2) NOT NULL DEFAULT '0' COMMENT '余额';
ALTER TABLE `transaction_flow` ADD `remark` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注';