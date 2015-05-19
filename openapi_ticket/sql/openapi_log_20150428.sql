use openapi_log;

alter table log_common add column category varchar(100) default '' comment '分类';
alter table log_common add column server_ip varchar(60) default '' comment '服务器ip';
alter table log_common add column `level` varchar(100) default '' comment '日志等级';
alter table log_common add index (created_date);
alter table log_common add index (category);
alter table log_common comment '通用';
alter table log_common add column `search_val` varchar(100) default '' comment '要进行搜索的值';

alter table log_qunar add column category varchar(100) default '' comment '分类';
alter table log_qunar add column server_ip varchar(60) default '' comment '服务器ip';
alter table log_qunar add column `level` varchar(100) default '' comment '日志等级';
alter table log_qunar add index (created_date);
alter table log_qunar add index (category);
alter table log_qunar comment '去哪儿';
alter table log_qunar add column `search_val` varchar(100) default '' comment '要进行搜索的值';

alter table log_taobao add column category varchar(100) default '' comment '分类';
alter table log_taobao add column server_ip varchar(60) default '' comment '服务器ip';
alter table log_taobao add column `level` varchar(100) default '' comment '日志等级';
alter table log_taobao add index (created_date);
alter table log_taobao add index (category);
alter table log_taobao comment '淘宝';
alter table log_taobao add column `search_val` varchar(100) default '' comment '要进行搜索的值';

alter table log_way add column category varchar(100) default '' comment '分类';
alter table log_way add column server_ip varchar(60) default '' comment '服务器ip';
alter table log_way add column `level` varchar(100) default '' comment '日志等级';
alter table log_way add index (created_date);
alter table log_way add index (category);
alter table log_way comment '淘在路上';
alter table log_way add column `search_val` varchar(100) default '' comment '要进行搜索的值';