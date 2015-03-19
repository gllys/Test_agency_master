UPDATE `ticket_order`.`config` SET `config_value` = 'http://mobile1-test.b0.upaiyun.com/android/Checkin/Checkin_Distributor_V5.apk' WHERE `config`.`config_key` = 'device_url';
UPDATE `ticket_order`.`config` SET `config_value` = '5' WHERE `config`.`config_key` = 'device_version';
INSERT INTO `ticket_order`.`config` (`config_key`, `config_value`) VALUES ('np_device_force', '1'), ('np_device_version', '1');
INSERT INTO `ticket_order`.`config` (`config_key`, `config_value`) VALUES ('np_device_url', 'http://mobile1-test.b0.upaiyun.com/android/Checkin/Checkin_WuYi_V1.apk');