<?php 
$this->breadcrumbs = array('结算管理','资产管理');
?>
<div class="contentpanel">
	<style>
	.table tr>*{
		text-align:center
	}
	</style>

	
	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">资产管理</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="post">
				<div class="form-group">
					<input class="form-control" name="supplier_name" placeholder="请输入供应商名称" type="text" style="width:480px;">
				</div>
				<button type="submit" class="btn btn-primary btn-xs">查询</button>
            </form>
        </div><!-- panel-body -->
    </div>


	<div class="panel panel-default">
		  <table class="table table-bordered mb30">
			<thead>
			  <tr>
				<th>供应商名称</th>
				<th>加入日期</th>
				<th>信用余额</th>
				<th>信用结算周期</th>
				<th>储蓄余额</th>
			  </tr>
			</thead>
			<tbody>
			<?php if(isset($credit)):?>
				<?php foreach ($credit as $value):?>
			  <tr>
				<td><?php echo $value['supplier_name']?></td>
				<td><?php echo date('Y年m月d日',$value['add_time'])?></td>
				<td><?php echo $value['credit_infinite']?"无限":number_format($value['credit_money'],2) ?></td>
				<td><?php if($value['checkout_type'] == '1'){echo '月结 '.$value['checkout_date'].'日';}elseif($value['checkout_type'] == '0'){echo '周结 '.Credit::getWeekDay($value['checkout_date']);}else{echo '未设置';}?></td>
				<td><?php echo number_format($value['balance_money'],2)?></td>
			  </tr>
				<?php endforeach;?>
			<?php else:?>
				<tr><td colspan="5">暂无数据</td></tr>
			<?php endif;?>
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
</div><!-- contentpanel -->
<script>
jQuery(document).ready(function() {

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
    jQuery('.datepicker').datepicker();
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