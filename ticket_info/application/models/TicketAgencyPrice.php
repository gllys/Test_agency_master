<?php
class TicketAgencyPriceModel extends Base_Model_Abstract
{
 	protected $dbname = 'itourism';
    protected $tblname = 'ticket_agency_price';
    protected $pkKey = 'id';
    protected $preCacheKey = 'cache|TicketAgencyPriceModel|';

    public function getTable() {
        return $this->tblname;
    }
	
 //更改分销售商价格
    public function updatefx( $args )
    {
    	try 
    	{	
    		$this->begin();
    		foreach( $args as $value )
    		{  	
    			//判断是否有记录，有就更新，没有就插入
    			$where =  'organization_id = '.$value[ 'organization_id' ].' and  city_id ='. $value[ 'city_id' ]. ' and agency_id = '. $value[ 'agency_id' ].' and ticket_id = '. $value[ 'ticket_id'];
    			$data  = array('fit_price' => $value[ 'fit_price' ], 'full_price' => $value[ 'full_price'] );
    			$have = $this->search( $where  );
    			if( $have )
    			{		
    	    			$this->update( $data , $where );
    			}
    			else
    			{  
    				$data[ 'city_id' ] = $value[ 'city_id' ];
    				$data[ 'agency_id' ] = $value[ 'agency_id' ];
    				$data[ 'ticket_id' ] = $value[ 'ticket_id' ];
    				$data[ 'organization_id' ] = $value[ 'organization_id' ];
    				$this->insert($data );
    			}
    		}
    		$this->commit();
    		Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
    	}
    	catch ( PDOException $err )
    	{
    		$this->rollback();
    		Lang_Msg::error('ERROR_OPERATE_1');
    	}
    	
    }
}