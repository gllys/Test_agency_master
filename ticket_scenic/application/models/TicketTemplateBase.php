<?php
/**
 * 票模板REST模型
 *
 * @Package Model
 * @Date 2015-4-10
 * @Author Joe
 */
class TicketTemplateBaseModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_info';
    protected $url = '/v1/Tickettemplatebase/';
    protected $method = 'POST';
    
    public function getTable() {
        return $this->tblname;
    }
    
    /**
     * 修改票模板的机构ID
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function updateOrganizationId($oldOrganizationId, $scenicId, $organizationId) {
        $this->url .= 'updateOrganizationId';
        $this->params = array(
            'organization_id'      => $oldOrganizationId,
            'scenic_id'            => $scenicId,
            'new_organization_id'  => $organizationId
        );
        $this->method = 'POST';
        $this->request();
    }
}