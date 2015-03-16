<?php
/**
    ߣ<fsxyly@qq.com>
    ַ: www.xihazhijia.com
    Ȩ֮
    web1.0
ʱ    䣺2012035
޸ʱ䣺
**/
class Model
{
      private static $app ;
      private function __construct(){
	          //Yii::app()->db->createCommand('SET NAMES utf8')->execute() ;
	  }
	  
      public static function app(){
	         if(empty(self::$app)){
			    self::$app = new self() ;
			 }
			 return self::$app ;
	  }
      
	  //ִnoSQLɹtrueʧܷfalse
	  public function execute($sql,$params=array()){
	         try{
	               Yii::app()->db->createCommand($sql)->execute($params) ;
				   return true ;
		     }catch(Exception $e){
			       return false ;
		     }
	 }
	
	 //ִnoSQLɹIDʧܷfalse
	 public function executeID($sql,$params=array()){
	        $connection = Yii::app()->db;
	        try{
	               $connection->createCommand($sql)->execute($params) ;
				   return $connection->lastInsertID ;
			}catch(Exception $e){
			       return false ;
			}
	 }
	 //ִsql䣬ȡѯ reader 
	 public function queryObject($sql,$params=array()){
	       return Yii::app()->db->createCommand($sql)->query($params) ;
	 }
	
	 //ִsql䣬ȡݣ
	 public function queryAll($sql,$params=array()){
	        return Yii::app()->db->createCommand($sql)->queryAll(true,$params) ;
	 }
	
	 //ִsql䣬ȡһֵ
	 public function queryRow($sql,$params=array()){
	        return Yii::app()->db->createCommand($sql)->queryRow(true,$params) ;
	 }
	
	 //ִsql䣬ȡһֵ
	 public function queryScalar($params=array()){
	        return Yii::app()->db->createCommand($sql)->queryScalar($params) ;
	 }
	
	 //ִsql䣬ȡһеһֵֶ
	 public function queryColumn($sql,$params=array()){
	        return Yii::app()->db->createCommand($sql)->queryColumn($params) ;
	 }
	 
	 //返回以唯一键的数组结构
	 public function queryAllUniqueKey($sql,$key,$params=array()){
	        $model = Yii::app()->db->createCommand($sql)->queryAll(true,$params) ;
			$_model = array() ;
			if($model){
			   foreach($model as $val){
			        $_model[$val[$key]] = $val ; 
			   }
			}
			return $_model ;
	 }
	 
	  //返回以唯一键的多维数组结构
	 public function queryAllUniqueKeys($sql,$key,$params=array()){
	        $model = Yii::app()->db->createCommand($sql)->queryAll(true,$params) ;
			$_model = array() ;
			if($model){
			   foreach($model as $val){
			        $_model[$val[$key]][] = $val ; 
			   }
			}
			return $_model ;
	 }
}
?>