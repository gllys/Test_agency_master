USE `ticket_order`;
UPDATE `order_items` a JOIN tickets b ON a.id = b.order_item_id SET b.`status` = 0 WHERE a.`status` = 0 AND b.status = 1;
ALTER TABLE `refund_apply` ADD `refund_items` text NOT NULL;