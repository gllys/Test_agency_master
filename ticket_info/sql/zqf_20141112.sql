ALTER TABLE `ticket_info`.`ticket_rule_items` 
CHANGE `reserve` `reserve` INT(11) UNSIGNED DEFAULT 0 NULL COMMENT '库存规则', 
ADD COLUMN `used_reserve` INT(11) UNSIGNED DEFAULT 0 NULL COMMENT '已用库存' AFTER `reserve`; 
