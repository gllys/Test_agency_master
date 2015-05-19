<?php

class TestController extends Base_Controller_Api
{
    public function indexAction() {
        //
      	//$header = array();
        //$header[ 'pwd' ] = 'itourism-distribution-api' . ':' . 'itourism-distribution-api';
      	//$url ='http://itourism-api.api.jinglvtong.com/advanced/landscapes?filter=key:equal_8e141dacac2ce9c6160d90e90c60991a';
      	//$tmp = Tools::curl($url,"", "", $header );
      	//print_r( $tmp );
      	$sms = new Sms();
      	$sms->sendSMS( 15026707499, 'testssss');
    } 
    
    
}