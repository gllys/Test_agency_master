<?php
class debug{

	public static function dump($data,$exit = 0){
		echo '<pre>';
        var_dump($data);
        if($exit){
            Yii::app()->end();
        }
        echo '</pre><br/>';
	}
}
?>