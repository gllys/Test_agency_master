<?php
class LandscapeModel extends Base_Model_Api
{
    protected $srvKey = 'ticket_scenic';
    protected $url = '/v1/landscape/detail';
    protected $method = 'POST';

    public function getDetail($id){
        if(!$id)
            return array();
        $this->params = array('id'=>$id);
        $info = $this->request();
        $info = json_decode($info, true);
        if(!$info || empty($info['body']))
            return false;
        return $info['body'];
    }
}