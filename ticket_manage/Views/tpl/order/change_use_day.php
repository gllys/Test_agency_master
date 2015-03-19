		
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h6 id="modal-formLabel">改期</h6>
	</div>
	
	<div id="change_show_msg"></div>
	
	<form id="change-form">
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
							<td>支付状态</td>
						  </tr>
						</thead>
						<tbody>
						    <?php if($orderInfo):?>
								<tr>
									<td><?php echo $orderInfo['id'];?></td>
									<td><?php echo $orderInfo['hash'];?></td>
									<td><?php echo $orderInfo['ticket']['name'];?></td>
									<td><?php echo $orderInfo['owner_name'];?></td>
									<td><?php echo $orderInfo['owner_mobile']?></td>
									<td><?php echo $orderInfo['nums'];?></td>
									<td><?php echo OrderCommon::getPayments($orderInfo['payment']);?></td>
									<td><?php echo OrderCommon::getOrderRealShowStatus($orderInfo['status'], $orderInfo['pay_status']);?></td>
									<td><?php echo OrderCommon::getOrderPayStatus($orderInfo['pay_status']);?></td>
								</tr>
							<?php endif;?>
						</tbody>
					</table>

				</div>
					<table class="table table-normal">
					<tr>
						<td><b>游玩时间：</b> <?php echo $orderInfo['useday'];?></td>
						<td><b>结束时间：</b> <?php echo $orderInfo['ticket']['expire_end_at'];?></td>
					</tr>
					<tr>
						<td colspan="2">
							改期时间： 
							<input type="text" name="changeTo" readonly="readonly" style="width:40%;margin:0 20px 10px" class="input-append FX-date validate[required,custom[date]" data-prompt-position="topLeft" value="<?php echo $orderInfo['useday'];?>">
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="modal-footer">
			<input type="hidden" name="order_id" value="<?php echo $orderInfo['id'];?>"/>
			<button class="btn btn-green" type="button" id="change-form-button">改期</button>
		</div>

	</form>

		<script>
			var phpvars = {};
			phpvars.expire_start_at = "<?php echo strtotime($orderInfo['ticket']['expire_start_at'])*1000;?>";
			phpvars.expire_end_at   = "<?php echo strtotime($orderInfo['ticket']['expire_end_at'])*1000;?>";
			phpvars.weekly          = "<?php echo $orderInfo['ticket']['weekly'];?>";
			phpvars.sale_price      = "<?php echo $orderInfo['ticket']['price'];?>";
			phpvars.reserve         = "<?php echo $orderInfo['ticket']['reserve'] ? $ticketInfo['reserve'] : 0;?>";
			phpvars.now_time        = "<?php echo strtotime(date('Y-m-d', time()))*1000;?>";
			phpvars.useday          = "<?php echo $orderInfo['useday'];?>";
		</script>
		<link href="Views/css/daterangepicker.css" rel="stylesheet">
		<script src="Views/js/vendor/date.js"></script>
		<script src="Views/js/vendor/moment.js"></script>
		<script src="Views/js/vendor/daterangepicker.js"></script>
		<script src="Views/js/common/common.js"></script>
		<script src="Views/js/order/change_use_day.js"></script>