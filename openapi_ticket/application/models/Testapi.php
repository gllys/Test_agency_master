<?php

/**
 * Class TestapiModel
 */
class TestapiModel extends Base_Model_Api
{
	protected $srvKey = 'ticket_info';
    protected $url = '/v1/TicketTemplate/ticketinfo';
    protected $method = 'GET';
    
    public function getList() {
    	$this->params = array();
        return $this->request();
    }
}
