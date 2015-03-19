<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h6 id="modal-formLabel">" <?php echo $landscape['name'];?> " 二级票务信息</h6>
</div>
<div id="verify_return"></div>
<div class="modal-body select" id="modal-body">
	<div class="container-fluid">
		<table class="table table-normal">
			<thead>
				<tr>
					<td>二级票务编号</td>
					<td>类型</td>
                                        <td style="width:100px;">销售类型</td>
					<td>支付方式</td>
					<td>适用时间</td>
					<!-- <td>总库存</td>
					<td>剩余库存</td> -->
					<td>结算价</td>
					<td>销售价</td>
					<td>挂牌价</td>
					<td>散客价</td>
					<td>团客价</td>
					<td>其他信息</td>
					<td>状态</td>
					<td>提交时间</td>
					<td>审核时间</td>
					<td>操作</td>
				</tr>
			</thead>
			<tbody>
				<?php if($list):?>
					<?php foreach($list as $ticket):?>
					<tr>
						<td><?php echo $ticket['id'];?></td>
						<td><?php echo $ticket['name'];?></td>
						<td><?php 
						 if($ticket['source']==1){
						 	echo "网络门票";
						 } else {
                            
						    echo "窗口门票";
						 }?>
						 <?php if($ticket['ticket_type']==5){
						 	echo "<font color='red'>(联票)</font>";
						 	
						 }?></td>
						<td><?php echo TicketCommon::getTicketPayment($ticket['payment']);?></td>
						<td>
							<div class="pop">

								<i class="icon-zoom-in" data-placement="bottom" data-toggle="popover" data-container="body"></i>
								<div class="pop-content">
									<ul>
										<li>适用时间段：<?php echo $ticket['expire_start_at'].'~'.$ticket['expire_end_at'];?></li>
										<li>适用日期：<?php $ticketWeek = TicketCommon::getTicketWeek();$weekly     = explode(',', $ticket['weekly']);
														if($weekly){
															foreach($ticketWeek as $ticketWeekK => $ticketWeekV){
																if(in_array($ticketWeekK, $weekly)) {
																	echo $ticketWeekV.'、';
																}
															}
														}?>
										</li>
									</ul>
								</div>
							</div>
						</td>
						<!-- <td>1000</td>
						<td>1000</td> -->
						<td><?php echo $ticket['sale_price'];?></td>
						<td><?php echo $ticket['market_price'];?></td>
						<td><?php echo $ticket['brand_price'];?></td>
						<td><?php echo $ticket['retail_price'];?></td>
						<td><?php echo $ticket['group_price'];?></td>
						<td>
							<div class="pop">
								<i class="icon-zoom-in" data-placement="bottom" data-toggle="popover" data-container="body"></i>
								<div class="pop-content">
									<ul>
									<li>允许退票：<?php if($ticket['allow_back'] == 'yes'):?>是<?php else:?>否<?php endif;?></li>
									<li>使用有效期：<?php if($ticket['use_expire'] == 0):?>使用日期当天有效<?php else:?>使用日期（含）之后<?php echo $ticket['use_expire'];?>天有效<?php endif;?></li>
									<li>提前预订：<?php echo $ticket['reserve'];?>天</li>
									<li>最少订票数：<?php echo $ticket['min_order'];?></li>
									<li>最多订票数：<?php echo $ticket['max_order'];?></li>
									<li>是否可改期：<?php if($ticket['allow_change_times'] == 0):?>不可改期<?php else:?><?php echo $ticket['allow_change_times'];?>次<?php endif;?></li>
									<li>产品说明：<?php echo $ticket['description'];?></li>
									</ul>
								</div>
							</div>
						</td>
						<td><span class="label <?php if($ticket['status'] == 'normal' || $ticket['status'] == 'failed'):?>label-green<?php else:?>label-red<?php endif;?>"><?php echo TicketCommon::getTicketStatus($ticket['status']);?></span></td>
						<td><?php echo $ticket['created_at'];?></td>
						<td><?php if($ticket['status'] == 'normal'){echo $ticket['audited_at'];}?></td>
						<td>
	
							<?php if($ticket['ticket_type']==5):?>
								<?php if($union):?>
									<?php if(strstr($union,$ticket['id'])): echo "<button class=\"btn btn-blue\" disabled>待审核</button>";?>
									<?php else:?>
										<a title="审核" href="" data-original-title="审核" class="ticketVerify" data-ticket-id="<?php echo $ticket['id'];?>"><button class="btn btn-blue">审核</button></a>
									<?php endif;?>	
								<?php else:?>		
							<!-- <a title="编辑" href=""><i class="icon-edit"></i></a> -->
									<?php if($ticket['status'] == 'unaudited'):?><a title="审核" href="" data-original-title="审核" class="ticketVerify" data-ticket-id="<?php echo $ticket['id'];?>"><button class="btn btn-blue">审核</button></a><?php endif;?>
							<!-- <a title="删除" href="" class="del-ticket"><i class="icon-trash"></i></a> -->
								<?php endif;?>
							<?php else:?>	
									<?php if($ticket['status'] == 'unaudited'):?><a title="审核" href="" data-original-title="审核" class="ticketVerify" data-ticket-id="<?php echo $ticket['id'];?>"><button class="btn btn-blue">审核</button></a><?php endif;?>
							<?php endif;?>		

						</td>
					</tr>
					<?php endforeach;?>
				<?php else:?>
					<tr>
						<td colspan="12">无记录</td>
					</tr>
				<?php endif;?>
			</tbody>
		</table>

		
	</div>
</div>
<script>
	//审核按钮
	$('.ticketVerify').popover({'placement':'bottom','html':true}).click(function(){
		var ticket_id = $(this).attr('data-ticket-id');
		var html='<div class="editable-buttons ticket"><button class="btn  btn-primary btn-sm" data-ticket-id="'+ticket_id+'"><i class="icon-ok"></i></button><button class="btn btn-default btn-sm" data-ticket-id="'+ticket_id+'"><i class="icon-remove"></i></button></div>'
		$('.popover-content').html(html)
		return false
	})
	
	//审核按钮 - 同意
	$(document).one('click','.ticket .btn-primary',function(){
		var ticket_id = $(this).attr('data-ticket-id');
		if(confirm('确定要审核通过吗？')) {
			verifyTicket(ticket_id, 'normal');
		}
		$('.ticketVerify').popover('hide')
                return false;
	});

	//审核按钮 - 不同意
	$(document).one('click','.ticket .btn-default',function(){
		var ticket_id = $(this).attr('data-ticket-id');
		if(confirm('确定要驳回吗？')) {
			verifyTicket(ticket_id, 'failed');
		}
		$('.ticketVerify').popover('hide');
                return false;
	});


	function verifyTicket(ticket_id, status){
		$.post('index.php?c=ticket&a=ticketVerify', {id:ticket_id,status:status},function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
				$('#verify_return').html(warn_msg);
			}else if(data['data'][0]['id']){
				var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功!</strong></div>';
				$('#verify_return').html(succss_msg);
                                setTimeout(function(){
                                modal_jump(<?php echo $landscape['id']; ?>, 'unaudited');
                                }, 2000);
			}
		}, "json");
		return false;
	}
</script>