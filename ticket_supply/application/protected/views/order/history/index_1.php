<?php
$this->breadcrumbs = array('订单管理', '近期订单');
?>
<div class="contentpanel">

	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns" style="display: none;">
				<a title="" data-toggle="tooltip" class="panel-minimize tooltips" href=""
				   data-original-title=""><i class="fa fa-minus"></i></a>
				<a title="" data-toggle="tooltip" class="panel-close tooltips" href=""
				   data-original-title=""><i class="fa fa-times"></i></a>
			</div>
			<!-- panel-btns -->
			<h4 class="panel-title">分销商查询</h4>
		</div>
		<div class="panel-body">
			<form class="form-inline" method="post">
				<div class="mb10">
					<div class="form-group" style="margin:0">
						<input name="start_date" class="form-control datepicker" placeholder="<?php echo isset($get['start_date']) ? $get['start_date'] : '开始日期'?>" type="text"> ~
						<input name="end_date" class="form-control datepicker" placeholder="<?php echo isset($get['end_date']) ? $get['end_date'] : '结束日期'?>" type="text">
					</div>
					<!-- form-group -->

					<div class="form-group" style="margin:0">
						<select class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
							<option value="">订单类型</option>
						</select>
					</div>
					<div class="form-group" style="margin:0">
						<select id="status_link" class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
							<option value="">订单状态</option>
							<?php foreach ($status_labels as $status => $label) :?>
								<option <?php echo isset($get['status']) && $status == $get['status'] ? 'selected="selectd"' : ''?> value="<?php echo $status?>"><?php echo $label?></option>
							<?php endforeach; unset($status, $label)?>
						</select>
						<script>
							$('#status_link').change(function() {
								location.href = '/order/history/view/status/'+$(this).val();
							});
						</script>
					</div>
				</div>
				<div>


					<div class="input-group input-group-sm mb15">
						<div class="input-group-btn">
							<button id="search_label" type="button" class="btn btn-default" tabindex="-1">订单号</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
							        tabindex="-1">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a class="sec-btn" href="javascript:;" data-id="id" id="">订单号</a></li>
								<li><a class="sec-btn" href="javascript:;" data-id="name" id="">门票名称</a></li>
								<li><a class="sec-btn" href="javascript:;" data-id="owner_name" id="">取票人</a></li>
								<li><a class="sec-btn" href="javascript:;" data-id="owner_mobile" id="">手机号</a></li>
								<li><a class="sec-btn" href="javascript:;" data-id="owner_card" id="">身份证</a></li>
							</ul>
							<script>
								$('.sec-btn').click(function(){
									$('#search_label').text($(this).text());
									$('#search_field').attr('name', $(this).attr('data-id'));
								});
							</script>
						</div>
						<!-- input-group-btn -->
						<input id="search_field" name="id" type="text" class="form-control" style="z-index: 0"/>
					</div>
					<!-- input-group -->
					<div class="input-group input-group-sm mb15">

						<button class="btn btn-primary btn-xs" type="submit">查询</button>
					</div>
				</div>


			</form>
		</div>
		<!-- panel-body -->
	</div>


	<ul class="nav nav-tabs">
		<?php
		$status_labels = array_merge(array('all' => '全部'), $status_labels);
		if (!isset($get['status'])) {
			$get['status'] = 'all';
		}
		foreach ($status_labels as $status => $label) :
			?>
			<li class="<?php echo isset($get['status']) && $status == $get['status'] ? 'active' : '' ?>">
				<a href="/order/history/view/status/<?php echo $status ?>"><strong><?php echo $label ?></strong></a>
			</li>
		<?php endforeach;
		unset($status, $label) ?>
	</ul>

	<div class="tab-content mb30">
		<style>
			.tab-content .table tr > * {
				text-align: center
			}

			.tab-content .ckbox {
				display: inline-block;
				width: 30px;
				text-align: left

			}

		</style>
		<div id="t1" class="tab-pane active">
			<table class="table table-bordered mb30">
				<thead>
				<tr>
					<th>订单号</th>
					<th>门票名称</th>
					<th>取票人</th>
					<th>取票人手机号</th>
					<th>游玩日期</th>
					<th>票数</th>
					<th>支付金额</th>
					<th>订单状态</th>
					<th>分销商</th>
				</tr>
				</thead>
				<tbody>
				<?php if (isset($lists['data'])) : foreach ($lists['data'] as $order) :?>
				<tr>
					<td><a href="/order/detail/?id=<?php echo $order['id']?>"><?php echo $order['id']?></a></td>
					<td style="text-align: left"><?php echo $order['name']?></td>
					<td><?php echo $order['owner_name']?></td>
					<td><?php echo $order['owner_mobile']?></td>
					<td><?php echo $order['use_day']?></td>
					<td style="text-align: right"><?php echo $order['nums']?></td>
					<td style="text-align: right"><?php echo number_format($order['amount'], 2)?></td>
					<td class="text-<?php echo $status_class[$order['status']]?>"><?php
						echo '<span class="btn btn-xs">';
						echo  $status_labels[$order['status']];
						echo '</span>';
						?></td>
					<td><?php echo $order['distributor_name']?></td>
				</tr>
				<?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
				<?php
				if (isset($lists['data'])) {
					$this->widget('CLinkPager', array(
						'cssFile' => '',
						'header' => '',
						'prevPageLabel' => '上一页',
						'nextPageLabel' => '下一页',
						'firstPageLabel' => '',
						'lastPageLabel' => '',
						'pages' => $pages,
						'maxButtonCount' => 5, //分页数量
					));
				}
				?>
			</div>
		</div>
		<!-- tab-pane -->

		<div id="t2" class="tab-pane">


		</div>
		<!-- tab-pane -->

	</div>


