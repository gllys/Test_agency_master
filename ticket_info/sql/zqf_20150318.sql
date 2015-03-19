use `ticket_info`;
update ticket_template set ota_code=md5( CONCAT(id, organization_id,created_at) ) where ota_code='' or ota_code is null