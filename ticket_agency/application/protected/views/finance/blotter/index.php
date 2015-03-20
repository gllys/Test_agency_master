<?php
$this->breadcrumbs = array('结算管理', '交易流水');
?>
<style>
	.table-bordered th {
		line-height: 2em !important;;
	}
	.table-bordered th, .table-bordered td {
		vertical-align: middle !important;
	}
	.table-bordered a:hover {
		text-decoration: none;
	}
</style>
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
			<h4 class="panel-title" style="padding-left:0;">交易流水</h4>
		</div>
		<div class="panel-body">
			<form class="form-inline" action="/finance/blotter/view/" method="get">
				
					<div class="form-group">
						 <input name="time[]" class="form-control datepicker" readonly placeholder="开始日期" type="text" value="<?php if(isset($get['time'])){ list($a,$b) = explode(' - ', $get['time']);echo $a;}?>"> ~
                                                 <input name="time[]" class="form-control datepicker" readonly placeholder="结束日期" type="text" value="<?php if(isset($get['time'])){ list($a,$b) = explode(' - ', $get['time']);echo $b;}?>">
					</div>
					<!-- form-group -->

					<div class="form-group">
						<select name="mode" id="mode_link" class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
							<option value="">支付方式</option>
							<?php foreach ($mode_type as $mode => $value) :?>
								<option <?php echo isset($get['mode']) && $mode == $get['mode'] ? 'selected="selectd"' : ''?> value="<?php echo $mode?>"><?php echo $value?></option>
							<?php endforeach; unset($mode, $value)?>
						</select>
					</div>
					<div class="form-group">
						<select name="type" id="type_link" class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
							<option value="">交易类型</option>
							<?php foreach ($status_labels as $type => $label) :?>
								<option <?php echo isset($get['type']) && $type == $get['type'] ? 'selected="selectd"' : ''?> value="<?php echo $type?>"><?php echo $label?></option>
							<?php endforeach; unset($type, $label)?>
						</select>
					</div>
                                        <div class="form-group">
					     <input class="form-control" placeholder="请输入流水号"type="text" style="width:200px;" id="search_field" name="id" value="<?php echo isset($get['id'])?$get['id']:'' ?>">	
					</div>
				
					<!--div class="input-group input-group-sm mb15">
						<div class="input-group-btn">
							<button readonly id="search_label" type="button" class="btn btn-default" tabindex="-1">流水号</button>
						</div>
						<input id="search_field" name="id" value="<?php echo isset($get['id'])?$get['id']:'' ?>" type="text" class="form-control" style="z-index: 0"/>
					</div-->
					<!-- input-group -->
					<div class="form-group">
						<button class="btn btn-primary btn-sm" type="submit">查询</button>
					</div>
			</form>
		</div>
		<!-- panel-body -->
	</div>

<div class="table table-bordered">
	<ul class="nav nav-tabs">
		<?php
		$status_labels = array_merge(array('0' => '全部'), $status_labels);
		if (!isset($get['type'])) {
			$get['type'] = '0';
		}
		foreach ($status_labels as $type => $label) :
			?>
			<li class="<?php echo isset($get['type']) && $type == $get['type'] ? 'active' : '' ?>">
				<a href="/finance/blotter/<?php echo $type?'view/type/'.$type:'' ?>"><strong><?php echo $label ?></strong></a>
			</li>
		<?php endforeach;
		unset($type, $label) ?>
	</ul>
</div>
		<style>
		.pageheader{border-bottom:none; -webkit-box-shadow:none; box-shadow:none;}
		</style>
                <div class="tab-content mb30">
            <div id="t1" class="tab-pane active">
           		
                    <table class="table table-bordered mb30">
                        <thead>
                            <tr>
                            	<!--th width="100">
                                    <div class="ckbox ckbox-primary" style="margin-left:17px;">
                                        <input type="checkbox" class="ids" id="checkbox-allcheck" value="949">
                                        <label for="checkbox-allcheck" class="allcheck">全选</label>
                                    </div>
                                </th-->
                                <th width="150" style="padding-left:20px;">交易日期</th>
					<th>操作人</th>
					<th>支付方式</th>
					<th>交易类型</th>
					<th>交易金额</th>
					<!--th>平台余额</th-->
					<th>交易流水号</th>
				</tr>
				</thead>
				 <tbody id="staff-body">
                            
                            	
				<?php if (isset($lists['data']) && !empty($lists['data'])) : foreach ($lists['data'] as $blotter) :  ?>
				<tr>
                                        <!--td>
                                            <div class="ckbox ckbox-primary" style="margin-left: 17px;">
                                                <input type="checkbox" class="ids" id="checkbox943" value="943">
                                                <label for="checkbox943"></label>
                                            </div>
                                       </td-->
					<td style="padding-left:20px;"><?php echo date('Y年m月d日 H:i:s',$blotter['created_at'])?></td>
					<td><?php 
                                     $rs = Users::model()->find('id=:id', array(':id'=>$blotter['op_id']));
                                     if(!empty($rs)){
                                         if(!empty($rs->name)){
                                            echo $rs->name;
                                        }else{ echo $rs->account;
                                            }
                                     }
                                        ?></td>
					<td><?php echo $mode_type[$blotter['mode']]?></td>
					
					<td class="text-<?php echo $status_class[$blotter['type']];?>"><?php echo $status_labels[$blotter['type']]?></td>
					<td class="text-<?php echo $status_class[$blotter['type']];?>"><?php if(in_array($blotter['type'], array('2','3','5'))) echo "+"; else echo "-"; ?><?php echo number_format($blotter['amount'],2)?></td>
					<!--td><?php //echo number_format($blotter['balance'],2)?></td-->
					<td><?php echo $blotter['id']?></td>
				</tr>
				<?php endforeach; ?>
			<?php else:?>
				<tr>
					<td colspan="5">暂无数据</td>
				</tr>
				<?php endif; ?>
				</tbody>
			</table>
            </div>
			<div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
				<?php
				if (isset($lists['data']) && !empty($lists['data'])) {
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
		<!-- tab-pane -->

		<div id="t2" class="tab-pane">


		</div>
		<!-- tab-pane -->

	</div>


</div><!-- contentpanel -->
<script>
	jQuery(document).ready(function () {
		jQuery('#tags').tagsInput({width: 'auto'});

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
        jQuery('.datepicker').datepicker({showOtherMonths: true, selectOtherMonths: true});
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
            return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined) ? "" : item.element[0].getAttribute('rel')) + ' mr10"></i>' + item.text;
        }

// This will empty first option in select to enable placeholder
        jQuery('select option:first-child').text('');

        jQuery("#select-templating").select2({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) {
                return m;
            }
        });

// Color Picker
        if (jQuery('#colorpicker').length > 0) {
            jQuery('#colorSelector').ColorPicker({
                onShow: function(colpkr) {
                    jQuery(colpkr).fadeIn(500);
                    return false;
                },
                onHide: function(colpkr) {
                    jQuery(colpkr).fadeOut(500);
                    return false;
                },
                onChange: function(hsb, hex, rgb) {
                    jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
                    jQuery('#colorpicker').val('#' + hex);
                }
            });
        }

// Color Picker Flat Mode
        jQuery('#colorpickerholder').ColorPicker({
            flat: true,
            onChange: function(hsb, hex, rgb) {
                jQuery('#colorpicker3').val('#' + hex);
            }
        });

//		$('#mode_link').change(function() {
//			location.href = '/finance/blotter/view/mode/'+$(this).val();
//		});
//
//		$('#type_link').change(function() {
//			location.href = '/finance/blotter/view/type/'+$(this).val();
//		});
	});
</script>

