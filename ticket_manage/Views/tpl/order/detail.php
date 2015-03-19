<!DOCTYPE html>
<html>
<?php get_header();?>
<body>
<?php get_top_nav();?>
<div class="sidebar-background">
    <div class="primary-sidebar-background"></div>
</div>
<?php get_menu();?>
<div class="main-content">
<?php get_crumbs();?>
<div id="show_msg"></div>	
	
<style>

div.selector{
	margin:0 10px;
	width:200px;
	}
.table-normal tbody td a{
	margin:0 5px;
	text-decoration:none;
	}
.table-normal button{
	min-width:inherit;
	}
.table-normal tbody td{
	text-align:center
	}
</style>
	<div class="container-fluid padded">
		<div class="box">
		<div class="box-header">
			<span class="title"><i class="icon-list"></i> 门票使用查询</span>
		</div>
			<div class="table-header" style="height:auto;padding-bottom:10px;">
			  <form action="">
				<div class="row-fluid" style="margin-bottom:10px;">
					电子编码：<input type="text" placeholder="" name="order_hash" style="width:150px;margin:0 10px 0" value="<?php echo $get['order_hash'];?>">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					票号：<input type="text" placeholder="" name="ticket_hash" style="width:150px;margin:0 10px 0" value="<?php echo $get['ticket_hash']?>">
				</div>
			  
				<div class="row-fluid" style="margin-bottom:10px;">
					游玩时间：<input type="text" placeholder="" name="useday_time" style="width:150px;margin:0 10px 0" class="form-time">
					使用时间：<input type="text" placeholder="" name="used_time" style="width:150px;margin:0 10px 0" class="form-time">
				</div>
				<div class="row-fluid" style="margin-bottom:10px;">
					门票名称：<input type="text" name="landscape_name" style="width:150px;margin:0 10px 0" value="<?php echo $get['landscape_name']?>" />
					门票状态：<select class="uniform" name="ticket_status">
                                            <option  selected="selected">选择门票状态</option>
						<option value='unused' <?php if($get['ticket_status'] == 'unused'):?>selected="selected"<?php endif;?>>未使用</option>
						<option value='used' <?php if($get['ticket_status'] == 'used'):?>selected="selected"<?php endif;?>>已使用</option>
						<option value='refunding' <?php if($get['ticket_status'] == 'refunding'):?>selected="selected"<?php endif;?>>退款中</option>
						<option value='refunded' <?php if($get['ticket_status'] == 'refunded'):?>selected="selected"<?php endif;?>>已退款</option>
					</select>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				  <button class="btn btn-default" style="float:none;">搜索</button>
				</div>
			  </form>
            </div>


			<div class="content">
			<table class="table table-normal order-list">
				<thead>
				  <tr>
						<td>电子编码</td>
						<td>票号</td>
						<td>门票名称</td>
						<td>门票类型</td>
						<td>支付方式</td>
						<td>状态</td>
						<!-- <td>退票</td> -->
						<td>游玩时间</td>
						<td>使用时间</td>
				  </tr>
				</thead>
				<tbody>
				    <?php if($ticketsList):?>
					    <?php foreach($ticketsList as $ticket):?>
					    <tr>
							<td><?php echo $ticket['order_id'];?></td>
							<td><?php echo $ticket['id'];?></td>
							<td><?php echo $ticket['landscape_name'];?></td>
							<td><?php echo $ticket['ticket_tmp_name'];?></td>
							<td class="center"><?php echo OrderCommon::getPayments($ticket['order_payment']);?></td>
							<td class="center"><?php echo TicketCommon::getShowStatus($ticket);?></td>
							<!-- <td class="center"><?php echo $ticket['refund_apply_status'] ? RefundApplyCommon::getRefundApplyStatus($ticket['refund_apply_status']) : '';?></td> -->
							<td class="center"><?php echo $ticket['order_useday'];?></td>
							<td class="center"><?php echo ($_model = $ticketUsedModel->get('ticket_id',$ticket['id']))?$_model['created_at']:'未使用' ?></td>
					    </tr>
					    <?php endforeach;?>
				    <?php endif;?>
				</tbody>
			</table>
            </div>
			
			<div class="dataTables_paginate paging_full_numbers">
				<?php echo $pagination;?>
			</div>
			
		</div>
	</div>
</div>

<link href="Views/css/daterangepicker.css" rel="stylesheet">
<script src="Views/js/vendor/date.js"></script>
<script src="Views/js/vendor/moment.js"></script>
<script src="Views/js/vendor/daterangepicker.js"></script>
<script src="Views/js/jquery.slimscroll.min.js"></script>
<script>
$(document).ready(function() {
	$('.form-time').daterangepicker({
		format:'YYYY-MM-DD'
		})
})
</script>
</body>
</html>