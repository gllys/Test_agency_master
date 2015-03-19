<?php

class TestController extends Base_Controller_Api
{
    public function indexAction() {
        //
        $data = array("result"=>1);
        Lang_Msg::output($data);
    } 
}