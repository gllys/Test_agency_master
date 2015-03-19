<?php
class Filter{
	  public static function html($value){
	      $value = self::maxLength($value) ;
		  $value = self::htmlSpecialChars($value) ;
		  //$value = self::addSlashes($value) ;
		  $value = self::cleanHex($value) ;
		  return $value ;
	  }
	  public static function htmls(Array &$values){
	       //  if(!is_array($values)) return false ;
	         foreach($values as $key=>$value){
			         if(is_array($value))self::htmls($values[$key]) ;
					 else $values[$key] = self::html($value);
			 }
	  }
	  protected static function addSlashes($value){
	         if (!get_magic_quotes_gpc()) {
                 return addslashes($value);
             }else { 
			     return $value ;
			 }
	  }
	  
	  protected static function htmlSpecialChars($value){
	         $value = str_replace('>','&gt;',$value) ;
			 $value = str_replace('<','&lt;',$value) ;
			 return $value  ;
	         //return htmlspecialchars($value)   ;
	  }
	  
	  protected static function cleanHex($value){	
	        return preg_replace("/\\[xX][A-Fa-f0-9]{1,3}/", "",$value);	
	  }
	  
	  protected static function maxLength($value){
	         return   mb_substr($value,0,5000);
	  }
}
/*
$arr = array('fsx'=>"fsx123456&<>");
Filter::htmls($arr);
print_r($arr);
*/
?>