<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2014/12/18
 * Time: 14:34
 */
class TicketTemplateBaseModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_template_base';
    protected $pkKey = 'id';
    // protected $preCacheKey = 'cache|TicketTemplateModel|';
    protected $preCacheKey = '';

    public function getTable() {
        return $this->tblname;
    }

    //检查门票是否有上架产品
    public function haveProduct($ids){
        if(!$ids) return false;
        $ticketTemplateItem = TicketTemplateItemModel::model()->search(array('base_id|in'=>$ids,'deleted_at'=>0));
        $product_id = array();
        if($ticketTemplateItem){
            foreach($ticketTemplateItem as $k=>$v){
                $product_id[] = $v['product_id'];
            }
            if($product_id){
                $products = TicketTemplateModel::model()->search(array('id|in'=>$product_id,'state'=>1));
                return $products;
            }
            else return false;
        }
        else return false;
    }
}