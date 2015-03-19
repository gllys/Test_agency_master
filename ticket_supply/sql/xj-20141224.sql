#批发商-供应商，绑定景区可拥有景区只读权限或管理权限
ALTER TABLE `users` ADD `msg_rdy` ENUM('manage','ready') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'ready' COMMENT '管理，自读权限' AFTER `sell_role`;