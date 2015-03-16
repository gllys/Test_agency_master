<?php
class TicketTemplateModel extends Base_Model_Abstract
{
 	protected $dbname = 'itourism';
    protected $tblname = 'ticket_template';
    protected $pkKey = 'id';
   // protected $preCacheKey = 'cache|TicketTemplateModel|';
    protected $preCacheKey = '';

    public function getTable() {
        return $this->tblname;
    }

    public function addNew($data,$baseItems){
        if(!$data || !$baseItems) return false;
        $base_ids = array_keys($baseItems);
        $baseLists = TicketTemplateBaseModel::model()->getByIds($base_ids);
        if(!$baseLists) return false;
        $scenic_ids = $view_points = $baseOrgIds = array();
//        $data['sale_price'] = 0;
        foreach($baseLists as $k=>$v){
            $baseLists[$k]['num'] = $baseItems[$v['id']];
            $scenic_ids[] = $v['scenic_id'];
            $view_points[] = $v['view_point'];
            $baseOrgIds[] = $v['organization_id'];
//            $data['sale_price']+= $v['sale_price']*$baseItems[$v['id']];
        }
        $scenic_ids = array_unique($scenic_ids);
        $view_points = array_unique(explode(',',implode(',',$view_points)));
        $baseOrgIds = array_unique($baseOrgIds);
        $now = time();
        $data['scenic_id'] = implode(',',$scenic_ids);
        $data['view_point'] = implode(',',$view_points);
        $data['base_org_num'] = count($baseOrgIds);
//        $data['listed_price'] = $data['fat_price'];
        $data['ota_code'] = Util_Common::genTicketCode(microtime(true));
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        if(1 < count($scenic_ids)) //2个或2个以上景区为联票
            $data['is_union']=1;

        $this->begin();
        $res = $this->add($data);
        if($res) {
            $data['id'] = $this->getInsertId();
            $r = TicketTemplateItemModel::model()->addList($data,$baseLists);
            if($r) {
                $this->commit();
                return $data;
            }
        }
        $this->rollback();
        return false;
    }
    
    public function checkList( $id, $key ,$return )
    {
     	$t = array();
		//例外
		if( $return[ $key.'_list'] )
		{  
			$t = explode( ',', $return[ $key.'_list'] );
			
			if( in_array( $id , $t ) )
			{
				return  $return[ $key ] == 0 ? true : false;
			}
			else
			{
				return $return[ $key ] == 0 ? false : true;
			}
		}
		else
		{
			return $return[ $key ] == 0 ? false : true;
		}		
		
    }
}