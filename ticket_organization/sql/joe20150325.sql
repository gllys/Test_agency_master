# alter table taobao_organization
# ------------------------------------------------------------
ALTER TABLE `taobao_organization` add COLUMN `ext` varchar(500) NOT NULL after account ;
ALTER TABLE `taobao_organization` add COLUMN `source` mediumint(5) NOT NULL after organization_id ;

update taobao_organization set ext='a:0:{}';
update taobao_organization set source=1;

CREATE TABLE `channel_organization` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `account` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '绑定渠道账号',
  `ext` varchar(500) COLLATE utf8_unicode_ci NOT NULL COMMENT '额外数据包(serialize)',
  `organization_id` int(20) NOT NULL DEFAULT '0' COMMENT '机构ID',
  `source` mediumint(5) NOT NULL COMMENT '来源ID(1淘宝2去哪儿)',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '账号审核状态：0未审核 1已审核',
  `created_by` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_org` (`account`,`organization_id`),
  KEY `source` (`source`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='绑定渠道账号表';

insert into channel_organization select * from taobao_organization;
drop table taobao_organization;