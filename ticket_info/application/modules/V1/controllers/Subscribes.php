<?php
class SubscribesController extends Base_Controller_Api
{
	public function addAction()
	{	
		!Validate::isInt($this->body['ticket_id'] ) && Lang_Msg::error('没有票板ID');
		!Validate::isInt($this->body['organization_id'] ) && Lang_Msg::error('没有机构ID');
		!Validate::isString( $this->body[ 'name' ] ) && Lang_Msg::error( '没有name参数！');	
		!Validate::isString( $this->body[ 'fat_price' ] ) && Lang_Msg::error( '没有fat_price参数！');
		!Validate::isString( $this->body[ 'group_price' ] ) && Lang_Msg::error( '没有group_price参数！');
		!Validate::isString( $this->body[ 'type' ] ) && Lang_Msg::error( '没有type参数！');
		$args[ 'ticket_id' ] = $this->body[ 'ticket_id' ];	
		$args[ 'organization_id' ] = $this->body[ 'organization_id' ];	
		$args[  'type' ] = intval( $this->body[ 'type' ] );
		if( SubscribesModel::model()->search( $args ) ) Lang_Msg::error('已经订阅');
		$args[ 'name' ] = $this->body[ 'name' ];	
		$args[ 'fat_price' ] = $this->body[ 'fat_price' ];	
		$args[ 'group_price' ] = $this->body[  'group_price' ];
		$args[  'update_time' ] = time();
		
		if( SubscribesModel::model()->add(  $args ) )
		{
			Lang_Msg::output( 'suss');
		}
		else 
		{
			Lang_Msg::error('error');
		}
	}

    /**
     * 列表
     * author : yinjian
     */
    public function listsAction()
    {
        !Validate::isInt($this->body['organization_id'] ) && Lang_Msg::error('没有机构ID');
        !Validate::isString( $this->body[ 'type' ] ) && Lang_Msg::error( '没有type参数！');
        $args[ 'organization_id' ] = $this->body[ 'organization_id' ];
        $args[ 'type' ] = $this->body[ 'type' ];
        // 分页
        $count = reset(SubscribesModel::model()->search($args,'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        $data['data'] = SubscribesModel::model()->search($args);
        // 获取是否联票
        if($data['data']){
            $ticket_ids = array();
            foreach($data['data'] as $k => $v){
                $ticket_ids[] = $v['ticket_id'];
            }
            $ticket = TicketTemplateModel::model()->search(array('id|in'=>array_flip(array_flip($ticket_ids))),'*');
            foreach($data['data'] as $k => $v){
                $data['data'][$k]['is_union'] = isset($ticket[$v['ticket_id']]['is_union'])?$ticket[$v['ticket_id']]['is_union']:0;
//                scenic_id    organization_id   date_available   sale_price   listed_price  mini_buy
                $data['data'][$k]['scenic_id'] = isset($ticket[$v['ticket_id']]['scenic_id'])?$ticket[$v['ticket_id']]['scenic_id']:0;
                $data['data'][$k]['organization_id'] = isset($ticket[$v['ticket_id']]['organization_id'])?$ticket[$v['ticket_id']]['organization_id']:0;
                $data['data'][$k]['date_available'] = isset($ticket[$v['ticket_id']]['date_available'])?$ticket[$v['ticket_id']]['date_available']:0;
                $data['data'][$k]['sale_price'] = isset($ticket[$v['ticket_id']]['sale_price'])?$ticket[$v['ticket_id']]['sale_price']:0;
                $data['data'][$k]['listed_price'] = isset($ticket[$v['ticket_id']]['listed_price'])?$ticket[$v['ticket_id']]['listed_price']:0;
                $data['data'][$k]['mini_buy'] = isset($ticket[$v['ticket_id']]['mini_buy'])?$ticket[$v['ticket_id']]['mini_buy']:0;
            }
        }
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Lang_Msg::output($data);
    }

    public function countAction()
    {
        !Validate::isInt($this->body['organization_id'] ) && Lang_Msg::error('没有机构ID');
        $args[ 'organization_id' ] = $this->body[ 'organization_id' ];
        isset($this->body['type']) && $args[ 'type' ] = $this->body[ 'type' ];
        // 分页
        $count = reset(SubscribesModel::model()->search($args,'count(*) as count'));
        $this->count = $count['count'];
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Lang_Msg::output($data);
    }
	
	public function deleteAction()
	{
		!Validate::isInt($this->body['ticket_id'] ) && Lang_Msg::error('没有票板ID');
		!Validate::isInt($this->body['organization_id'] ) && Lang_Msg::error('没有机构ID');
		!Validate::isString( $this->body[ 'type' ] ) && Lang_Msg::error( '没有type参数！');
		$args[ 'ticket_id' ] = $this->body[ 'ticket_id' ];	
		$args[ 'organization_id' ] = $this->body[ 'organization_id' ];	
		$args[ 'type' ] = $this->body[ 'type' ];
		if( !SubscribesModel::model()->search( $args ) ) Lang_Msg::error('没有被订阅');
		if( SubscribesModel::model()->delete( $args))
		{
			Lang_Msg::output( 'suss');
		}
		else
		{
			Lang_Msg::error('error');
		}
	}
}