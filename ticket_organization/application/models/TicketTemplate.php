<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2014/11/26
 * Time: 19:34
 */

class TicketTemplateModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_info';
    protected $url = '/v1/tickettemplate/remaind';
    protected $method = 'POST';

    public function getRemaindTicket($organization_id)
    {
        if (!$organization_id)
            return false;
        $this->params = array('organization_id' => $organization_id);
        $ticketInfo = json_decode($this->request(), true);
        if (!$ticketInfo || empty($ticketInfo['body']))
            return false;
        return $ticketInfo['body'];
    }
}