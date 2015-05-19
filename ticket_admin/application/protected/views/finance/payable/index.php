<?php
$this->breadcrumbs = array('结算管理', '应付账款');
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
            <h4 class="panel-title">应付账款</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="get" action="/finance/payable/">
				<div class="form-group" style="width: 335px;">
                                    <label>账单时间:</label>
					<input style="cursor: pointer;cursor: hand;background-color: #fff" class="form-control datepicker" placeholder="账单日期" type="text" name="bill_sd" readonly="readonly" value="<?php echo isset($_REQUEST['bill_sd']) ? $_REQUEST['bill_sd'] : ''?>"> ~
					<input style="cursor: pointer;cursor: hand;background-color: #fff" class="form-control datepicker" placeholder="账单日期" type="text" name="bill_ed" readonly="readonly" value="<?php echo isset($_REQUEST['bill_ed']) ? $_REQUEST['bill_ed'] : ''?>">
				</div><!-- form-group -->
				<div class="form-group">
					<select class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;" name="pay_state">
						<option value="">支付状态</option>
                        <option value="">全部</option>
                        <option value="1" <?php if(isset($_REQUEST['pay_state']) && '1' == $_REQUEST['pay_state']) { echo 'selected';}?> >已打款</option>
						<option value="0" <?php if(isset($_REQUEST['pay_state']) && '0' == $_REQUEST['pay_state']) { echo 'selected';}?>>未打款</option>
					</select>
				</div>
				<div class="form-group">
                                    <input class="form-control" placeholder="请输入供应商名称"type="text" name="supply_name" value="<?php echo isset($_REQUEST['supply_name'])?$_REQUEST['supply_name']:""; ?>">
				</div>
                                 <div class="form-group">
				      <button class="btn btn-primary btn-sm" type="submit">查询</button>
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
			<th>供应商名称</th>
			<th>账单生成日期</th>
			<th>账单类型</th>
			<th>应付金额</th>
			<th>订单张数</th>
			<th>支付状态</th>
			<th>操作</th>
		  </tr>
		</thead>
		<tbody>
		<?php if(isset($bill)):?>
			<?php foreach ($bill as $value):?>
		  <tr>
			<td style="padding-left:20px;">
                <a onclick="finance_detail('<?php echo $value['id']; ?>');" href="#finance-detail" data-toggle="modal">
                    <?php echo $value['id']?>
                </a>
            </td>
			<td style="text-align: left;color:gray"><?php echo $value['supply_name'];?></td>
			<td><?php echo date('Y-m-d H:i:s',$value['created_at'])?></td>
			<td>
				<?php if($value['bill_type'] == 1){
					echo "在线支付";
					}elseif ($value['bill_type'] == 2) {
						echo "信用支付";
					}elseif ($value['bill_type'] == 4) {
						echo "平台支付";
					}else{
						echo "储值支付";
						}?>应付账款
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
            
			<td >
            <a onclick="finance_detail('<?php echo $value['id']; ?>');" href="#finance-detail" data-toggle="modal" class="btn btn-success btn-bordered btn-xs" style="border-width: 1px">查看</a>
			
			<?php if (0 == $value['pay_status']): ?>
                <button onclick="finace_upload_show('<?php echo $value['id']; ?>');" href="#finance-detail" data-toggle="modal" class="btn btn-success btn-bordered btn-xs" style="border-width: 1px">打款</button>
            <?php endif; ?>    
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
        if (isset($bill)) {
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
    <div id="finance-detail" class="modal fade"></div>
    <div id="finance-upload-show" class="modal fade"></div>
</div><!-- contentpanel -->
<script>
function finance_detail($id)
{
    $('#finance-detail').html();
	$.get('/finance/payable/detail?id='+$id,function(data){
		$('#finance-detail').html(data);
	});
}

function finace_upload_show($id)
{
    $('#finance-detail').html();
	$.get('/finance/payable/uploadshow?id='+$id,function(data){
		$('#finance-detail').html(data);
	});
}

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
