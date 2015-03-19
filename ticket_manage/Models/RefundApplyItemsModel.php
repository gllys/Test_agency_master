<?php
/**
 * 
 * 2014-1-14
 *
 * @author  cyl
 * @version 1.0
 */
class RefundApplyItemsModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'refund_apply_items';
	public $pk         = 'id';


	//生成票
	public function addRefundApplyItems($tickets, $refundApplyId)
	{
		if($tickets){
			$insertSql = 'INSERT INTO '.$this->table.' (`refund_apply_id`,`ticket_id`)';
			$addItmes  = array();
			foreach($tickets as $value){
				$addItmes[] = "('{$refundApplyId}','{$value}')";
			}
			$insertSql .= 'VALUES '.implode(',', $addItmes);
			$result = $this->query($insertSql);
			$affectedRows   = $this->affectedRows();
			if($result && $affectedRows >= 1){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}