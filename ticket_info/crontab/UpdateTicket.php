<?php

require dirname(__FILE__) . '/Base.php';

class Crontab_UpdateTicket extends Process_Base 
{
    public function run() {
    	$m = TicketTemplateModel::model();
    	$sm = ScenicModel::model();
    	$dm = DistrictModel::model();
        $rows = $m->search(array('id|>'=>0));
        foreach($rows as $row) {
        	echo "deal {$row['id']}..";
        	$tmp = explode(',', $row['scenic_id']);
        	$scenic_id = $tmp[0];
        	$info = $sm->getScenicInfo(array('id'=>$scenic_id));
        	$info = $info['body'];
        	// echo var_export($info, true);
	        
            $save['province_id'] = $info['province_id'];
            $save['city_id'] = $info['city_id'];
            $save['district_id'] = $info['district_id'];
            // echo var_export($save, true);
            $m->updateById($row['id'],$save);

	        echo "ok\n";
        }
    }
}

$test = new Crontab_UpdateTicket;
