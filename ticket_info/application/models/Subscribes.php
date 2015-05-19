<?php
class SubscribesModel extends Base_Model_Abstract
{
 	protected $dbname = 'itourism';
    protected $tblname = 'subscribes';
    protected $pkKey = 'id';
    //protected $preCacheKey = 'cache|subscribesModel|';
    protected $preCacheKey ='';
    
    public function getTable() {
        return $this->tblname;
    }
    
    public function sendMsg( $id, $or_id, $args, $record)
    {
    	/*$type = 0;
    	if( $args[ 'fat_price' ] ) $type =1;
    	$return  = SubscribesModel::model()->search( array( 'ticket_id' => $id , 'type' => $type ) );
    	if( !$return ) return;
    	$update = array();
    	if( isset( $args[ 'fat_price' ])) $update[ 'fat_price' ] = $args[ 'fat_price' ];
    	if( isset( $args[ 'group_price' ])) $update[ 'group_price' ] = $args[ 'group_price' ];
    	//send msg
    	foreach( $return as $v )
    	{  
	    	if( $type == 1 )
	    	{    
	    		$content='您订阅的<'. $record[ "name" ].'><散客价>，由<'. $record[ 'fat_price' ].'>变动到<'.$args[ 'fat_price' ].'>，请注意查看！';
	    	
	    	}
	    	else
	    	{
	    		$content='您订阅的<'. $record[ "name" ].'><团客价>，由<'. $record[ 'group_price' ].'>变动到<'.$args[ 'group_price' ].'>，请注意查看！';
	    	}
	    	SendmsgModel::model()->send( $content, 0,1 , $v[ 'organization_id' ] );
    	}
    	if( SubscribesModel::model()->updateByAttr( $update, array( 'ticket_id' => $id , 'type' =>  $type ) ) )
    	{  
    		 return ;
    	}
    	else
    	{
    		Lang_Msg::error( 'update sub error!' );
    	}*/
        $return  = SubscribesModel::model()->search( array( 'ticket_id' => $id ) );
        foreach($return as $k=>$v){
            if(isset($args['group_price']) && $v['type']==0 && $v['group_price']!=$args['group_price']){
                //团客
                $content='您订阅的<'. $record[ "name" ].'><团客价>，由<'. $record[ 'group_price' ].'>变动到<'.$args[ 'group_price' ].'>，请注意查看！';
                SendmsgModel::model()->send( $content, 0,1 , $v[ 'organization_id' ],$or_id );
            }
            if(isset($args['fat_price']) && $v['type']==1 && $v['fat_price'] != $args['fat_price']){
                //散客
                $content='您订阅的<'. $record[ "name" ].'><散客价>，由<'. $record[ 'fat_price' ].'>变动到<'.$args[ 'fat_price' ].'>，请注意查看！';
                SendmsgModel::model()->send( $content, 0,1 , $v[ 'organization_id' ],$or_id );
            }
        }
        $update = array('update_time'=>time());
        if( isset( $args[ 'fat_price' ])) $update[ 'fat_price' ] = $args[ 'fat_price' ];
        if( isset( $args[ 'group_price' ])) $update[ 'group_price' ] = $args[ 'group_price' ];
        if( SubscribesModel::model()->updateByAttr( $update, array( 'ticket_id' => $id ) ) )
        {
            return ;
        }
        else
        {
            Lang_Msg::error( 'update sub error!' );
        }
    }
}