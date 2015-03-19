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
			<div class="table-header" style="height:auto;padding-bottom:10px;">
			  <form action="">
				<div class="row-fluid" style="margin-bottom:10px;">
					退款支付日期：<input type="text" placeholder="" name="refunds_time" style="width:200px;margin:0 10px 0" value="<?php echo $get['refunds_time'];?>" class="form-time">
					申请机构：<input type="text" placeholder="" name="apply_name" style="width:200px;margin:0 10px 0" value="<?php echo $get['apply_name'];?>">
					供应商名称：<input type="text" placeholder="" name="organization_name" style="width:200px;margin:0 10px 0" value="<?php echo $get['organization_name'];?>" >
				</div>
				<div class="row-fluid" style="margin-bottom:10px;">
					退款申请单号：<input type="text" placeholder="" name="refund_apply_id" value="<?php echo $get['refund_apply_id'];?>" style="width:200px;margin:0 10px 0">
					退款单号：<input type="text" placeholder="" name="refund_id" style="width:200px;margin:0 10px 0" value="<?php echo $get['refund_id'];?>">
				  <button class="btn btn-default" style="float:none;">搜索</button>
				</div>
			  </form>
            </div>

			<div class="content">
			<table class="table table-normal order-list">
				<thead>
				  <tr>
						<td>退款单号</td>
						<td>退款申请单号</td>
						<td>退款申请机构</td>
						<td>金额</td>
						<td>收款账号</td>
						<td>收款凭证</td>
						<td>退款支付状态</td>
						<td>退款支付时间</td>
				  </tr>
				</thead>
				<tbody>
					<?php if($data):?>
						<?php foreach($data as $refunds):?>
							<tr>
								<td><?php echo $refunds['id'];?></td>
								<td><?php echo $refunds['refund_apply_id'];?></td>
								<td><?php echo $refunds['og_name'];?></td>
								<td><?php echo $refunds['money'];?></td>
								<td><?php echo $refunds['account'].$refunds['bank'];?></td>
								<td><?php echo $refunds['payment_bn'];?></td>
								<td><?php echo RefundsCommon::getRefundsStatus($refunds['status']);?></td>
								<td><?php echo $refunds['created_at'];?></td>
							</tr>
						<?php endforeach;?>
					<?php else: ?>
						<tr clospan="8">
							<td>无记录</td>
						</tr>
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