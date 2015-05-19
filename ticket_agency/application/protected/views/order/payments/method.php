<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/25/14
 * Time: 9:02 PM
 */
$this->breadcrumbs = array('订单', '支付订单');
$is_limit          = false; //库存限制
?>

<div class="contentpanel contentpanel-wizard">
	<div class="row">
		<div class="col-md-12">
			<form id="valWizard" action="">
				<ul class="nav nav-justified nav-wizard nav-disabled-click">
					<li><a href="javascript:void(0);" data-toggle="tab"><strong>Step 1:</strong> 提交订单</a></li>
					<li class="active"><a href="#tab2-4" data-toggle="tab"><strong>Step 2:</strong> 选择支付方式</a></li>
					<li><a href="javascript:void(0);" data-toggle="tab"><strong>Step 3:</strong> 支付完成</a></li>
				</ul>
			</form>
			<?php if (isset($orders) && !empty($orders)): ?>
				<div class="tab-content">
					<div class="table-responsive">
						<table class="table table-striped mb30">
							<thead>
							<tr>
								<th colspan="5">电子票</th>
							</tr>
							<tr>
								<th style="text-align: center">订单号</th>
								<th style="width: 55%">门票</th>
								<th style="text-align: left">游玩日期</th>
								<th style="text-align: left">张数</th>
								<th style="text-align: right">订单总价</th>
							</tr>
							</thead>
							<tbody>
							<?php $amount = 0;
							foreach ($orders as $order) : $amount += $order['amount'];
								?>
								<tr>
									<td style="text-align: center"><?php echo $order['id'] ?></td>
									<td><?php echo $order['name'] ?></td>
									<td style="text-align: left"><?php echo $order['use_day'] ?></td>
									<td style="text-align: left"><?php
										echo "<strong style=\"color:black\">{$order['nums']}</strong>";
										$k = "{$order['ticket_template_id']}_{$order['use_day']}";
										if (array_key_exists($k, $storage) && isset($storage[$k]['remain_reserve']) && !is_null($storage[$k]['remain_reserve'])) {
											echo $storage[$k]['remain_reserve'] >= $order['nums'] ? '（库存：' . $storage[$k]['remain_reserve'] . '）' : '<span style="color:darkred">（库存不足！）</span>';
											$storage[$k]['remain_reserve'] -= $order['nums'];
											$is_limit |= $storage[$k]['remain_reserve'] < 0;
										}
										else {
											echo '（库存：不限）';
										}
										?></td>
									<td style="text-align: right"><?php echo $order['amount'] ?></td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			<?php endif; ?>
			<?php if (isset($renwus) && !empty($renwus)): ?>
				<div class="tab-content">
					<div class="table-responsive">
						<table class="table table-striped mb30">
							<thead>
							<tr>
								<th colspan="5">任务单 <span class="text-danger">注意：任务单无需支付，不计算入总金额内，请前往任务单管理进行确认</span>
								</th>
							</tr>
							<tr>
								<th style="text-align: center">订单号</th>
								<th style="width: 55%">门票</th>
								<th style="text-align: center">游玩日期</th>
								<th style="text-align: center">张数</th>
								<th style="text-align: right">订单总价</th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ($renwus as $renwu): ?>
								<tr>
									<td style="text-align: center"><?php echo $renwu['id'] ?></td>
									<td><?php echo $renwu['name'] ?></td>
									<td style="text-align: center"><?php echo $renwu['use_day'] ?></td>
									<td style="text-align: center"><?php
										echo "<strong style=\"color:black\">{$renwu['nums']}</strong>";
										$k = "{$renwu['ticket_template_id']}_{$renwu['use_day']}";
										if (array_key_exists($k, $storage) && !is_null($storage[$k]['remain_reserve'])) {
											echo $storage[$k]['remain_reserve'] >= $renwu['nums'] ? '（库存：' . $storage[$k]['remain_reserve'] . '）' : '<span style="color:darkred">（库存不足！）</span>';
											$storage[$k]['remain_reserve'] -= $renwu['nums'];
											$is_limit |= $storage[$k]['remain_reserve'] < 0;
										}
										else {
											echo '（库存：不限）';
										}
										?></td>
									<td style="text-align: right"><?php echo $renwu['amount'] ?></td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			<?php endif; ?>
			<br/>

			<div class="row" style="padding: 10px ">
				<form action="/order/payments/prepay/" method="post" id="valWizard" class="panel-wizard form-inline"
				      novalidate="novalidate" target="_blank">
					<?php if (isset($amount)) : if ($amount < 0) {
						$amount = 0;
					} ?>
						<div class="panel" style="text-align: right">
							<div class="form-group hide">
								<input name="method" id="pay1" type="radio" value="alipay" disabled="disabled"/>
								<label for="pay1">支付宝</label>
							</div>
							<div class="form-group">
								<input name="method" id="pay2" type="radio"
								       value="kuaiqian" <?php echo $only_one_supplier ? 'checked' : 'checked' ?>/>
								<label for="pay2" style="max-width:inherit">快钱支付</label>
							</div>
							<div class="form-group">
								<input name="method" id="pay3" type="radio" value="union_4"/>
								<label for="pay3" style="max-width:inherit">平台支付( 剩余资金: <?php  echo $unionmoney ? $unionmoney: '0.00'; ?> )</label>
							</div>
							<?php if ($only_one_supplier) :
								if (isset($credit_money) && !is_null($credit_money)) :
									?>
									<div class="form-group">
										<input name="method" id="pay4" type="radio"
										       value="credit_0" <?php echo $credit_money >= $amount || $credit_money == 'infinite' ? '' : 'disabled="disabled"' ?>/>
										<label for="pay4" 
										       style="max-width:inherit">信用支付（信用额度：<?php echo $credit_money != 'infinite' ? number_format($credit_money, 2) : '无限' ?>
											）</label>
									</div>
								<?php endif;
								if (isset($balance_money) && !is_null($balance_money)) :
									?>
									<div class="form-group">
										<input name="method" id="pay5" type="radio"
										       value="credit_1" <?php echo $balance_money >= $amount || $balance_money == 'infinite' ? '' : 'disabled="disabled"' ?>/>
										<label for="pay5"
										        style="max-width:inherit">储值支付（储值额度：<?php echo $balance_money != 'infinite' ? number_format($balance_money, 2) : '无限' ?>
											）</label>
									</div>
								<?php endif;
							endif;
							?>
						</div>
						<div class="panel">
							<h5 class="lg-title mb5" style="text-align: right">
								<strong
									style="font-size: 1.4em;color: #dd1144">合计：<?php echo number_format($amount, 2) ?>
									元</strong>
								<?php if (time() < strtotime('2014-11-01 01:01:01')) : ?>
									<br/>
									<small>为了测试，2014-11-01 01:01:01之前，实付金额为1分钱</small>
								<?php endif; ?>
							</h5>
						</div>
					<?php endif; ?>

					<div class="panel" style="padding: 0;margin: 0">
						<input type="hidden" name="combine" value="<?php echo $e_order_ids ?>"/>

						<ul class="list-unstyled wizard">
							<li class="pull-left previous disabled" style="display: none">
								<button type="button" class="btn btn-default">返回修改</button>
							</li>
							<?php if (isset($renwus) && empty($orders)): ?>
								<li class="pull-right next">
									<a href="/order/renwu" class="btn btn-primary">去确认</a>
								</li>
							<?php elseif (!$is_limit): ?>
								<li class="pull-right next">
									<button id="btn_pay" type="submit" class="btn btn-primary">去支付</button>
								</li>
							<?php endif; ?>
							<li class="pull-right finish hide">
								<button type="submit" class="btn btn-primary">Finish</button>
							</li>
						</ul>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>
