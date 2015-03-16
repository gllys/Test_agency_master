<?php
class FavoritesController extends Base_Controller_Api
{
	//添加一张票到收藏
	public function addAction()
	{	
		$args = array();
		!Validate::isInt($this->body['ticket_id'] ) && Lang_Msg::error('没有票板ID');
		!Validate::isInt($this->body['organization_id'] ) && Lang_Msg::error('没有机构ID');
		!Validate::isString( $this->body[ 'name' ] ) && Lang_Msg::error( '没有name参数！');
		!Validate::isString( $this->body[ 'type' ] ) && Lang_Msg::error( '没有type参数！');
		$args[ 'ticket_id' ]= $this->body[ 'ticket_id' ];
		$args[ 'organization_id' ]= $this->body[ 'organization_id' ] ;
		$args[ 'type' ] = $this->body[ 'type' ];
		if( FavoritesModel::model()->search($args) )Lang_Msg::error('已经收藏过了');
		$args[ 'add_time' ] = time();
		$args[ 'name' ] = $this->body[ 'name' ];
		$return = FavoritesModel::model()->add( $args );
		if( $return )
		{
			Tools::lsJson( true, '收藏成功！');
		}
		else
		{
			Tools::lsJson( false);
		}
	}
	
	//根据名字来查找
	public function listAction()
	{	
	   	!Validate::isInt($this->body['organization_id'] ) && Lang_Msg::error('没有机构ID');
	   	!Validate::isInt($this->body['type'] ) && Lang_Msg::error('no params type');
	   	 $where = "  type = ".intval( $this->body[ 'type' ] )." and organization_id = ". $this->body['organization_id'];
	   	if( $this->body[ 'name' ] )
	   	{
	   		$where .= " and name like '%". $this->body[ 'name' ]."%' ";
	   	}
	   	//查询
	   $limit = array();
       null !== $this->body[ 'items' ] ? $page_limit =intval($this->body[ 'items']):$page_limit =15;
       $page =1;
       if( $this->body[ 'current' ] ) $page = $this->body[ 'current' ] ;
	   if( $page >0 )$limit = array(( $page-1 )*$page_limit, $page_limit);
       $count = FavoritesModel::model()->countResult($where);
       $total = ceil($count/ $page_limit);
       $tmp = $count > 0 ? FavoritesModel::model()->search( $where, '*', null, $limit ):null;
       $return = array();
       $return[ 'data' ] = array();
       foreach( $tmp as $key => $value )
       {	
       		$ticket_info = TicketTemplateModel::model()->getById( $value[  'ticket_id' ] );
       		$subscribes  = SubscribesModel::model()->search( array( 'ticket_id' => $value[  'ticket_id' ] , 'organization_id' => $this->body['organization_id'] , 'type' => $this->body[ 'type' ]) );
       		if( $subscribes )
       		{
       			$ticket_info[ 'sub' ] =1;
       		}
       		else
       		{
       			$ticket_info[ 'sub' ] =0;
       		}
       		$return[ 'data' ][ $key ] = $ticket_info;
       }
       $return[ 'pagination' ] = array( 'count' => $count , 'current' => $page, 'items' => $page_limit, 'total' => $total);
       Lang_Msg::output($return);
	}
	//删除一个收藏
	public function deleteAction()
	{
		!Validate::isInt( $this->body[ 'organization_id' ] ) && Lang_Msg::error( '缺少organization_id参数');
		!Validate::isInt( $this->body[ 'ticket_id' ] ) && Lang_Msg::error( '缺少ticket_id参数');
		if( !FavoritesModel::model()->search(  array( 'organization_id' => $this->body[ 'organization_id' ], 'ticket_id' => $this->body[ 'ticket_id' ]))) Lang_Msg::error( '没有这条记录');
		if( FavoritesModel::model()->delete( array( 'organization_id' => $this->body[ 'organization_id' ], 'ticket_id' => $this->body[ 'ticket_id' ] ) ))
		{
			Lang_Msg::output( 'ok' );
		}
		else 
		{
			Lang_Msg::error('删除失败');
		}
	}
	
	//收藏数量
	public function countAction()
	{
		Cache_Memcache::factory()->flush();
		!Validate::isInt( $this->body[ 'organization_id' ] ) && Lang_Msg::error( '缺少organization_id参数');
		$args[ 'organization_id' ] = $this->body[ 'organization_id' ];
		Lang_Msg::output( array( 'count' => FavoritesModel::model()->countResult(  $args ) ));
	}
}