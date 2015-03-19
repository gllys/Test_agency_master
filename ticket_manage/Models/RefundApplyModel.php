<?php
/**
 * 
 * 2014-1-14
 *
 * @author  cyl
 * @version 1.0
 */
class RefundApplyModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'refund_apply';
	public $pk         = 'id';

	/**
	 * 得到唯一的id
	 * @return string id
	 */
	public function genId()
	{
	    $i = rand(0,9999);
	    do {
	        if(9999 == $i){
	            $i = 0;
	        }
	        $i++;
	        $id    = date('YmdHi').str_pad($i, 4, '0', STR_PAD_LEFT).$_SERVER['FX_REMOTE'];
	        $exist = $this->getID($id);
	    } while($exist);
	    return $id;
	}
}