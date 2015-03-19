USE `ticket_order`;
ALTER TABLE `orders201410` CHANGE `distributor_name` `distributor_name` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT '';
ALTER TABLE `orders201410` CHANGE `supplier_name` `supplier_name` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT '';

ALTER TABLE `orders201411` CHANGE `distributor_name` `distributor_name` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT '';
ALTER TABLE `orders201411` CHANGE `supplier_name` `supplier_name` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT '';

ALTER TABLE `orders201412` CHANGE `distributor_name` `distributor_name` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT '';
ALTER TABLE `orders201412` CHANGE `supplier_name` `supplier_name` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT '';

ALTER TABLE `orders201501` CHANGE `distributor_name` `distributor_name` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT '';
ALTER TABLE `orders201501` CHANGE `supplier_name` `supplier_name` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT '';