UPDATE `ticket_order`.`config` SET `config_value` = '0' WHERE `config`.`config_key` = 'device_force';
UPDATE `ticket_order`.`config` SET `config_value` = '0' WHERE `config`.`config_key` = 'np_device_force';
update orders201411 set nums = 10000000 where id = 166129187502183;
update order_items201411 set nums = 10000000 where id = 166129187502183;