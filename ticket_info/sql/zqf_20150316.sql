use `ticket_info`;

/* 修复销售价，挂牌价 */
update ticket_template a,(select id,sale_price,listed_price from ticket_template where sale_price>listed_price) b
set  a.sale_price=b.listed_price, a.listed_price=b.sale_price where a.id=b.id;