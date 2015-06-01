<?php
use common\huilian\utils\TwoDimensionalArray;
?>
<style>
.ui-datepicker { z-index:9999!important }
</style>
<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns" style="display: none;">
                <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title=""><i class="fa fa-minus"></i></a> <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title=""><i class="fa fa-times"></i></a>
            </div>
            <!-- panel-btns -->
            <h4 class="panel-title">验票记录</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="get" action="/agency/verification/index">
                <div class="mb10">
                    <div class="form-group" style="margin: 0">
                        <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="begin_date" value="<?php if (isset($_GET['begin_date'])) echo $_GET['begin_date'] ?>" placeholder="开始日期" type="text" readonly="readonly">
                    ~
                        <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="end_date" value="<?php if (isset($_GET['end_date'])) echo $_GET['end_date'] ?>" placeholder="结束日期" type="text" readonly="readonly">
                    </div>
                    <!-- form-group -->
                    <div class="form-group" style="margin: 0;width: 150px;">
						<input type="text" name="supply_name" class="form-control" placeholder="供应商" value="<?= empty($_GET['supply_name']) ? '' : $_GET['supply_name'] ?>">
                    </div>
                    <!-- form-group -->
                    <div class="form-group" style="margin: 0;width: 150px;">
             			<input type="text" name="scenic_name" class="form-control" placeholder="景区" value="<?= empty($_GET['scenic_name']) ? '' : $_GET['scenic_name'] ?>">
                    </div>
                    <div class="form-group" style="margin: 0 5px 0 0">
                        <input class="form-control" name="order_id" value="<?= empty($_GET['order_id']) ? '' : $_GET['order_id'] ?>" placeholder="订单编号" type="text" style="width: 318px;">
                    </div>
                    <button class="btn btn-primary btn-xs" type="submit">查询</button>
                </div>
            </form>
        </div>
        <!-- panel-body -->
    </div>
    <style>
        .tab-content .table tr>* {
            text-align: center
        }

        .tab-content .ckbox {
            display: inline-block;
            width: 30px;
            text-align: left
        }
    </style>
    <table class="table table-bordered mb30">
        <thead>
            <tr>
                <th style="width:12%">订单编号</th>
                <th style="width:10%">验证时间</th>
                <th style="width:10%">验证数量</th>
                <th style="width:10%">供应商</th>
                <th style="width:10%">景区</th>
                <th style="width:10%">验证景点</th>
                <th style="width:10%">操作员</th>
                <th style="width:10%">设备类型</th>
                <th style="width:10%">设备编号</th>
                <th style="width:10%">设备名称</th>
                <th style="width:60px;">操作</th>
            </tr>
        </thead>
        <tbody>
			<?php foreach($verifications as $verification) { ?>
			<tr>
				<td><?= $verification['record_code'] ?></td>
				<td><?= date('Y-m-d H:i:s', $verification['created_at']) ?></td>
				<td><?= $verification['num'] ?></td>
				<td><?= empty($verification['organization'][0]['name']) ? '' :  $verification['organization'][0]['name'] ?></td>
				<td>
				<?php
					foreach( $verification['landscapes'] as $v ) {
						echo $v['name'] . '<br/>';
					}
				?>
				</td>
				<td><?= $verification['pois'] ? implode(',', TwoDimensionalArray::columns($verification['pois'], 'name')) : '全部' ?>
				</td>
				<td><?= $verification['cancel_name'] ? $verification['cancel_name'] : $verification['user_name'] ?></td>
				<td><?php if(isset($verification['device'][0]['type'])) { 
							echo $verification['device'][0]['type'] ? '闸机' : '手持'; 
						} else {
							echo '无';
						}
					 ?>
				</td>
				<td><?= $verification['equipment_code'] ? $verification['equipment_code'] : '无' ?></td>
				<td><?= empty($verification['device'][0]['name']) ? '无' : $verification['device'][0]['name'] ?></td>
				<td>
					<?php if($verification['cancel_status']) { ?>
					已撤销
					<?php } else { ?>
					<a class="clearPart" href="javascript:void(0)" onclick="cancel(<?= $verification['id'] ?>);">撤销</a>
					<?php } ?>
				</td>
			</tr>
			<?php } ?>
        </tbody>
    </table>
    <div style="text-align: left;" class="panel-footer">
        订单数：&nbsp;<span style="color: red; font-size: 17px;"><?= $orderNums ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 总人次：&nbsp;<span style="color: blue; font-size: 17px;"><?= $totalNums ?></span>&nbsp;
        <div id="basicTable_paginate" class="pagenumQu">
            <?php
            if(!empty($verifications)) {
            $this->widget('common.widgets.pagers.ULinkPager', array(
                'cssFile' => '',
                'header' => '',
                'prevPageLabel' => '上一页',
                'nextPageLabel' => '下一页',
                'firstPageLabel' => '',
                'lastPageLabel' => '',
                'pages' => $pages,
                'maxButtonCount' => 5
                ) // 分页数量
            );
            }
            ?>
        </div>
    </div>
</div>
<!-- contentpanel -->    

<div id='verify-modal' class="modal fade modal-bank" tabindex="-1" role="dialog"></div>

<script src="/js/async.names.js"></script>
<script>
 function modal_jump(obj) {
        $('#verify-modal').html('');
        $.get($(obj).attr('href'), function(data) {
            $('#verify-modal').html(data);
        });
    }

window.cancel = function(id) {
    PWConfirm('您是否需要撤销该操作?', function() {
        $.post('/agency/verification/cancel/', {id: id}, function(data) {
            if (data.error) {
                setTimeout(function() {
                	alert(data.msg);
                }, 1000);
            } else {
                window.location.reload();
            }
        }, "json");
    });
    return false;
}


jQuery(document).ready(function() {

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
    jQuery('.select2').select2();

});

</script>