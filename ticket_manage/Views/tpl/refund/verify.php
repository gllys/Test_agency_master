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
.modal .table-normal tbody td{
	text-align:left
	}
.table-normal .dropdown-menu{
	left:auto;
	right:0;
	}
.order-list{
	width:3000px;
	max-width:inherit
	}
.table-header span{
	display:inline-block;
	width:80px;
	}
.box .box-header .box-toolbar, .box .box-footer .box-toolbar{
	float:none;
	}
.box .box-header .box-toolbar > li, .box .box-footer .box-toolbar > li{
	margin:0 10px 0 0;
	}
.box .box-header .box-toolbar > li.toolbar-link > a, .box .box-footer .box-toolbar > li.toolbar-link > a{
	border-left:0;
	border-right:1px solid #CECECE;
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
					<span>申请日期：</span><input type="text" placeholder="" name="apply_time" style="width:200px;margin:0 10px 0" class="form-time">
					<span>审核状态：</span><select class="uniform" name="apply_status">
                                            <option  selected="selected" value="">全部</option>
						<?php foreach($apply_status as $key => $value):?>
							<option value="<?php echo $key;?>" <?php if ($get['apply_status'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value;?></option>
						<?php endforeach;?>
					</select>
					<span>退款状态：</span><select class="uniform" name="refund_status">
                                            <option  selected="selected" value="">全部</option>
						<?php foreach($refund_status as $k => $v):?>
							<option value="<?php echo $k;?>" <?php if ($get['refund_status'] == $k): ?>selected="selected"<?php endif; ?>><?php echo $v;?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="row-fluid" style="margin-bottom:10px;">
					<span>退款申请机构：</span><input type="text" placeholder="" name="apply_name" value="<?php echo $get['apply_name'] ?>" style="width:200px;margin:0 10px 0">
					<span>一级票务名称：</span><input type="text" placeholder="" name="landscape_name" value="<?php echo $get['landscape_name'] ?>" style="width:200px;margin:0 10px 0">
					<span>退款申请单号：</span><input type="text" placeholder="" name="refund_apply_id" value="<?php echo $get['refund_apply_id'] ?>" style="width:200px;margin:0 10px 0">
				</div>
				<div class="row-fluid" style="margin-bottom:10px;">
                                    <span>订单号：</span><input type="text" placeholder="" name="order_id" value="<?php echo $get['order_id'] ?>" style="width:200px;margin:0 10px 0">
				  <button class="btn btn-default" style="float:none;">搜索</button>
				</div>
			  </form>
            </div>
        <form action="refund_prefund.html" method="post" onsubmit="return validate();">
		<div class="box-header">
	        <ul class="box-toolbar">
	          <li class="toolbar-link">
	            <button class="btn btn-blue" type="submit">退款</button>
	          </li>
	        </ul>
		</div>
			<div class="content">
			<table class="table table-normal order-list" id="tickets-use">
				<thead>
				  <tr>
						<td class="icon"><button id="allcheck" class="btn btn-default" style="min-width:60px">全选</button></td>
						<td>退款申请单号</td>
						<td>退款申请机构</td>
						<td>申请日期</td>
						<td>订单号</td>
						<td>订单状态</td>
						<td>门票名称</td>
						<td>门票类型</td>
						<td>允许退票</td>
						<td>供应商名称</td>
						<td>支付方式</td>
						<td>供应价</td>
						<td>数量</td>
						<td>小计</td>
						<td>审核状态</td>
						<td>审核日期</td>
						<td>退款状态</td>
						<td>退款日期</td>
						<td>退款理由</td>
						<td>操作</td>
				  </tr>
				</thead>
				<tbody>
					<?php if($refundApplys):?>
					<?php foreach($refundApplys as $ra):?>
                                   
				    <tr>
						<td class="icon">
							<?php if($ra['status'] == 'checked'):?>
							<input type="checkbox" class="icheck" value="<?php echo $ra['id']?>" name="apply_ids[]">
							<?php endif;?>
						</td>
						<td>
							<a class="underline" href="refund_record_<?php echo $ra['id'];?>.html" ><?php echo $ra['id'];?></a>
						</td>
						<td><?php echo $ra['apply_name'];?></td>
						<td><?php echo $ra['created_at']?></td>
						<td><?php echo $ra['order_id'];?></td>
						<td><?php echo OrderCommon::getOrderStatus($ra['order_status']);?></td>
						<td><?php echo $ra['landscape_name'];?></td>
						<td><?php echo $ra['ticket_type'];?></td>
						<td><?php echo $ra['allow_back'] == 'no' ? '否' : '是';?></td>
						<td><?php echo $ra['supply_name'];?></td>
						<td><?php echo OrderCommon::getPayments($ra['payment']);?></td>
						<td><?php echo $ra['price'];?></td>
						<td><?php echo $ra['ticket_nums']?></td>
						<td><?php echo floatval($ra['price']*$ra['ticket_nums']);?></td>
						<td><?php echo RefundApplyCommon::getRefundApplyStatus($ra['status']);?></td>
						<td><?php echo $ra['audited_at'];?></td>
						<td><?php echo $ra['refund_status'] ?  RefundsCommon::getRefundsStatus($ra['refund_status']) : '';?></td>
						<td><?php echo $ra['refund_at'] ?  $ra['refund_at']: '';?></td>
						<td>
						<a data-original-title="Popover on bottom" class="remarkPopover" href="#" data-toggle="popover" data-placement="bottom" data-content="<?php echo $ra['remark']?$ra['remark']:""; ?>" title="退款/阻止退款理由"><?php echo msubstr($ra['remark']?$ra['remark']:"",20);?></a>
						</td>
						<td>
							<?php if($ra['status'] != 'refunded' && $ra['status'] != 'checked'):?>
								<a style="color:white;" onclick="verify('<?php echo $ra['id'];?>')" class="btn refundVerify btn-blue">审核通过</a>
							<?php endif;?>
							<?php if($ra['status'] != 'refunded' && $ra['status'] != 'reject'):?>
								<a style="color:white;" onclick="reject('<?php echo $ra['id'];?>')" class="btn refundVerify btn-blue">驳回</a>
							<?php endif;?>
							<?php if($ra['status'] == 'checked'):?>
								<a class="btn btn-blue" style="color:white;" onclick="prefund('<?php echo $ra['id'];?>')" >退款</a>
						    <?php endif;?>
						</td>
				    </tr>
				    <?php endforeach;?>
				    <?php else:?>
				   		<tr><td colspan="20">无退票记录</td></tr>
				    <?php endif;?>
				</tbody>
			</table>
            </div>
			</form>
			
			<div class="table-footer">
			    <div class="dataTables_paginate paging_full_numbers">
					<?php echo $pagination;?>
				</div>
			</div>
			
		</div>
	</div>
</div>

<link href="Views/css/daterangepicker.css" rel="stylesheet">
<script src="Views/js/vendor/date.js"></script>
<script src="Views/js/vendor/moment.js"></script>
<script src="Views/js/vendor/daterangepicker.js"></script>
<script src="Views/js/jquery.slimscroll.min.js"></script>
<script src="Views/js/refund/verify.js"></script>
<script>
    $('.remarkPopover').click(function(){
		$(this).popover('show');
	});
</script>
</body>
</html>