<div id="lock_layer" class="unlocked" style="display: none">
	<div class="lockedpanel">
		<div class="loginuser">
			<img src="/img/logo.png" width="200" class="img-circleimg-online" alt=""/>
		</div>
		<div class="logged">
			<h3 style="color:darkred">金额:<?php echo isset($amount) ? number_format($amount, 2) : 0 ?>元</h3>
			<strong class="text-muted">支付中，请勿关闭页面</strong>
		</div>
		<form id="unlock" method="post" class="form-inline" action="">
			<div class="form-group">
				<button class="btn btn-success">支付成功</button>
			</div>
			<div class="form-group" style="margin-right: 0">
				<button class="btn btn-fail">支付失败</button>
			</div>
			<!-- input-group -->
		</form>
	</div>
	<!-- lockedpanel -->
</div>
<!-- locked -->
<script src="/js/bootstrap-wizard.min.js"></script>
<script>
	jQuery(document).ready(function () {
		jQuery('#valWizard').bootstrapWizard({
			onTabClick: function (tab, navigation, index) {
				return false;
			},
			onNext: function (tab, navigation, index) {
				return false;
			}
		});
		jQuery('#btn_pay').click(function () {
			jQuery('#lock_layer').removeClass('unlocked');
			jQuery('#lock_layer').addClass('locked');
			jQuery('#lock_layer').show();
		});

		jQuery('#unlock').submit(function () {
			$('.locked').fadeOut(function () {
				$(this).remove();
				location.reload();
			});
			return false;
		});

		setTimeout(function () {
			var pay_state = setInterval(function () {
				if (!$('#lock_layer').hasClass('locked')) {
					return;
				}
				$.get('/order/payments/state/', {order:'<?php echo $e_order_ids ?>'}, function (result) {
					if (result > 0) {
						clearInterval(pay_state);
						location.href = '/order/payments/completed/id/' + result;
					}
				});
			}, 5000);
		}, 0);
	});
</script>
