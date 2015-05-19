use `ticket_scenic`;

insert into `language_config` (id,zh) values ('ERROR_LAND_ORG_6','该景区管理权限已分配给其他供应商，不能重复分配');
insert into `language_config` (id,zh) values ('ERROR_LANDSCAPE_4','已存在此名称的景区，景区名称不能重复');
insert into `language_config` (id,zh) values ('ERROR_POI_8','该景区下已存在此名称的景点');

alter table `landscape_organization` add `scenic_manage_right` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '景区管理权：1有0无' after `check_log_right`;