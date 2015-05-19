<?php
class SendmsgModel extends Base_Model_Api
{ 
	protected $srvKey = 'ticket_organization';
    protected $url = '/v1/message/add';
    protected $method = 'POST';
	
    public function send( $content, $sms_type, $sys_type , $receiver_organization ,$or_id)
    {	
    	 $this->params = array(
             'send_source'=>1,
             'send_status'=>1,
             'content'=>$content,
             'sms_type' => $sms_type,
             'sys_type' => $sys_type,
             'send_organization'=> $or_id,
             'receiver_organization' => $receiver_organization
         );
        $msg = json_decode($this->request(),true);
    }
}