use `ticket_info`;

ALTER TABLE `agency_product` 
ADD COLUMN `listed_price2`  decimal(10,2) NOT NULL COMMENT '自定义挂牌价' AFTER `product_name`,
ADD COLUMN `pass_type`  tinyint(3) NOT NULL COMMENT '入园方式' AFTER `code`,
ADD COLUMN `pass_address`  varchar(500) NOT NULL COMMENT '入园地址' AFTER `pass_type`,
ADD COLUMN `detail`  text NOT NULL COMMENT '产品描述' AFTER `pass_address`,
ADD COLUMN `description`  text NOT NULL COMMENT '使用说明' AFTER `detail`,
ADD COLUMN `consumption_detail`  text NOT NULL COMMENT '费用说明' AFTER `description`,
ADD COLUMN `refund_detail`  text NOT NULL COMMENT '退款说明' AFTER `consumption_detail`,
ADD COLUMN `settle_payment`  varchar(50) NOT NULL COMMENT '结算方式' AFTER `refund_detail`,
ADD COLUMN `extra`  text NOT NULL COMMENT 'serialize打包字段' AFTER `payment_list`;

update agency_product set extra='a:0:{}';