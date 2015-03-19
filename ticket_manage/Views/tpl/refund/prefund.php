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
<div id="show_msg">
<?php if (!empty($get['errmsg'])):?>
<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><?php echo $get['errmsg'];?></div>
<?php elseif (!empty($get['succmsg'])):?>
<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><?php echo $get['succmsg'];?></div>
<?php endif;?>
</div>
	
<style>
.table-normal button{
	min-width:inherit;
	}
.table-normal td{
	width:11%;
	text-align:center;
	}
.table-normal tbody .title{
	text-align:left;
	background:#ddd
	}
.table-normal tbody .title span{
	margin:0 10px;
	}
.table-footer{
	border:0
	}
.padded{
	padding:0 15px
	}
#model_show_msg {
    padding:5px;
}
</style>
	<div class="container-fluid padded">
		<div class="box">
			<div class="box-header">
				<span class="title">退款</span>
			</div>
			<table class="table table-normal">
				<thead>
				  <tr>
						<td>退款单号</td>
						<td>退款申请单号</td>
						<td>商品名称</td>
						<td>供应商名称</td>
						<td>供应价</td>
						<td>数量</td>
						<td>退款金额</td>
						<td>退款状态</td>
						<td>操作</td>
				  </tr>
				</thead>
			</table>
		</div>
		<?php if($refundList):?>
		<?php foreach($refundList as $rl):?>
		<div class="box">
			<table class="table table-normal">
			  <tr>
				<td colspan="9" class="title">
					<span class="label label-green"><?php echo $rl['apply_name'];?></span>
					<!-- <span>收款账号：</span><span>支付宝：abc@163.com</span> -->
				</td>
			  </tr>
			<tbody>
			  <tr>
					<td><?php echo $rl['id'];?></td>
					<td><?php echo $rl['refund_apply_id'];?></td>
					<td><?php echo $rl['landscape_name'].'('.$rl['ticket_name'].')';?></td>
					<td><?php echo $rl['supply_name'];?></td>
					<td><?php echo $rl['refund_price'];?></td>
					<td><?php echo $rl['ticket_nums'];?></td>
					<td><?php echo $rl['money'];?></td>
					<td><?php echo RefundsCommon::getRefundsStatus($rl['status']);?></td>
					<td>
						<?php if($rl['status'] != 'succ'):?>
							<button class="btn btn-lg btn-green" onclick="doRefund('<?php echo $rl['refund_apply_id']?>')">支付</button>
						<?php endif;?>
					</td>
			  </tr>
			</tbody>
			</table>
		</div>
		<?php endforeach;?>
		<?php else:?>
			<div class="box">没有可退款单</div>
		<?php endif;?>
		<!-- <div class="box">
			<table class="table table-normal">
			  <tr>
				<td colspan="9" class="title">
					<span class="label label-green">ABC旅行社</span><span>收款账号：</span><span>支付宝：abc@163.com</span>
				</td>
			  </tr>
			<tbody>
			  <tr>
					<td>43254325432532</td>
					<td>65434546576768</td>
					<td>上海东方明珠旋转餐厅自助晚餐（空中欢乐圣诞夜 赠圣诞神秘礼物1份）</td>
					<td>卡卡罗特集团公司</td>
					<td>748</td>
					<td>2</td>
					<td>1</td>
					<td>支付失败</td>
					<td></td>
			  </tr>
			  <tr>
					<td>43254325432532</td>
					<td>65434546576768</td>
					<td>上海东方明珠旋转餐厅自助晚餐（空中欢乐圣诞夜 赠圣诞神秘礼物1份）</td>
					<td>卡卡罗特集团公司</td>
					<td>748</td>
					<td>2</td>
					<td>1</td>
					<td>准备中</td>
					<td><button class="btn btn-lg btn-green">支付</button></td>
			  </tr>
			</tbody>
			</table>
		</div> -->

	</div>
	<div class="container-fluid padded">
		<div class="box">
			<div class="table-footer">
			 	<div class="dataTables_paginate paging_full_numbers">
					<?php echo $pagination;?>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="msgModal" class="modal hide fade in" aria-hidden="false">
    <div class="modal-header">
        <h6 id="modal-formLabel">提示消息</h6>
    </div>
    <div id="model_show_msg"><img src="/Views/images/select2-spinner.gif"/>&nbsp;&nbsp;<span id="modal_msg">正在提交请求，请稍等。。。</span></div>
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

//确定退款
function doRefund(refund_apply_id)
{
    $("#msgModal").modal('show');
	location.href = "refund_doRefund_"+refund_apply_id+'.html';
}
</script>
</body>
</html>