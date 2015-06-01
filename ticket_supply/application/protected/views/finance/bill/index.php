<?php
$this->breadcrumbs = array('结算管理', '应收账款');
?>
<style>
    .table-bordered th {
        line-height: 2em !important;
    }
    .table-bordered th,
    .table-bordered td {
        vertical-align: middle !important;
    }
    .table-bordered a:hover {
        text-decoration: none;
    }
    .ui-datepicker { z-index:9999!important }
</style>
<div class="contentpanel">

	<div class="panel panel-default">
        <div class="panel-heading">
			<ul class="list-inline">
				<li><h4 class="panel-title">应收账款</h4></li>
				<li><a href="/order/history/help?#6.2" title="帮助文档" class="clearPart" target="_blank">查看帮助文档</a> </li>
			</ul>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="post" action="/finance/bill/">
				<div class="form-group" style="width: 335px;">
                                    <label>账单时间:</label>
					<input style="cursor: pointer;cursor: hand;background-color: #ffffff" class="form-control datepicker" placeholder="账单日期" type="text" name="bill_sd" id="bill_sd"
						   readonly="readonly" value="<?php echo isset($_REQUEST['bill_sd']) ? $_REQUEST['bill_sd'] : ''?>"> ~
					<input style="cursor: pointer;cursor: hand;background-color: #ffffff" class="form-control datepicker" placeholder="账单日期" type="text" name="bill_ed" id="bill_ed"
						   readonly="readonly" value="<?php echo isset($_REQUEST['bill_ed']) ? $_REQUEST['bill_ed'] : ''?>">
				</div><!-- form-group -->
				<div class="form-group">
					<select class="select2" data-placeholder="Choose One" style="width:103px;height:34px;" name="pay_state">
						<option value="">支付状态</option>
						<option value="1">已打款</option>
						<option value="0">未打款</option>
					</select>
				</div>
				<div class="form-group">
                                    <input class="form-control" placeholder="请输入分销商名称"type="text" style="width:200px;" name="agency_name" value="<?php echo isset($_REQUEST['agency_name'])?$_REQUEST['agency_name']:""; ?>">
				</div>
                                 <div class="form-group">
									 <input type="hidden" name="is_export" class="is_export" value="0">
									 <button class="btn btn-primary btn-sm" type="submit">查询</button>
									 <button class="btn btn-primary btn-sm" type="button" id="export">导出</button>
                                 </div>
            </form>
        </div><!-- panel-body -->
    </div>
	<style>
	.table1 tr>*{
		text-align:center
	}
	
	</style>
	  <table class="table table-bordered table1 mb30">
		<thead>
		  <tr>
                      <th style="padding-left:20px;width: 180px;">结算单号</th>
			<th>打款机构</th>
			<th>账单生成日期</th>
			<th>账单类型</th>
			<th>应付金额</th>
			<th>订单张数</th>
			<th>支付状态</th>
			<th>操作状态</th>
			<th>操作</th>
		  </tr>
		</thead>
		<tbody>
		<?php
			if(isset($lists['data'])):
				foreach ($lists['data'] as  $value):
				?>
		  <tr>
			<td style="padding-left:20px;"><a href="/finance/detail?id=<?php echo $value['id']?>"><?php echo $value['id']?></a></td>
			<td style="text-align: left;color:gray"><?php echo $value['bill_type'] == 1||$value['bill_type'] == 4 ? '汇联' : $value['agency_name']?></td>
			<td><?php echo date('Y年m月d日',$value['created_at'])?></td>
			<td>
				<?php if($value['bill_type'] == 1){
					echo "在线支付";
					}elseif ($value['bill_type'] == 2) {
						echo "信用支付";
					}elseif ($value['bill_type'] == 4) {
						echo "平台支付";
					}else{
						echo "储值支付";
						}?>
			</td>
			<td class="text-danger"><?php echo $value['bill_amount']?></td>
			<td class="text-danger"><?php echo $value['bill_num']?>张</td>
			<?php if($value['pay_status'] == 1 && $value['bill_amount'] > 0):?>
				<td class="text-success">已打款</td>
			<?php elseif($value['bill_amount'] == 0):?>
				<td class="text-warning">无需打款</td>
			<?php else:?>
				<td class="text-danger">未打款</td>
			<?php endif;?>
			<?php if($value['receipt_status'] == 1 && $value['bill_amount'] > 0):?>
				<td class="text-success">已收款</td>
			<?php elseif($value['bill_amount'] == 0):?>
				<td class="text-warning">无需收款</td>
			<?php else:?>
				<td class="text-danger">未收款</td>
			<?php endif;?>
			<td >
			    <a class="btn btn-success btn-bordered btn-xs" href="/finance/detail?id=<?php echo $value['id']?>">查看</a>
			</td>
		  	</tr>
			<?php endforeach;?>
			<?php else:?>
					<tr><td colspan="8" style="text-align:center">暂无数据</td></tr>
		<?php endif;?>
		</tbody>
	  </table>
	<div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
		<?php
		if (isset($lists['data'])) {
			$this->widget('common.widgets.pagers.ULinkPager', array(
				'cssFile' => '',
				'header' => '',
				'prevPageLabel' => '上一页',
				'nextPageLabel' => '下一页',
				'firstPageLabel' => '',
				'lastPageLabel' => '',
				'pages' => $pages,
				'maxButtonCount' => 3, //分页数量
			));
		}
		?>
	</div>
	
</div><!-- contentpanel -->
<script>
jQuery(document).ready(function() {

	$('#export').click(function() {
		if ($('#bill_sd').val() == '')
		{
			$('#bill_sd').PWShowPrompt('请选择账单开始日期');
			return false;
		}
		if ($('#bill_ed').val() == '')
		{
			$('#bill_ed').PWShowPrompt('请选择账单结束日期');
			return false;
		}
		$('.is_export').attr('value', '1');
		$('form').addClass('clearPart');
        $('form').submit();
		$('form').removeClass('clearPart');
		$('.is_export').attr('value', '0');
	});



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
$('.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
        yearRange: "1995:2065",
		beforeShow: function(d){
			setTimeout(function(){
				$('.ui-datepicker-title select').select2({
					minimumResultsForSearch: -1
				});
			},0)
		},
		onChangeMonthYear: function(){
			setTimeout(function(){
				$('.ui-datepicker-title select').select2({
					minimumResultsForSearch: -1
				});
			},0)
		},
        onClose: function(dateText, inst) { 
            $('.select2-drop').hide(); 
        }
    });
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
