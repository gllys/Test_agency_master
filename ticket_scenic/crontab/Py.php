<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2015/4/28
 * Time: 13:41
 */
require dirname(__FILE__) . '/Base.php';

class Crontab_Py extends Process_Base
{
    public function run() {
        $i = 1;
        $count = LandscapeModel::model()->search(array('py'=>'','name|exp'=>'IS NOT NULL'),'count(1) count');
        $count = reset($count);
        while($data = LandscapeModel::model()->search(array('py'=>'','name|exp'=>'IS NOT NULL'),'id,name',null,100)){
            foreach($data as $k=>$v){
//                var_dump($v['name']." ".Pinyin::utf8_to($v['name'],1));
                $py = trim(Pinyin::utf8_to($v['name'],1))?trim(Pinyin::utf8_to($v['name'],1)):'_';
                $pinyin = trim(Pinyin::utf8_to($v['name']))?trim(Pinyin::utf8_to($v['name'])):'_';
                LandscapeModel::model()->updateByAttr(array('pinyin'=>$pinyin,'py'=>$py),array('id'=>$v['id']));
                echo "\r".sprintf('%-.2f%%',round($i++/$count['count'],4)*100);
            }
        }
        echo "\n";
    }
}

$test = new Crontab_Py;