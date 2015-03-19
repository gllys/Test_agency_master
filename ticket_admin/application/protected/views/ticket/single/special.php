<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/21/14
 * Time: 11:26 AM
 */
$this->breadcrumbs = array('单票', '设定特定日期的'.$labels[$type]);
?>
<style>
	td.ui-state-highlight a {
		background: none #03c3c4 !important;
	}
	.ui-datepicker-calendar td {
		background-image: none !important;
	}
	.ui-datepicker-prev,
	.ui-datepicker-next {
		display: none;
	}
	.ui-datepicker-inline {
		width: 66.7em !important;
	}
	#datepicker a, .ui-datepicker-unselectable span  {
		line-height: 2 !important;
	}
	.ui-datepicker-unselectable {
		height: 35px;
	}
	.ui-datepicker-unselectable span {
		padding: 3px 5px !important;
	}
</style>
<div class="contentpanel">
	<div class="row">
		<div class="col-md-12">
			<label>您现在设置的票种是 <?php echo $info['name']?></label>
			<br/>
			<label>选择一些日期，并设定<?php echo $labels[$type]?>。未设定日期均为默认<?php echo $labels[$type]?></label>
			<div class="panel-heading">
				<p>您已选择如下日期，已用深浅不同的颜色标注的日期也可以重新选择设定。</p>
				<div id="selected" style="color: #000020"></div>
				<p>设定上述日期的<?php echo $labels[$type]?>为

				<form class="form-inline" action="">
					<div class="form-group">
						<div class="input-group input-group-sm">
							<input type="text" placeholder="" class="form-control" id="val-special">
							<input type="text" id="spinner" />

						</div>
					</div>
					<div class="form-group">
						<button class="btn btn-primary btn-sm" id="btn-special">保存设定</button>
					</div>
				</form>

				</p>
			</div>

			<div class="input-group mb15">
				<div id="datepicker"></div>
			</div>
			<!-- input-group -->

		</div>
	</div>
	<div class="row">
		<?php if ($color_day_tickets) : foreach ($color_day_tickets as $key => $ticket) : list($number, $color) = explode('.', $key)?>
		<div class="col-sm-6 col-md-2">
			<div class="panel panel-primary">
				<div class="panel-heading <?php echo $color?>">
					<div class="panel-btns" style="display: none;">
						<a href="#" class="panel-minimize tooltips" data-toggle="tooltip" title="" data-original-title=""><i class="fa fa-minus"></i></a>
						<a href="#" class="panel-close tooltips" data-toggle="tooltip" title="" data-original-title=""><i class="fa fa-times"></i></a>
					</div><!-- panel-btns -->
					<h3 class="panel-title"><?php echo $number?></h3>
				</div>
				<div class="panel-body">
					<ul class="icon-list" style="margin: 0">
						<?php foreach ($ticket as $date) : ?>
							<li><?php echo $date?></li>
						<?php endforeach; ?>
					</ul>
				</div><!-- panel-body -->
			</div><!-- panel -->
		</div>
		<!-- col-sm-6 -->
		<?php endforeach; unset($ticket); endif; ?>
	</div>
</div>
<script>
	jQuery(document).ready(function () {
		var selected = {};
		var date = new Date(); // 不可删
		var day_stocks = '<?php echo json_encode($day_tickets)?>';
		day_stocks = JSON.parse(day_stocks);
		jQuery('#datepicker').multiDatesPicker({
			inline: true,
			minDate: 0,
			numberOfMonths: 3,
			showButtonPanel: true,
			todayHighlight: true,
			showButtonPanel: false,
			beforeShowDay: function (today) {
				var m = today.getMonth()+1;
				m = m < 10 ? '0'+m : m;
				var d = today.getDate();
				d = d < 10 ? '0'+d : d;
				var day = today.getFullYear()+'-'+m+'-'+d;
				var result = [true, ''];
				if (day_stocks[day] != undefined) {
					result[1] = ""+day_stocks[day][1].substr(1);
					result[2] = ""+day_stocks[day][0];
					return result;
				} else {
					return result;
				}
			},
//			addDates: [
//				<?php
//					if ($day_stocks) {
//						foreach ($day_stocks as $date => $stock) {
//							$day = strtotime($date.' 10:00:00');
//							echo 'date.setFullYear('.date('Y',$day).','.(date('n',$day)-1).','.date('j',$day).'),';
//						}
//						unset($date, $stock);
//					}
//				?>
//			],
			onSelect: function (dateText, inst) {
				if (selected[dateText] == undefined) {
					selected[dateText] = 1;
				} else {
					delete selected[dateText];
				}
				$('#selected').html('');
				$.each(selected, function(i, v) {
					$('#selected').append('<strong style="margin-right: 10px">'+i+'</strong>');
				});
			}
		});
		$("#datepicker").datepicker('option', {dateFormat: 'yy-m-d'});

		$("#datepicker").datepicker('option', 'dayNamesMin', ['日', '一', '二', '三', '四', '五', '六']);

		$("#datepicker").datepicker('option', 'monthNames', ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月']);

//		$(document).on("mouseenter", "td.ui-state-highlight a", function () {
//			console.log($(this).text());
//			console.log($(this).parent().attr('data-month'))
//			console.log($(this).parent().attr('data-year'))
//		});

		$('#btn-special').click(function() {
			var quantity = $('#val-special').val();
			quantity = Math.floor(quantity);
			var date = [];
			$.each(selected, function(i, v) {
				date.push(i);
			});
			if (date.length > 0 && quantity > 0) {
				$.post('/ticket/single/special_bind', {t_id: <?php echo $id?>, type: '<?php echo $type?>', quantity: quantity, date: date.join(',')}, function(result) {
					location.reload();
				});
			}
			console.log(date)
			return false;
		});

		// Spinner
		var spinner = jQuery('#spinner').spinner();
		spinner.spinner('value', 0);
	});
</script>
