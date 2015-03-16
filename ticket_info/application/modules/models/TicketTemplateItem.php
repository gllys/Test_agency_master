<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-1-19
 * Time: 下午6:49
 */

class TicketTemplateItemModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'ticket_template_items';
    protected $pkKey = '';
    // protected $preCacheKey = 'cache|TicketTemplateItemsModel|';
    protected $preCacheKey = '';

    public function getTable() {
        return $this->tblname;
    }

    public function addList($productInfo,$baseLists){
        if(!$productInfo || !$baseLists) return false;
        $scenicIds = explode(',',$productInfo['scenic_id']);
        $scenicLists = ScenicModel::model()->getScenicList(array('ids'=>$productInfo['scenic_id'],'items'=>count($scenicIds)));
        if(empty($scenicLists['body']['data'])) return false;
        $scenicLists = $scenicLists['body']['data'];
        $scenicNames = array();
        foreach($scenicLists as $v){
            $scenicNames[$v['id']] = $v;
        }
        $data = array();
        $now = time();
        $this->delete(array('product_id'=>$productInfo['id']));
        foreach($baseLists as $v) {
            $tmp = array(
                'product_id'=>$productInfo['id'],
                'base_id'=> $v['id'],
                'base_org_id'=> $v['organization_id'],
                'scenic_id'=> $v['scenic_id'],
                'sceinc_name'=> $scenicNames[$v['scenic_id']]['name'],
                'view_point'=> $v['view_point'],
                'base_name'=> $v['name'],
                'type'=> $v['type'],
                'sale_price'=> $v['sale_price'],
                'num'=> $v['num'],
                'province_id'=> $scenicNames[$v['scenic_id']]['province_id'],
                'city_id'=> $scenicNames[$v['scenic_id']]['city_id'],
                'district_id'=> $scenicNames[$v['scenic_id']]['district_id'],
                'created_at'=> $now,
                'updated_at'=> $now,
            );
            $data[] = $tmp;
        }

        array_unshift($data,array_keys($data[0]));
        $r = $this->add($data);
        return $r?true:false;
    }


}