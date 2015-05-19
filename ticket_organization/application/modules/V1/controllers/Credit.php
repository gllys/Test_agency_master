<?php
class CreditController  extends Base_Controller_Api
{
	
	public function updateAction()
	{
		$action_type = intval($this->body['action_type']); //操作类型：0调整，1支付，2退款
        !in_array($action_type,array(0,1,2)) && $action_type = 0;
		//  type 1:信用额度， 0：储值额度
		//$distributor_id = $this->body[ 'distributor_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_1' ): intval( $this->body[ 'distributor_id' ]);
		$type 			= $this->body[ 'type' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_2' ):intval( $this->body[ 'type' ] );
		$user_id		= $this->body[ 'user_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_8' ):$this->body[ 'user_id' ];
		if( $this->body[ 'id' ] )
		{
			$id  = $this->body[ 'id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_6' ): intval( $this->body[ 'id' ] );
		}
		else
		{
			$distributor_id = $this->body[ 'distributor_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_1' ): intval( $this->body[ 'distributor_id' ]);
			$supplier_id = $this->body[ 'supplier_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_18' ): intval( $this->body[ 'supplier_id' ]);
		}
		
		$infinite = -1;
		//is infinite
		if( $type ==1 )
		{
			if( $this->body[ 'infinite' ]!=null )
			{
				$infinite = $this->body[ 'infinite' ];
			}
			if($infinite<1)
			{
				$num = $this->body[ 'num' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_3' ) : $this->body[ 'num' ] ;
				$remark = $this->body[ 'remark' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_4' ) :$this->body[ 'remark' ]; 
			}
		}
		else
		{
				$num = $this->body[ 'num' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_3' ) : $this->body[ 'num' ] ;
				$remark = $this->body[ 'remark' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_4' ) :$this->body[ 'remark' ]; 
		}
		//check 
		if( $this->body[ 'id' ]  )
		{
			$data = CreditModel::model()->getById( $id );
		}
		else
		{
			$tmp = CreditModel::model()->search( array( 'distributor_id' => $distributor_id , 'supplier_id' => $supplier_id) );
			$data = reset( $tmp );
			$id = $data[ 'id' ];
		}
		if( !$data )Lang_Msg::error( Lang_Msg::getLang( 'ERROR_CREDIT_7' ) );

		//Operation
		try 
		{
			CreditModel::model()->begin();
            $arr = array();
			if( $type == 1 )
			{	
				if(1==$infinite)
				{	
				
					$arr[ 'credit_moeny' ] = 999999999;
				}
				else
				{
					$arr[ 'credit_moeny' ] = $num;
				}
				
			}
			else
			{
				$arr[ 'balance_money' ] = $num;
			}

			//intser log
			$insertData = array( 'action_type'=>$action_type,'supplier_id' =>$data[ 'supplier_id' ], 'distributor_id' => $data[ 'distributor_id' ], 'user_id' => $user_id , 'remark' => $remark, 'add_time' => time()) + $arr ;

            CreditlogModel::model()->insert(  $insertData );
			//update
			$arr = array();
			if( $type ==1 )
			{

                if( 1==$infinite)
				{
					$arr[ 'credit_infinite' ] = $infinite;
				}
				else
				{
                    if(0==$infinite)
                        $arr[ 'credit_infinite' ] = $infinite;
                    $arr[ 'credit_money' ] = $num + $data[ 'credit_money' ];
				}
				if( $arr[ 'credit_money']  < 0 )Lang_Msg::error( 'ERROR_CREDIT_27' );
			}
			else
			{
				$arr[ 'balance_money'] = $num + $data[ 'balance_money'];
				if( $arr[ 'balance_money']  < 0 )Lang_Msg::error( 'ERROR_CREDIT_27' );
			}
			//update 
			if( null !==$this->body[ 'checkout_type' ] ) $arr[ 'checkout_type' ] = intval( $this->body[ 'checkout_type' ] );
			if( $this->body[ 'checkout_date' ] ) $arr[ 'checkout_date' ] = intval( $this->body[ 'checkout_date' ] );

            CreditModel::model()->updateById( $id,  $arr );
		 	CreditModel::model()->commit();
		 	Lang_Msg::output( Lang_Msg::getLang( 'ERROR_CREDIT_5' ) );
		}
		catch( PDOException $err )
		{
			CreditModel::model()->rollback();
			Lang_Msg::error( Lang_Msg::getLang( 'ERROR_CREDIT_7' ) );
		}
	
	}
	
	//bind
	public function bindAction()
	{   
		$arg[ 'distributor_id' ] = $this->body[ 'distributor_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_1' ): intval( $this->body[ 'distributor_id' ]);
		$arg[ 'supplier_id' ]   =$this->body[ 'supplier_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_9' ): intval( $this->body[ 'supplier_id' ]);
		if( CreditModel::model()->search( $arg ) )Lang_Msg::error( 'ERROR_CREDIT_14' );
		$arg[ 'distributor_name' ]   =$this->body[ 'distributor_name' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_13' ): $this->body[ 'distributor_name' ];
		
		$tmp = OrganizationModel::model()->search( array( 'id' => $arg['supplier_id'], 'type' =>'supply' ,'is_del' => 0) );
        if( !$tmp ) Lang_Msg::error("ERROR_CREDIT_25" );
        $arg[ 'supplier_name' ] = $tmp[ key( $tmp ) ][ 'name' ];
        $tmp = array();
        $tmp = CreditModel::model()->search( array( 'distributor_id' => $arg[ 'distributor_id' ] , 'supplier_id' => $arg[ 'supplier_id' ] ) );
        if( $tmp )Lang_Msg::error("ERROR_CREDIT_26" );
		$arg[ 'add_time' ] = time();
		$arg[ 'source' ]   = 2;
		$re = CreditModel::model()->add(  $arg );
	    if( $re )
	    {   
	    	$update =array( 'agency_id' => $arg[ 'distributor_id' ], 'supply_id' =>$arg[ 'supplier_id' ] );
	    	if( !SupplyAgencyHistoryModel::model()->search( $update ) )
	    	{	
	    		$update[ 'agency_name' ] = $arg[ 'distributor_name' ];
	    		$update[ 'created_time' ] = time();
	    		$update[ 'is_bind' ] = 1;
	    		SupplyAgencyHistoryModel::model()->add( $update );
	    	}
	    	else
	    	{	
	    		SupplyAgencyHistoryModel::model()->updateByAttr( array( 'created_time' => time(), 'is_bind' => 1 ), $update );
	    	}
	    	 Tools::lsJson(true,'ok');
	    }
	    else
	    {
	    	Lang_Msg::error( 'ERROR_CREDIT_12' );
	    }
	}
	
	
	//显示自己下面所有的分销商
	public function listsAction()
	{	
		$where ='';
		$where .= $this->body[ 'supplier_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_18' ):  'supplier_id = '.intval( $this->body[ 'supplier_id' ]);
		if( $this->body[ 'name' ] )
		{
			$where .=' and  ';
			$where .= $this->body[ 'name' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_18' ):  "distributor_name like '%".$this->body[ 'name' ] ."%'";
		}
		if( $this->body[  'distributor_id' ] ) $where .=' and  distributor_id = '. $this->body[ 'distributor_id' ];
        if($this->body['source']) $where .= ' and source='.intval($this->body['source']); // 来源1注册2绑定
        if(!empty($this->body['add_time'])) { // 创建时间段
			$add_time = explode(' - ',$this->body['add_time']);
			$start_at = intval(strtotime(reset($add_time).' 00:00:00'));
			$end_at = intval(strtotime(end($add_time).'  23:59:59'));
			($end_at<$start_at || !Validate::isUnsignedInt($start_at) || !Validate::isUnsignedInt($end_at)) && Lang_Msg::error("ERROR_LIST_1");
			$where .= ' and add_time between '.$start_at.' and '.$end_at;
		}
		
		//查询 
		$page = 1;
		if( $this->body[ 'p' ] )
		{
			$page = intval( $this->body[ 'p' ] );
		}
		$limit = array();
      	null !== $this->body[ 'items' ] ? $page_limit =intval($this->body[ 'items']):$page_limit =15;
	   	if( $page >0 )$limit = array(( $page-1 )*$page_limit, $page_limit );
		$count = CreditModel::model()->countResult($where);
       	$total = ceil($count/ $page_limit);
        if(isset($this->body['show_all']) && $this->body['show_all']){
            $limit = null;
        }
       	$data[ 'data' ] = $count > 0 ? CreditModel::model()->search( $where, '*', 'convert(`distributor_name` USING gbk) COLLATE gbk_chinese_ci asc', $limit ):array();
        $data['pagination'] = array(
            'count'=>$count,
            'current'=>$page,
            'items' => $page_limit,
            'total' => $total,
        );
        Tools::lsJson(true,'ok',$data);
	}
	
	
	//供应商修改记录查询 
	public  function listWithModifAction()
	{	
		$where =' action_type=0 ';
		$where .= $this->body[ 'supplier_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_18' ):  ' and supplier_id = '.intval( $this->body[ 'supplier_id' ]);
		$where .= $this->body[ 'distributor_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_1' ): ' and distributor_id = '.intval( $this->body[ 'distributor_id' ] );
		// type  0 =>信用  1=》储值
		$type= $this->body[ 'type' ] == NULL ? Lang_Msg::error(  'ERROR_CREDIT_2' ): intval( $this->body[ 'type' ] );
		$where .= $type == 0 ? ' and credit_moeny != 0'  : ' and balance_money != 0';
		if( $this->body[ 'remark' ] )
		{	
			$remark = $this->body[ 'remark' ];
			$where .= " and remark like '%".$remark."%'";
		}
		$page = 1;
		if( $this->body[ 'p' ] )
		{
			$page = intval( $this->body[ 'p' ] );
		}
	
		$limit = array();
      	null !== $this->body[ 'items' ] ? $page_limit =intval($this->body[ 'items']):$page_limit =15;
	   	if( $page >0 )$limit = array(( $page-1 )*$page_limit, $page_limit );
		$count = CreditlogModel::model()->countResult($where);
       	$total = ceil($count/ $page_limit);
       	$tmp = $count > 0 ? CreditlogModel::model()->search( $where, '*', "id desc", $limit ):array();
       	foreach( $tmp as $key => $value )
       	{	
       		if( $value[ 'credit_moeny' ] == 999999999 )
       		{
       			$tmp[ $key ][ 'credit_moeny' ] ='无限';
       		}
       	}
       	$data[ 'data' ]  = $tmp;
        $data['pagination'] = array(
            'count'	=>$count,
            'current'=>$page,
            'items' => $page_limit,
            'total' => $total,
        );
        Tools::lsJson(true,'ok',$data);
	}
	
	//set check day
	public function setDayAction()
	{
		$arg[ 'checkout_type' ] = $this->body[ 'type' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_11' ): intval( $this->body[ 'type' ]);
	    $id = $this->body[ 'id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_6' ): $this->body[ 'id' ];
	    $arg[ 'checkout_date' ] = $this->body[ 'day' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_10' ): intval( $this->body[ 'day' ] );
	    $re = CreditModel::model()->updateById( $id, $arg );
	    if( $re )
	    {
	    	 Tools::lsJson(true,'ok');
	    }
	    else
	    {
	    	Lang_Msg::error( 'ERROR_CREDIT_12' );
	    }
	}
	
	//del
	public function delAction()
	{
		 $id = $this->body[ 'id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_6' ): $this->body[ 'id' ];
		 
		 //绑定记录
		 $info = CreditModel::model()->getById( $id);
		 if( !$info )Lang_Msg::error( 'ERROR_CREDIT_16' );
		 if( CreditModel::model()->deleteById( $id ) )
		 {   
			TicketPolicyModel::model()->unbindDistributor($info['distributor_id'], $info['supplier_id']);
	 
		 	$where[ 'agency_id' ] = $info[ 'distributor_id' ];
		 	$where[ 'supply_id' ] = $info[ 'supplier_id' ];
		 	if( SupplyAgencyHistoryModel::model()->search( $where ) )
		 	{	
		 		SupplyAgencyHistoryModel::model()->updateByAttr(  array( 'is_bind' => 0, 'delete_time' => time()), $where);
		 	}
		 	else
		 	{   
		 		SupplyAgencyHistoryModel::model()->add(  array( 'is_bind' => 0, 'delete_time' => time() ) + $where );
		 	}
		 	Tools::lsJson(true,'ok');
		 }
		 else
		 {
		 	Lang_Msg::error( 'ERROR_CREDIT_15' );
		 }
	}
	
	//over  额度：状态
	public function overAction()
	{
		 $id = $this->body[ 'id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_6' ): $this->body[ 'id' ];
		 $moeny = $this->body[ 'money' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_17' ): $this->body[ 'money' ];
		 $data = CreditModel::model()->getById( $id ) ;
		 if( !$data )Lang_Msg::error( 'ERROR_CREDIT_16' );
		 $arr[ 'balance_over' ] = $moeny.':0';
		 $update =CreditModel::model()->updateById( $id, $arr );
		 if( $update )
		 {
		 	 Tools::lsJson(true,'ok');
		 }
		 else
		 {
		 	Lang_Msg::error( 'ERROR_CREDIT_17' );
		 }
	}
	
	
	//detail by id
	public function detailAction()
	{
		$id = $this->body[ 'id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_6' ): intval( $this->body[ 'id' ] );
		$data = CreditModel::model()->getById( $id );
		Tools::lsJson(  true , '',$data );
	}
	
	//search
	public function getMoneyAction()
	{
		$where[ 'supplier_id' ] 	= $this->body[ 'supplier_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_18' ): intval( $this->body[ 'supplier_id' ] );
		$where[ 'distributor_id' ] 	= $this->body[ 'distributor_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_1' ): intval( $this->body[ 'distributor_id' ] );
		$data = CreditModel::model()->search( $where );
		if( !$data )Lang_Msg::error('ERROR_CREDIT_23');
		$return= array( 'credit_money' => 0, 'balance_money' => 0 );		
		if( $data )
		{	
			if( $data[ key( $data ) ][ 'credit_infinite' ] )
			{
				$return[ 'credit_money' ]= 'infinite';
			}
			else
			{	
				//print_r( $data[ key( $data ) ] );
				$return[ 'credit_money' ] = $data[ key( $data ) ][ 'credit_money' ];
			}
			$return[ 'balance_money'] = $data[ key( $data ) ][ 'balance_money' ];
			/* $str = explode(':', $data[ key( $data ) ][ 'balance_over' ] );
			 if( $str[ 1 ] == 0 )
			 {	
			 	$return[ 'balance_money'] +=$str[ 0 ];
			 }
			 */
		}
		Tools::lsJson( true,'', $return );
	}
	
	//data by distributor_id
	public function listbyxfAction()
	{	
		$where = $this->body[ 'distributor_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_18' ): 'distributor_id in ('.$this->body[ 'distributor_id' ].')' ;
		if( $this->body[ 'supplier_name' ] ) $where .= " and supplier_name like '%".  $this->body[ 'supplier_name' ] ."%'";
		$page =1;
		if( $this->body[ 'p' ] )
		{
			$page = intval( $this->body[ 'p' ] );
		}
		$limit = array();
      	null !== $this->body[ 'items' ] ? $page_limit =intval($this->body[ 'items']):$page_limit =15;
	   	if( $page >0 )$limit = array(( $page-1 )*$page_limit, $page_limit );
		$count = CreditModel::model()->countResult($where);
       	$total = ceil($count/ $page_limit);
       	$data[ 'data' ] = $count > 0 ? CreditModel::model()->search( $where, '*', null, $limit ):array();
        $data['pagination'] = array(
            'count'	=>$count,
            'current'=>$page,
            'items' => $page_limit,
            'total' => $total,
        );
        Tools::lsJson(true,'ok',$data);
	}
	
	//detail by name
	public function listWithSupplierAction()
	{	
		$where ='';
		if( $this->body[ 'supplier_id' ] ) $where= 'supplier_id = '.intval( $this->body[ 'supplier_id' ] );
		if( $this->body[ 'name' ] )	
		{
			if( $this->body[ 'supplier_id' ] )
			{
				$where .= ' and ';
			}
			$where .= "supplier_name like '%".$this->body[ 'name' ] ."%'";
		}
		if( !$where )
		{
			 Lang_Msg::error( 'ERROR_CREDIT_24' );
		}
		
		if( $this->body[ 'p' ] )
		{
			$page = intval( $this->body[ 'p' ] );
		}
		$limit = array();
      	null !== $this->body[ 'items' ] ? $page_limit =intval($this->body[ 'items']):$page_limit =15;
	   	if( $page >0 )$limit = array(( $page-1 )*$page_limit, $page_limit );
		$count = CreditModel::model()->countResult($where);
       	$total = ceil($count/ $page_limit);
       	$data[ 'data' ] = $count > 0 ? CreditModel::model()->search( $where, '*', null, $limit ):array();
        $data['pagination'] = array(
            'count'	=>$count,
            'current'=>$page,
            'items' => $page_limit,
            'total' => $total,
        );
        Tools::lsJson(true,'ok',$data);
		
	}
	
	//pay
	public function payAction()
	{	
		
		$where[ 'supplier_id' ] = $this->body[ 'supplier_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_18' ): intval( $this->body[ 'supplier_id' ] );
		$where[ 'distributor_id' ] = $this->body[ 'distributor_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_1' ): intval( $this->body[ 'distributor_id' ] );
	    $need_money = $this->body[ 'money' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_19' ): $this->body[ 'money' ] ;
	    $serial_id = $this->body[ 'serial_id' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_21' ): intval( $this->body[ 'serial_id' ] );
	    //方式 0 信用 ，1：储值
	    $type = $this->body[ 'type' ] == null ? Lang_Msg::error( 'ERROR_CREDIT_11'): $this->body[ 'type' ];
	    if( !in_array( $type , array( 0,1))) Lang_Msg::error( 'ERROR_CREDIT_11');

	    
	    //判断是否已经支付过
	    $payLog = CreditpayModel::model()->search(  array( 'serial_id' =>  $serial_id ) );
	    if( $payLog )Lang_Msg::error( 'ERROR_CREDIT_28' );
	    
	    $data = CreditModel::model()->search( $where );
        $nowTime = time();
	    //add pay_log
	    $log = array();
	    $log[ 'serial_id' ]= $serial_id;
	    $log[ 'supplier_id' ] = $where[ 'supplier_id' ];
	    $log[ 'distributor_id' ] = $where[ 'distributor_id' ];
	    $log[ 'money' ]	= $need_money;
	    $log[ 'type' ]	= $type;
	    $log[ 'add_time' ]= $nowTime;
	 
	    if( !$data )Lang_Msg::error('ERROR_CREDIT_20');

	    $update = $arr = array();
	    $data = $data[ key( $data ) ];
		
	    if( $type == 0 )
	    {	
	    	
	    	//信用支付
	    	if( !$data[ 'credit_infinite' ] )
	    	{	
	    		if( $data[ 'credit_money' ] >= $need_money )
	    		{
	    			$update[ 'credit_money' ] = $data[ 'credit_money' ]- $need_money;
                    $arr[ 'credit_moeny' ] = '-'.$need_money;
	    		}
	    		else
	    		{
	    			Lang_Msg::error('ERROR_CREDIT_20');
	    		}
	    	}
	    }
	    else
	    {
	    	//储值支付
	    	$str = explode(':', $data[ 'balance_over'] );
	    	$balance_money = $data[ 'balance_money' ];
	    	if( count( $str  ) >1 )
	    	{	
	    		if( $str[ 1 ] == 0 )
	    		{
	    			$balance_money =  $str[ 0 ]+ $data[ 'balance_money' ];
	    		}
	    	}
	    	if( $balance_money >= $need_money )
	    	{	
	    		//
	    		if( $need_money > $data[ 'balance_money'] )
	    		{
	    			$str[ 1 ] = $need_money- $data[ 'balance_money' ];
	    			$update[ 'balance_over' ] =join(':',$str );
	    			$update[ 'balance_money' ] =0;
	    			
	    		}
	    		else
	    		{	
	    			$update[  'balance_money'  ] = $data[ 'balance_money'] - $need_money;
	    		}
                $arr[ 'balance_money' ] = '-'.$need_money;
	    	}
	    	else
	    	{
	    		Lang_Msg::error('ERROR_CREDIT_20');
	    	}
	    	
	    }
	    
	    //update mysql
	    try
	    {
	    	CreditModel::model()->begin();
	    	
	    	CreditpayModel::model()->insert( $log );
	   		if( $update )
	   		{
	    		CreditModel::model()->updateById( $data[ 'id' ], $update );
	   		}

            $logData = array( 'action_type'=>1,'supplier_id' =>$log[ 'supplier_id' ], 'distributor_id' => $log[ 'distributor_id' ], 'user_id' => 0 , 'remark' => $serial_id, 'add_time' => $nowTime) + $arr ;
            CreditlogModel::model()->insert( $logData );

	    	CreditModel::model()->commit();
	    	Tools::lsJson(true,'支付成功');
	    }
	 	catch ( Exception  $err)
	    {
            CreditModel::model()->rollback();
	    	Lang_Msg::error('ERROR_CREDIT_22');
	    }
	}
	
	
	 public function historyAction()
	{
	 	!Validate::isInt($this->body['supply_id'] ) && Lang_Msg::error('没有supply_id' );
	 	$where[ 'supply_id' ] = $this->body[ 'supply_id' ];
	 	$limit = array();
       null !== $this->body[ 'items' ] ? $page_limit =intval($this->body[ 'items']):$page_limit =15;
       $page =1;
       if( $this->body[ 'current' ] ) $page = $this->body[ 'current' ] ;
	   if( $page >0 )$limit = array(( $page-1 )*$page_limit, $page_limit);
       $count = SupplyAgencyHistoryModel::model()->countResult($where);
       $total = ceil($count/ $page_limit);
       $return = array();
       $return[ 'data' ] = $count > 0 ? SupplyAgencyHistoryModel::model()->search( $where, '*', null, $limit ):array();
       $return[ 'pagination' ] = array( 'count' => $count , 'current' => $page, 'items' => $page_limit, 'total' => $total);
       Lang_Msg::output($return);
	}
}