alter table poi add `level_id`   int(10)   DEFAULT 0 COMMENT '景区级别',
				add `created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
				add `updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间';

alter table poi_last_edit add `level_id`   int(10)   DEFAULT 0 COMMENT '景区级别';