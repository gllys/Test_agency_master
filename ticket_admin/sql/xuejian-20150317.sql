#ticket_admin users�����Ƿ�ɾ���ֶ�
ALTER TABLE `users` Add `is_delete` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ�ɾ��' AFTER `identity`;
#ticket_admin role_user�����Ƿ�ɾ���ֶ�
ALTER TABLE `role_user` Add `is_delete` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ�ɾ��' AFTER `uid`; 