</div><!-- contentpanel -->
<script>
	jQuery(document).ready(function () {

		// Tags Input
jQuery('#tags').tagsInput({width:'auto'});
 
// Textarea Autogrow
jQuery('#autoResizeTA').autogrow();

// Spinner
var spinner = jQuery('#spinner').spinner();
spinner.spinner('value', 0);

// Form Toggles
jQuery('.toggle').toggles({on: true});

// Time Picker
jQuery('#timepicker').timepicker({defaultTIme: false});
jQuery('#timepicker2').timepicker({showMeridian: false});
jQuery('#timepicker3').timepicker({minuteStep: 15});

// Date Picker
jQuery('#datepicker').datepicker();
jQuery('#datepicker-inline').datepicker();
jQuery('#datepicker-multiple').datepicker({
    numberOfMonths: 3,
    showButtonPanel: true
});

// Input Masks
jQuery("#date").mask("99/99/9999");
jQuery("#phone").mask("(999) 999-9999");
jQuery("#ssn").mask("999-99-9999");

// Select2
jQuery("#select-basic, #select-multi").select2();
jQuery('.select2').select2({
    minimumResultsForSearch: -1
});

function format(item) {
    return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined)?"":item.element[0].getAttribute('rel') ) + ' mr10"></i>' + item.text;
}

// This will empty first option in select to enable placeholder
jQuery('select option:first-child').text('');

jQuery("#select-templating").select2({
    formatResult: format,
    formatSelection: format,
    escapeMarkup: function(m) { return m; }
});

// Color Picker
if(jQuery('#colorpicker').length > 0) {
    jQuery('#colorSelector').ColorPicker({
onShow: function (colpkr) {
jQuery(colpkr).fadeIn(500);
            return false;
},
onHide: function (colpkr) {
            jQuery(colpkr).fadeOut(500);
            return false;
},
onChange: function (hsb, hex, rgb) {
jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
jQuery('#colorpicker').val('#'+hex);
}
    });
}

// Color Picker Flat Mode
jQuery('#colorpickerholder').ColorPicker({
    flat: true,
    onChange: function (hsb, hex, rgb) {
jQuery('#colorpicker3').val('#'+hex);
    }
});
		$('#all-btn').click(function () {
			var obj = $(this).parents('table')
			if ($(this).is(':checked')) {
				obj.find('input').prop('checked', true)
				$(this).text('反选')
			} else {
				obj.find('input').prop('checked', false)
				$(this).text('全选')
			}
		});
		jQuery('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
	});
</script>

