<?php
$this->breadcrumbs = array('结算管理','应付账款');
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
    .table-bordered th:nth-child(1){padding-left:35px;}
    .table-bordered td:nth-child(1){padding-left:35px;}
</style>

<div class="contentpanel">

	<div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns" style="display: block;">
                <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title="Minimize Panel"><i class="fa fa-minus"></i></a>
                <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title="Close Panel"><i class="fa fa-times"></i></a>
            </div><!-- panel-btns -->
            <h4 class="panel-title">应付账款</h4>
        </div>

        <div class="panel-body">
            <form class="form-inline" method="get" action="/finance/payment/">
        		<div class="form-group">
                            <input id="dp1423213543102" class="form-control datepicker" placeholder="账单日期" type="text" name="bill_sd" readonly="readonly" value="<?php echo isset($get['bill_sd']) ? $get['bill_sd'] : ''?>"> ~
        		    <input id="dp1423213543103" class="form-control datepicker" placeholder="账单日期" type="text" name="bill_ed" readonly="readonly" value="<?php echo isset($get['bill_ed']) ? $get['bill_ed'] : ''?>">
        	    </div><!-- form-group -->
                <div class="form-group">
                        <select class="select2"  style="width:150px;padding:0 10px;" name="pay_state">
                                <option value="" selected="selected">支付状态</option>
                                <option value="1" <?php  if(!empty($get['pay_state']) && $get['pay_state']==1 ){echo "selected=selected";}?>>已打款</option>
                                <option value="0" <?php  if(!empty($get['pay_state']) && $get['pay_state']==0 ){echo "selected=selected";}?>>未打款</option>
                        </select>
                </div>		
                <div class="form-group input-group-btn" style="padding:0;">
                        <button id="search_label" type="button" class="btn btn-default" tabindex="-1" style="line-height:12px;border-top-left-radius:3px;border-bottom-left-radius:3px;"><?= empty($ids) ? '供应商名称' : '结算单号' ?></button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1" style="line-height:12px;border-radius:0;"><span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                                <li><a class="sec-btn" href="javascript:;" data-id="supply_name">供应商名称</a></li>
                                <li><a class="sec-btn" href="javascript:;" data-id="ids" aria-labelledby="search_label">结算单号</a></li>
                        </ul>
                        <script>
                                $('.sec-btn').click(function() {
                                        $('#search_label').text($(this).text());
                                        $('#search_field, #menu_input').attr('name', $(this).attr('data-id'));
                                        $('#menu_input').val('');
                                });
                        </script>
                        <input class="form-control" id="menu_input" placeholder=""  name="<?= empty($ids) ? 'supply_name' : 'ids' ?>" value="<?= empty($ids) ? $supply_name : $ids ?>" type="text" style="width:200px;border-top-left-radius:0;border-bottom-left-radius:0;border-left:none;">
                        <button class="btn btn-primary btn-xs" type="submit" style="margin-left:15px;">查询</button>
                </div> 
            </form>
        </div><!-- panel-body -->
    </div>
	<style>
    	.table1 tr>*{
    		text-align:center
    	}
	</style>
	  <table class="table table-bordered table1">
		<thead>
		  <tr>
			<th width="200">结算单号</th>
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
    		<?php if(isset($bill) && !empty($bill)):?>
    			<?php foreach ($bill as $value):?>
    		  <tr>
    			<td><a href="/finance/detail?id=<?php echo $value['id']?>"><?php echo $value['id']?></a></td>
    			<td><?php echo $value['supply_name']?></td>
    			<td><?php echo date('Y年m月d日',$value['created_at'])?></td>
    			<td>
    				<?php if($value['bill_type'] == 1){
    					echo "在线支付";
    					}elseif ($value['bill_type'] == 2) {
    						echo "信用支付";
    					}else{
    						echo "储值支付";
    						}?>
    			</td>
    			<td><?php echo $value['bill_amount']?></td>
    			<td ><?php echo $value['bill_num']?>张</td>
    			<?php if($value['pay_status'] == 1 && $value['bill_amount'] > 0):?>
                    <td class="text-success">已打款</td>
                <?php elseif($value['bill_amount'] == 0):?>
                    <td class="text-warning">无需打款</td>
                <?php else:?>
                    <td class="text-danger">未打款</td>
                <?php endif;?>
    			<td>
    			    <a class="btn btn-primary btn-xs mr10" href="/finance/detail?id=<?php echo $value['id']?>" data-toggle="modal">查看</a>
    			</td>
    		  </tr>
    			<?php endforeach;?>
    			<?php else:?>
    				<tbody>
    					<tr><td colspan="8" style="text-align:center">暂无数据</td></tr>
    				</tbody>
    		<?php endif;?>
		</tbody>
	  </table>


   <div class="panel-footer pagenumQu basicTable_paginate" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
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
</div>
<!-- contentpanel -->

























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
