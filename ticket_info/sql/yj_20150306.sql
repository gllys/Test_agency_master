ALTER TABLE `ticket_template` ADD `force_out` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否强制下架' ;
ALTER TABLE `ticket_template` ADD `force_out_remark` text NOT NULL COMMENT '强制下架理由';