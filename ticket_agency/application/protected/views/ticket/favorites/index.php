<?php
use common\huilian\utils\Format;

/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/19/14
 * Time: 4:29 PM
 */
$this->breadcrumbs = array(
	'门票管理',
	'我的收藏'
);
?>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns" style="display: none;">
				<a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title="Minimize Panel"><i class="fa fa-minus"></i></a> <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title="Close Panel"><i class="fa fa-times"></i></a>
			</div>
			<!-- panel-btns -->
			<h4 class="panel-title" style="padding-left: 0;">查询</h4>
		</div>
		<div class="panel-body">
			<!--搜索结构改动--->
			<form class="form-inline" method="get" action="/ticket/favorites/index/type/<?= $type ?>">
				<div class="form-group">
					门票名称： <input class="form-control" placeholder="" type="text" name="name" value="<?= $name ?>">
				</div>
				<div class="form-group">
					<button class="btn btn-primary mr5 btn-sm">搜索</button>
				</div>
			</form>
		</div>
		<!-- panel-body -->
	</div>
	<style>
.table-bordered th:nth-child(1) {
	padding-left: 20px;
}

.table-bordered td:nth-child(1) {
	padding-left: 20px;
}
</style>
	<ul class="nav nav-tabs">
		<li class="<?= $type == 1 ? 'active' : '' ?>"><a data-id="1" data-toggle="tab" href="#t1" class="bun-link"><strong>散客</strong></a></li>
		<li class="<?= $type == 0 ? 'active' : '' ?>"><a data-id="0" data-toggle="tab" href="#t2" class="bun-link"><strong>团队</strong></a></li>
		<script>
			$(function(){
				$('.bun-link').click(function(){
					location.href = '/ticket/favorites/index/type/' + $(this).attr('data-id');
				});
			});
		</script>
	</ul>
	<div class="tab-content mb30" style="padding: 0; border: none;">
		<div id="t1" class="tab-pane active">
			<table class="table table-bordered" id="tablecss">
				<thead>
					<tr>
						<th>票种</th>
						<th>景区</th>
						<th>门票名称</th>
						<th>供应商</th>
						<th>游玩有效期</th>
						<th>门市挂牌价</th>
						<th>网络销售价</th>
						<th><?= $type == 0 ? '团客' : '散客' ?>结算价</th>
						<?php if($type == 0): ?>
						<th>最低订购数</th>
						<?php endif; ?>
						<th>类型</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						if (isset($lists)):
							foreach($lists as $model):
					?>
					<tr>
						<td><?= $model['is_union'] == 1 ? '联票' : '单票' ?></td>
						<td><?php
                            //单例，性能优化
                            if (!isset($singleLans)) {
                                //得到所有景点信息
                                $ids = PublicFunHelper::arrayKey($lists, 'scenic_id');
                                $param = array();
                                $param['ids'] = join(',', $ids);
                                $param['items'] = 100000;
                                $param['fields'] = 'id,name';
                                $data = Landscape::api()->lists($param,true,30);
                                $singleLans = PublicFunHelper::ArrayByUniqueKey(ApiModel::getLists($data), 'id');
                                //print_r($singleLans);
                            }
                            $_lans = explode(',', $model['scenic_id']);
                            //print_r($_lans);
                            $html = '';
                            foreach ($_lans as $id) {
                                if (!empty($singleLans[$id])) {
                                    $html .= "<a href='/ticket/show/?id=" . $singleLans[$id]['id'] . "'>" . $singleLans[$id]['name'] . "</a><br>";
                                }
                            }
                            ?>
                            <div class="lanpart<?php echo $model['id']?>">
								 <?php echo $html?>
                            </div>
                            <div class="lan<?php echo $model['id']?>" style="display: none"><?php echo $html;?></div>
                        </td>

						<td><?= $model['name'] ?></td>
						<td><?php
						$organ = Organizations::api()->show(array('id'=>$model['organization_id']));
						echo isset($organ['body']['name'])?$organ['body']['name']:"";
						?></td>
						<td><?php
						$time = explode(',',$model['date_available']);
						if(!empty($time[0]) && !empty($time[1])){
							echo Format::date($time[0], 'zh') . '~<br/>' . Format::date($time[1], 'zh');
						}else{
							echo '';
						}

						?></td>
						<td><?= $model['listed_price'];?></td>
						<td><?= $model['sale_price'] ?></td>
						<td><?= $type == 0 ? number_format($model['group_price'],2) : number_format($model['fat_price'],2) ?></td>
						<?php if($type == 0): ?>
						<td><?= $model['mini_buy'] ?></td>
						<?php endif; ?>
						<td>电子票</td>
						<td>

							<div class="pull-left"><a data-toggle="modal" onclick="buy('<?= $model['ticket_id'] ?>','<?= $model['organization_id']?>');" href=".bs-example-modal-lg" class="btn btn-success btn-xs">购买</a></div>
                            <div class="pull-right"><a data-id="<?= $model['ticket_id'] ?>" data-operate="1" data-type="<?= $type ?>" data-name="<?= $model['name'] ?>"
                                                      data-fat="<?= $model['fat_price'] ?>" data-group="<?= $model['group_price'] ?>"
                                                      class="favorite-operator btn btn-danger btn-xs" href="javascript:;" title="删除">删除</a></div>
                        </td>
					</tr>
					<?php 
						endforeach;
							endif;
					?>
				</tbody>
			</table>
		</div>
		<!-- tab-pane -->
		
			<div style="text-align:center" class="panel-footer">
				<div id="basicTable_paginate" class="pagenumQu">
					<?php
					if (isset($pages)) {
						$this->widget('common.widgets.pagers.ULinkPager', array(
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
</div>
<!-- contentpanel -->

<div id="lock_layer" class="unlocked" style="display: none">
	<div class="lockedpanel">
		<div class="loginuser">
			<img src="/img/logo.png" width="200" class="img-circleimg-online" alt="" />
		</div>
		<div class="logged">
			<h3 style="color:darkred">金额:0元</h3>
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

    //全展示
    $('.lanview').click(function(){
        var id = $(this).attr('data-id');
        $('.lanpart' + id).hide();
        $('.lan' + id).show();

    })


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

		// 收藏
		$('.favorite-operator').click(function() {
			var _this = this;
			var params = {
				id: $(this).attr('data-id'),
				done: $(this).attr('data-operate'),
				single: $(this).attr('data-type'),
				name: $(this).attr('data-name'),
                fat_price : $(this).attr('data-fat'),
                group_price : $(this).attr('data-group')
			}
			$.post('/ticket/favorites/toggle', params, function(data) {
				if(data['code'] == 1) {
					if($(_this).attr('data-operate') == 1) {
						$(_this).attr('data-operate', 0).removeClass('btn-danger').addClass('btn-success').text('添加');
					} else {
						$(_this).attr('data-operate', 1).removeClass('btn-success').addClass('btn-danger').text('删除');
					}
				}	
		    }, 'json');
			
		});
		
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

