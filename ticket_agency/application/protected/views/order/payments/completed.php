<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/25/14
 * Time: 9:02 PM
 */
$this->breadcrumbs = array('订单', '支付完成');
?>
<div class="contentpanel contentpanel-wizard">
	<div class="row">
		<div class="col-md-12">
			<form id="valWizard" action="">
				<ul class="nav nav-justified nav-wizard nav-disabled-click nav-pills">
					<li><a href="#tab1-4" data-toggle="tab"><strong>Step 1:</strong> 提交订单</a></li>
					<li><a href="#tab2-4" data-toggle="tab"><strong>Step 2:</strong> 选择支付方式</a></li>
					<li class="active"><a href="#tab3-4" data-toggle="tab"><strong>Step 3:</strong> 支付完成</a></li>
				</ul>
			</form>

			<div class="tab-content" style="padding: 10px">
				<h3 style="text-align: center;color: #006600">支付<?php echo $status_labels[$status]?>!</h3>
				<small style="text-align: center">支付单号：<?php echo $pid?></small>

				<form action="/order/history" method="post" class="panel-wizard">
					<ul class="list-unstyled wizard">
						<li class="pull-left previous disabled hide">
							<button type="button" class="btn btn-default">返回修改</button>
						</li>
						<li class="pull-right next hide">
							<button type="submit" class="btn btn-primary">去支付</button>
						</li>
						<li class="pull-right finish ">
							<a href="/order/history" class="btn btn-primary">完成</a>
						</li>
					</ul>
				</form>
			</div>
		</div>
	</div>
</div>
<script src="/js/bootstrap-wizard.min.js"></script>
<script>
	jQuery(document).ready(function () {
		jQuery('#valWizard').bootstrapWizard({
			onTabClick: function(tab, navigation, index) {
				return false;
			},
			onNext: function(tab, navigation, index) {
				return false;
			}
		});
	});
</script>
