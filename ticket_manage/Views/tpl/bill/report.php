<?php
$type_names = array('电子票', '任务单');
$payment_types = array('cash' => '现金','offline' => '线下','credit'=>'信用支付','advance' =>'储值支付','union'=>'平台支付','alipay'=>'支付宝','kuaiqian'=>'快钱');
$status_labels = array('unpaid'=>'未支付','cancel' => '已取消','paid' => '已付款','finish' => '已结束','billed' => '已结款');
?>
<!DOCTYPE html>
<html>
<?php get_header(); ?>
<body>
<style type="text/css">
	.lel {margin-left: 0;line-height: 30px;text-align: right}
</style>
<?php get_top_nav(); ?>
<div class="sidebar-background">
	<div class="primary-sidebar-background"></div>
</div>
<?php get_menu(); ?>
<div class="main-content">
	<?php get_crumbs(); ?>

	<div id="show_msg"></div>

	<div class="container-fluid padded">
		<div class="box">
			<div class="box-header">
				<span class="title">交易报表</span>
			</div>
			<div class="table-header" style="height: auto">
				<form id="filter_form" action="/bill_report.html" method="get" style="padding-left: 20px">
					<div class="row">
						<div class="span1 lel" style="width: 35px;margin-right: 5px">
							<button class="btn month-prev" type="button">
								<i class="icon-chevron-left"></i>
							</button>
						</div>
						<div class="span3" style="margin-left: 0;width: 170px">
							<input type="text" name="date[]" class="date" value="<?php echo $get['date'][0] ?>"
							       style="width: 71px" id="ym1"/>
							- <input type="text" name="date[]" class="date" value="<?php echo $get['date'][1] ?>"
							         style="width: 71px" id="ym2"/>
						</div>
						<div class="span1 lel" style="width: 35px">
							<button class="btn month-next" type="button">
								<i class="icon-chevron-right"></i>
							</button>
						</div>
						<div class="span1 lel hide" style="width: 60px">报表类型：</div>
						<div class="span1 lel hide" style="width: 130px;margin-right: 5px">
							<select name="type" id="type">
								<option value=""></option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="span1 lel" style="width: 100px;margin-right: 5px">
							<select name="field" id="field">
								<option value="id">订单号</option>
							</select>
						</div>
						<div class="span2" style="margin-left: 0;width: 160px">
							<input type="text" name="val" style="width: 150px"/>
						</div>
						<div class="span1 lel hide" style="width: 60px">支付方式：</div>
						<div class="span1 lel hide" style="width: 130px;margin-right: 5px">
							<select name="method" id="method">
								<option>全部</option>
								<?php foreach($payment_types as $type => $name):?>
									<option value="<?php echo $type?>"><?php echo $name?></option>
								<?php endforeach;?>
							</select>
						</div>
						<div class="span1"><button class="btn btn-green">查询</button></div>
						<button id="btn_gen" type="button" class="btn btn-small btn-blue">导出报表（Excel）</button>
					</div>
				</form>
			</div>
			<div class="content" style="overflow-x: auto">
				<table class="table table-normal table-hover" style="width: 2000px !important;max-width: none !important;">
					<thead>
					<tr>
						<th>订单号</th>
						<th>机构名称</th>
						<th>门票名称</th>
						<th>景区</th>
						<th>供应商</th>
						<th>预订时间</th>
						<th>支付时间</th>
						<th>游玩时间</th>
						<th>张数</th>
						<th>单价</th>
						<th>结算金额</th>
						<th>订单类型</th>
						<th>支付方式</th>
                                                <th>支付金额</th>
						<th>退款金额</th>
						<th>结款金额</th>
						<th>订单状态</th>
					</tr>
					</thead>
					<tbody>
					<?php
					if (isset($lists)) {
						$ticket_ids = array();
						foreach ($lists as $bill) {
							$ticket_ids[] = $bill['ticket_template_id'];
							$bill['created_at'] = $bill['created_at'] > 0
								? date('Y-m-d H:i', $bill['created_at'])
								: '';
							$bill['pay_at'] = $bill['pay_at'] > 0
								? date('Y-m-d H:i', $bill['pay_at'])
								: '';
							?>
							<tr>
								<td><?php echo $bill['id']?></td>
								<td><?php echo $bill['distributor_name']?></td>
								<td><?php echo $bill['name']?></td>
								<td><?php 
                                                                        $result = Landscape::api()->lists(array("ids" => $bill['landscape_ids']));
                                                                        $landspaceInfo = ApiModel::getLists($result);
                                                                        foreach ($landspaceInfo as $value) {   
                                                                                echo  $value['name'].'&nbsp;&nbsp;&nbsp;&nbsp;';
                                                                        }
                                                                ?></td>
								<td><?php echo $bill['supplier_name']?></td>
								<td><?php echo $bill['created_at']?></td>
								<td><?php echo $bill['pay_at']?></td>
								<td><?php echo $bill['use_day']?></td>
								<td><?php echo $bill['nums']?></td>
								<td><?php echo number_format($bill['amount'] / $bill['nums'], 2)?></td>
								<td><?php echo $bill['amount']?></td>
								<td><?php echo $type_names[$bill['type']]?></td>
								<td><?php echo $payment_types[$bill['payment']]?></td>
								<td><?php echo $bill['payed']?></td>
								<td><?php echo $bill['refunded']?></td>
								<td><?php echo $bill['payed'] - $bill['refunded']?></td>
								<td><?php echo $status_labels[$bill['status']]?></td>
							</tr>
						<?php }
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="dataTables_paginate paging_full_numbers">
				<?php echo isset($pagination) ? $pagination : '';?>
			</div>
		</div>
	</div>
</div>
<script>
	$(function(){
		$('button.month-prev').bind('click', function() {
			var origin = $('#ym1').val();
			origin = origin.split('-');
			var year = Math.floor(origin[0]);
			var month = Math.floor(origin[1]);
			year = month > 1 ? year : year - 1;
			month = month > 1 ? month - 1 : 12;
			var days = new Date(year,month,0).getDate();//当月天数
			month = month < 10 ? '0' + month : month;
			days = days < 10 ? '0' + days : days;
			$('#ym1').val(year + '-' + month + '-01');
			$('#ym2').val(year + '-' + month + '-' + days);
		});
		$('button.month-next').bind('click', function() {
			var origin = $('#ym1').val();
			origin = origin.split('-');
			var year = Math.floor(origin[0]);
			var month = Math.floor(origin[1]);
			year = month < 12 ? year : year + 1;
			month = month < 12 ? month + 1 : 1;
			var days = new Date(year,month,0).getDate();
			month = month < 10 ? '0' + month : month;
			days = days < 10 ? '0' + days : days;
			$('#ym1').val(year + '-' + month + '-01');
			$('#ym2').val(year + '-' + month + '-' + days);
		});
		$('#btn_gen').click(function(){
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('#filter_form').serialize(),
				url: '/bill_file.html',
				beforeSend: function() {
					$('#btn_gen').attr('disabled', 'disabled');
				},
				success: function(result) {
					$('#btn_gen').removeAttr('disabled');
					$('#btn_gen').addClass('btn-link');
					$('#btn_gen').text('下载报表（Excel）');
					$('#btn_gen').attr('data-link', result.link);
					$('#btn_gen').click(function(){
						location.href = result.link;
					});


				}
			});
		});
	});
</script>
</body>
</html>

