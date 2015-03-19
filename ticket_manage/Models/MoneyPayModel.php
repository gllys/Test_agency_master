<?php
/**
 *
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class MoneyPayModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'money_pay';
	public $pk         = 'id';


   public function getPayList($parm,$page){
   
      $filter["filter"]=array(
		   $this->table.".type" => 2,
		   $this->table.".deleted_at"    =>null
	   );
	   $filter["page"] =$page;
	   $filter["items"] =15;
       if($parm["updated_at"]){
		   $seller_time   = explode(" - ",$parm["updated_at"]);
			$startTime =trim($seller_time[0])." 00:00:00";;
			$endTime =trim($seller_time[1])." 23:59:59";;
	       $filter["filter"][$this->table.".created_at|between"]   = array($startTime,$endTime);
	   }

	   if(intval($parm["organization_id"])){
	        $filter["filter"][$this->table.'.organization_id']   = intval($parm["organization_id"]);
	   }

	   if($parm["organization_name"]){
	        $filter["filter"]['o.name|like']   = $parm["organization_name"];
		   
	   }

	   
	   if($parm["state"] !=""){
	        $filter["filter"][$this->table.'.state']   = $parm["state"];
	   }

       $organizationsModel       = $this->load->model('Organizations');
	   $filter["join"] = array(
	      array('left_join' => $organizationsModel->table.' o ON o.id='.$this->table.'.organization_id')
	  
	   );
      $filter["fields"]=$this->table.".*,o.name";
      $filter["order"]=$this->table.'.created_at desc';
      $list = $this->commonGetList($filter);
	  return $list;
   }

}