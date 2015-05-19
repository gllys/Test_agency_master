<?php

require dirname(__FILE__) . '/Base.php';

class Crontab_UpdateScenic extends Process_Base 
{
    public function run() {
    	$lm = LandscapeModel::model();
    	$lim = LandscapeInfoModel::model();
        $rows = $lm->search(array('id|>'=>0));
        foreach($rows as $row) {
        	echo "deal {$row['id']}..";
        	$info = $lim->getInfo($row['id']);
        	// echo var_export($info, true);
	        $district_id = $info['district_id'];
	        $province_id = intval($district_id/10000) * 10000;
        	$city_id = intval($district_id/100) * 100;

            $save['province_id'] = $province_id;
            $save['city_id'] = $city_id;
            $save['district_id'] = $district_id;
            // echo var_export($save, true);
            $lm->updateById($row['id'],$save);

	        echo "ok\n";
        }
    }
}

$test = new Crontab_UpdateScenic;
