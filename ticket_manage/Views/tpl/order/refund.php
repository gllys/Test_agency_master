<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h6 id="modal-formLabel">退票</h6>
	</div>

	<div id="model_show_msg"></div>

	<form action="index.php?c=order&a=doRefundApply" method="post" id="refund-apply-form">
	<div class="modal-body select">
		<div class="container-fluid">
			<div class="box">
				<table class="table table-normal">
					<thead>
					  <tr>
						<td>订单号</td>
						<td>电子编码号</td>
						<td>产品名称</td>
						<td>取票人</td>
						<td>联系电话</td>
						<td>订购数量</td>
						<td>支付方式</td>
						<td>订单状态</td>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<td><?php echo $orderInfo['id'];?></td>
						<td><?php echo $orderInfo['hash'];?></td>
						<td><?php echo $orderInfo['landscape']['name']?>-<?php echo $orderInfo['order_item'][0]['name'];?></td>
						<td><?php echo $orderInfo['owner_name'];?></td>
						<td><?php echo $orderInfo['owner_mobile'];?></td>
						<td><?php echo $orderInfo['nums'];?></td>
						<td><?php echo OrderCommon::getPayments($orderInfo['payment']);?></td>
						<td><?php echo OrderCommon::getOrderRealShowStatus($orderInfo['status'], $orderInfo['pay_status']);?></td>
					  </tr>
					</tbody>
				</table>
			</div>
				<table class="table table-normal">
				<tr>
					<td><b>已使用数量:</b></td>
					<td><?php echo $orderInfo['used_nums'] ? $orderInfo['used_nums'] : 0;?></td>
					<td><b>已退款数量:</b></td>
					<td><?php echo $orderInfo['refunded_nums'] ? $orderInfo['refunded_nums'] : 0;?></td>
				</tr>
				<tr>
					<td><b>退票审核中数量:</b></td>
					<td><?php echo $orderInfo['apply_nums'] ? $orderInfo['apply_nums'] : 0;?> </td>
					<td><b>退票审核通过数量:</b></td>
					<td><?php echo $orderInfo['checked_nums'] ? $orderInfo['checked_nums'] : 0;?></td>
				</tr>
				<tr>
					<td colspan="2">
					<?php if(!$refund_illegal):?>
					申请退票数量: <input type="text" id="refund_apply_num" value="<?php echo $refundValidNum;?>" name="refund_apply_num" max="<?php echo $refundValidNum;?>" style="width:100px;margin-left:10px;">
					<?php else:?>
						<?php echo $refund_illegal;?>
					<?php endif;?>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php if(!$refund_illegal):?>
	<div class="modal-footer">
		<input type="hidden" name="order_id" value="<?php echo $orderInfo['id'];?>">
		<input type="hidden" id='refundValidNum' value="<?php echo $refundValidNum;?>" />
		<button class="btn btn-green" type="button" id="refund-apply-button">申请退票</button>
	</div>
	<script src="Views/js/order/refund_apply.js"></script>
	<script src="Views/js/common/common.js"></script>
	<script src="Views/js/plugins/jquery.form.js"></script>
	<?php endif;?>
	</form>