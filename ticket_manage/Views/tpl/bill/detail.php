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
					width:100px;
				}
				.btn-default{
					margin-right:10px;
				}
			</style>
			<div class="container-fluid padded">
				<div class="box">
					<div class="box-header">
						<span class="title"><i class="icon-list-ol"></i> 账款单明细</span>
					</div>

					<div class="table-header" style="height:auto;padding-bottom:10px;">
						<div class="row-fluid" style="margin-bottom:10px;">
							<span style="margin-right:50px">账单日期：<?php echo $billInfo['created_at'];?></span>	<span>账款单支付状态：<?php if($billInfo['pay_status'] == '1' && $billInfo['order_list'][0]['bill_amount'] > 0):?><span class="label label-green">已打款</span> <?php elseif($billInfo['order_list'][0]['bill_amount'] == 0): ?><span class="label">无需打款</span><?php else:?><span class="label label-red">未打款</span><?php endif;?></span>
						</div>
					</div>

					<div class="content" style="border-bottom:1px solid #ccc">
					<table class="table table-normal">
						<thead>
							<tr>
							<td>订单号</td>
							<td>分销商</td>
							<td>产品名称</td>
							<td>创建订单日期</td>
							<td>游玩日期</td>
							<td>取票人</td>
							<td>取票人手机</td>
							<td>支付金额</td>
							<td>退款金额</td>
							<td>结款金额</td>
							</tr>
						</thead>
						<tbody>
							<?php if($billInfo['order_list']):?>
								<?php foreach($billInfo['order_list'] as $orderInfo):?>
									<tr>
										<td><?php echo $orderInfo['order_id'];?></td>
										<td><?php echo $orderInfo['agency_name'];?></td>
										<td><?php echo $orderInfo['ticket_name'];?></td>
										<td><?php echo $orderInfo['ordered_at'];?></td>
										<td><?php echo $orderInfo['use_day'];?></td>
										<td><?php echo $orderInfo['owner_name'];?></td>
										<td><?php echo $orderInfo['owner_mobile'];?></td>
										<td><?php echo $orderInfo['payed'];?></td>
                                        <td><?php echo $orderInfo['refunded'];?></td>
                                        <td><?php echo $orderInfo['bill_amount'];?></td>
									</tr>
								<?php endforeach;?>
							<?php endif;?>
						</tbody>
					</table>
					</div>

					<table class="table table-normal">
						<tbody>
						<tr><th width="100">交易信用总额:</th><td><?php echo $billInfo['bill_amount'];?></td></tr>
						<tr <?php if($billInfo['order_list'][0]['bill_amount'] == 0 || (isset($supply) && $supply == 1)){ echo "style='display:none'";}?>>
							<th>打款凭证:</th>
							<td>
							<?php if($billInfo['payed_img']):?>
								<a href="<?php echo $billInfo['payed_img'];?>" class="editable-empty thumbs">
									<img src="<?php echo $billInfo['payed_img'];?>" height="100" width="100"/>
								</a>
							<?php endif;?>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<script type="text/javascript">
		$(document).ready(function(){
			$('.thumbs').touchTouch();
		});
		</script>
	</body>
</html>