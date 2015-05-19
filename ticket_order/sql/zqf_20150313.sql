use `ticket_order`;

/* 修复销售价，挂牌价 */
update orders a,(select id,sale_price,listed_price from orders where sale_price>listed_price) b
set  a.sale_price=b.listed_price, a.listed_price=b.sale_price where a.id=b.id;

update order_items a,(select id,sale_price,listed_price from order_items where sale_price>listed_price) b
set  a.sale_price=b.listed_price, a.listed_price=b.sale_price where a.id=b.id;





