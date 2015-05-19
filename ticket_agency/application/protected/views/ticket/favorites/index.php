<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/19/14
 * Time: 4:29 PM
 */
$this->breadcrumbs = array('门票管理', '我的收藏');
?>
<style>
	.prov_p{width:120px;display:inline-block;height: 20px;text-align: left; cursor: pointer;}
	.ticket_type{cursor: pointer;}
	#proname{color:red;}
	#tablecss th,#tablecss td{text-align: center;}
	.bun {color: #999}
	.fav-done {color: #269abc}
	.sub-done {color: #643534}
	.sub-done:hover {color: #801504}
</style>
<div class="contentpanel">

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">查询</h4>
	</div>
	<div class="panel-body">
		<form class="form-inline" method="get" action="/ticket/favorites/index/type/<?php echo $type?>">
			<table>
				<tr>

					<td>
						<div class="form-group">
							门票名称：
							<input class="form-control" type="text" name="name" value="<?php echo $name?>"/>
						</div>
						<button class="btn btn-primary mr5 btn-sm">搜索</button>
					</td>

				</tr>
			</table>
		</form>
	</div><!-- panel-body -->
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title btn-list">
			<?php
			$btn_cls = array('primary', 'default');
			$group_cls = '';
			if ($type == 0) {
				$btn_cls = array('default', 'primary');
				$group_cls = 'group';
			}
			?>
			<button type="button" data-id="1" class="btn bun-link btn-<?php echo $btn_cls[0]?> btn-xs">散 客</button>
			<button type="button" data-id="0" class="btn bun-link btn-<?php echo $btn_cls[1]?> btn-xs">团 客</button>
			<script>
				$(function(){
					$('.bun-link').click(function(){
						location.href = '/ticket/favorites/index/type/' + $(this).attr('data-id');
					});
				});
			</script>
		</h4>
	</div>
	<style>
		.table-responsive img{
			max-width:100px
		}
		.table-responsive th,.table-responsive td{
			vertical-align:middle!important
		}
		.rules{
			position:relative;
			display:inline-block;
		}
		.rules+.rules{
			margin-left:20px;
		}
		.rules > span{
			color:#999;
			font-size:12px;
			cursor:pointer
		}
		.rules > div >span{
			margin:0 10px
		}
		.rules > div{
			display:none;
			position:absolute;
			top:15px;
			left:50px;
			z-index:999;
			width:500px;
			padding:10px;
			background-color:#fbf8e9;
			border:1px solid #fed202;
			border-radius:2px;
			box-shadow:0 0 10px rgba(0,0,0,.2);
		}
		.rules > div .table{
			background:none;
		}
		.rules > div .table tr > *{
			border:1px solid #e0d9b6
		}
		.rules:hover > div{
			display:block;
		}
	</style>
	<div class="table-responsive">
		<table class="table table-bordered mb30" id="tablecss">
			<thead>
			<tr>
				<th style="width:5%">票种</th>
				<th style="text-align:left;">景区</th>
				<th style="text-align:left;width: 25%">门票名称</th>
				<th style="text-align:left">供应商</th>
				<th style="width:15%">游玩日期</th>
				<th style="text-align:right;width:5%">销售价</th>
				<th style="text-align:right;width:5%">挂牌价</th>
				<th style="text-align:right;width:5%">散客价</th>
				<th style="width:5%">类型</th>
				<th style="width:5%">操作</th>
			</tr>
			</thead>
			<tbody>
			<?php if (isset($lists)):
			foreach($lists as $model):
				?>
				<tr>
					<td><?php echo $model['is_union'] == 1 ?'联票': '单票'?></td>
					<td style="text-align:left"><?php
						$result = Landscape::api()->lists(array("ids" => $model['scenic_id']));
						$landspaceInfo = ApiModel::getLists($result);
						foreach ($landspaceInfo as $value) {
							echo "<a href='/ticket/show/?id=" . $value['id'] . "'>" . $value['name'] . "</a><br>";
						}
						?></td>
					<td style="text-align:left">
						<div class="col-md-12">
							<div class="pull-left"><strong><?php echo  $model['name'];?></strong></div>
							<div class="pull-right" data-id="<?php echo $model['id'] ?>"><?php
								echo true
									? '<a class="bun fav '.$group_cls.' fav-done" href="javascript:;" title="取消收藏">已收藏</a>'
									: '<a class="bun fav '.$group_cls.'" href="javascript:;" title="加入收藏">收藏</a>';
								?></div>
						</div>
						<div class="col-md-12">
							<div class="pull-left">
								<div class="rules"><span>订票规则</span>
									<div class="table-responsive">
										<table class="table table-bordered mb30">
											<?php echo $model['remark'];?>
										</table>
									</div>
								</div>
								<div class="rules"><span>游玩星期</span>

									<div class="day"><?php
										if (strstr($model['week_time'], '1')) {
											echo '周一' . '&nbsp;';
										}
										if (strstr($model['week_time'], '2')) {
											echo '周二' . '&nbsp;';
										}
										if (strstr($model['week_time'], '3')) {
											echo '周三' . '&nbsp;';
										}
										if (strstr($model['week_time'], '4')) {
											echo '周四' . '&nbsp;';
										}
										if (strstr($model['week_time'], '5')) {
											echo '周五' . '&nbsp;';
										}
										if (strstr($model['week_time'], '6')) {
											echo '周六' . '&nbsp;';
										}
										if (strstr($model['week_time'], '0') === '0') {
											echo '周日' . '&nbsp;';
										}
										?></div>
								</div>
							</div>
							<div class="pull-right" data-id="<?php echo $model['id'] ?>" data-fat="<?php echo $model['fat_price'] ?>" data-group="<?php echo $model['group_price'] ?>"><?php
								echo isset($model['sub']) && $model['sub'] == 1
									? '<a class="bun sub '.$group_cls.' sub-done" href="javascript:;" title="取消订阅">已订阅</a>'
									: '<a class="bun sub '.$group_cls.'" href="javascript:;" title="加入订阅">订阅</a>';
								?></div>
						</div>
					</td>
					<td style="text-align:left">
						<?php
						$organ = Organizations::api()->show(array('id'=>$model['organization_id']));
						echo isset($organ['body']['name'])?$organ['body']['name']:"";
						?></td>
					<td>
						<?php
						$time = explode(',',$model['date_available']);
						if(!empty($time[0]) && !empty($time[1])){
							echo date('m月d日',$time[0]) . '~' .date('m月d日',$time[1]);
						}else{
							echo '';
						}

						?>
					</td>
					<td style="text-align:right"><del><?php echo $model['sale_price'];?></del></td>
					<td style="text-align:right"><del><?php echo $model['listed_price'];?></del></td>
					<td style="text-align:right" class="text-success"><?php echo number_format($model['fat_price'],2);?></td>
					<td><?php echo $model['type']?'任务单':'电子票';?></td>
					<td>
						<a class="btn btn-success btn-xs" href=".bs-example-modal-lg" onclick="buy('<?php echo $model['id'] ?>','<?php echo $model['organization_id']?>');" data-toggle="modal">购买</a>
					</td>
				</tr>
			<?php endforeach;
			endif;?>
			</tbody>
		</table>

	</div>

	<div style="text-align:center" class="panel-footer">
		<div id="basicTable_paginate" class="pagenumQu">
			<?php
			if (isset($pages)) {
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


</div>
</div><!-- contentpanel -->

<style>
	.red{color:red;}
</style>

<!--购买票开始-->
<div class="modal fade bs-example-modal-lg" id="verify-modal-buy" tabindex="-1" role="dialog"></div>

<script type="text/javascript">
	function buy(id,supplier_id){
		$('#verify-modal-buy').html();
		$.get('/ticket/buy/?price_type=0&id='+id+'&supplier_id='+supplier_id, function(data) {
			$('#verify-modal-buy').html(data);
		});
	}
	// <!--购买票结束-->

	function province(provinceid){
		// alert($(this));
		pid = provinceid;
		var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ;
		window.location.href = url;

	}

	function ticketType(typeid){
		typ = typeid;
		var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ;
		window.location.href = url;

	}
	function getLandspace(id){
		scenic = id;
		var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ+"&scenic_id="+scenic;
		window.location.href = url;
	}




	$("#getOver0").click(function(){
		$("#getOver0").parent().remove();
		pid = '';
		var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ;
		//alert(url);
		window.location.href = url;

	})

	$("#getOver1").click(function(){
		$("#getOver1").parent().remove();
		typ = '';
		var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ;
		// alert(url);
		window.location.href = url;

	})
</script>



<script>
	jQuery(document).ready(function(){
		// Tags Input
		jQuery('#tags').tagsInput({width:'auto'});

		// Textarea Autogrow
		jQuery('#autoResizeTA').autogrow();



		// Form Toggles
		jQuery('.toggle').toggles({on: true});


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


	});

</script>
<script src="/js/fav-sub.js"></script>























