<?php
use common\huilian\utils\TwoDimensionalArray;
?>
<style>
.ui-datepicker {
	z-index: 9999 !important
}
</style>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns" style="display: none;">
				<a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title=""><i class="fa fa-minus"></i></a> <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title=""><i class="fa fa-times"></i></a>
			</div>
			<!-- panel-btns -->
			<h4 class="panel-title">设备管理</h4>
		</div>
		<div class="panel-body">
			<form class="form-inline" method="get" action="/scenic/device/index">
				<div class="mb10">
					<div class="form-group" style="">
						<input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="s_time" value="<?php if (isset($_GET['s_time'])) echo $_GET['s_time'] ?>" placeholder="开始日期" type="text" readonly="readonly">
                        ~
                        <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="e_time" value="<?php if (isset($_GET['e_time'])) echo $_GET['e_time'] ?>" placeholder="结束日期" type="text" readonly="readonly">
					</div>
					<!-- form-group -->
					<div class="form-group" style="margin-left: 15px; margin-right: 0px;">是否绑定景区:</div>
					<!-- form-group -->
					<div class="form-group" style="width: 80px;">
						<select name="is_bind" class="select2 no-match" style="width: 80px; padding-left: 6px; height: 32px;">
							<option value="" <?= isset($_GET['is_bind']) && ($_GET['is_bind'] === '') ? ' selected' : '' ?>>全部</option>
							<option value="1" <?= isset($_GET['is_bind']) && ($_GET['is_bind'] == 1) ? ' selected' : '' ?>>是</option>
							<option value="0" <?= isset($_GET['is_bind']) && ($_GET['is_bind'] === '0') ? ' selected' : '' ?>>否</option>
						</select>
					</div>
					<!-- form-group -->
					<div class="form-group" style="margin-left: 15px; margin-right: 0px;">是否安装:</div>
					<!-- form-group -->
					<div class="form-group" style="width: 80px;">
						<select name="is_fix" class="select2 no-match" style="width: 80px; padding-left: 6px; height: 32px;">
							<option value="" <?= isset($_GET['is_fix']) && ($_GET['is_fix'] === '') ? ' selected' : '' ?>>全部</option>
							<option value="1" <?= isset($_GET['is_fix']) && ($_GET['is_fix'] == 1) ? ' selected' : '' ?>>是</option>
							<option value="0" <?= isset($_GET['is_fix']) && ($_GET['is_fix'] === '0') ? ' selected' : '' ?>>否</option>
						</select>
					</div>
					<!-- form-group -->
					<div class="form-group" style="margin-right: 0px;">景区:</div>
					<!-- form-group -->
					<div class="form-group" style="width: 150px;">
						<input class="form-control" name="scenic_name" value="<?= isset($_GET['scenic_name']) ? $_GET['scenic_name'] : '' ?>">
					</div>
					<div class="form-group">
						<button class="btn btn-primary btn-xs" type="submit">查询</button>
					</div>
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
				<th style="width: 12%">设备编号</th>
				<th style="">设备类型</th>
				<th style="">设备名称</th>
				<th style="">使用类型</th>
				<th style="">景区</th>
				<th style="">景点</th>
				<th style="width: 60px;">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($devices as $device) { ?>
			<tr>
				<td><?= $device['code'] ?></td>
				<td><?= $device['type'] ? '闸机' : '手持验票机' ?></td>
				<td><?= $device['name'] ?></td>
				<td>
					<?php if($device['landscape_id']) { ?>	
						<?= $device['scene'] ? ($device['scene'] == 1 ? '入园' : '出园') : '未选择' ?>
					<?php } else { ?>
						<select name="scene" class="select2 no-match" style="width: 80px; padding-left: 6px; height: 32px;">
								<option value="1">入园</option>
                                <?php if(!empty($device['type'])){ ?>
								<option value="2">出园</option>
                                <?php }?>
						</select> 
					<?php } ?>
				</td>
				<td>
					<?php if($device['landscape_id']) { ?>	
						<?= empty($device['landscape']['name']) ? '无' : $device['landscape']['name'] ?>
					<?php } else { ?>
						<select name="landscape_id" class="select2" style="width: 150px;">
                            <option value="0">请选择景区</option>
							<?php foreach($landscapeNames as $k => $v) { ?>
							<option value="<?= $k ?>"><?= $v ?></option>
							<?php } ?>
						</select>
					<?php } ?>
				</td>
				<td>
					<?php if($device['landscape_id']) { ?>	
						<?= empty($device['poi']['name']) ? '全部' : $device['poi']['name'] ?>
					<?php } else { ?>
						<select name="poi_id" class="select2" style="width: 150px;">
								<option value="0">全部</option>
						</select>
					<?php } ?>
				</td>
				<td>
					<?php if($device['landscape_id']) { ?>
					<a class="clearPart" href="javascript:unbind(<?= $device['id'] ?>)">解绑</a>
					<?php } else { ?>
					<a class="bind clearPart" data-value="<?= $device['id'] ?>" href="javascript:;">绑定</a>
					<?php } ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<div style="text-align: left;" class="panel-footer">
		<div id="basicTable_paginate" class="pagenumQu">
            <?php
            if(!empty($devices)) {
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

}


// 解绑
function unbind(id) {
	$.post('/scenic/device/unbind', {id: id}, function(data) {
        alert('解绑成功！',function(){
            location.partReload();
    	});
	}, 'json');
}


jQuery(document).ready(function() {

	// 绑定
	$('.bind').click(function() {
		var id = $(this).attr('data-value')
		var tr = $(this).parents('tr');
		var scene = tr.find('select[name="scene"]').val();
		var landscapeId = tr.find('select[name="landscape_id"]').val();
		var poiId = tr.find('select[name="poi_id"]').val();

		// 如果poiId不为0，则type为1景点；如果为0，则type为0，景区。
		var type = poiId == 0 ? 0 : 1;
		var scene_id = type ? poiId : landscapeId;
		// 选择出园的时候，景点为空
        if(scene == 2){
            scene_id = landscapeId;
            type = 0;
        }
        
        if(landscapeId == '0'){
            alert('请选择景区');
        }else{
            $.post('/scenic/device/bind', {id: id, scene: scene, type: type, scene_id: scene_id, landscape_id: landscapeId}, function(data) {
                if(data.error) {
                    alert(data.msg);
                } else {
                    alert('绑定成功！',function(){
                        location.partReload();
                    }); 
                } 
            }, 'json');
        }
	});


	$('select[name="landscape_id"]').change(function() {
		var landscapeId = $(this).val();
		var poiSelect = $(this).parent().next().find('select[name="poi_id"]');
        var scene = $(this).parent().prev().find('select[name="scene"]').val();
        if(scene != 2){
            $.get('/assistive/landscape/poiOptions', {landscape_id: landscapeId}, function(data) {
                data = '<option value="0">全部</option>' + data;
                poiSelect.html(data);
                poiSelect.select2();
            });
        }
	});
    
    $('select[name="scene"]').change(function() {
		var tr = $(this).parents('tr');
		var scene = tr.find('select[name="scene"]').val();
        if(scene == 2){
            //隐藏景点和景区控件
		   tr.find('select[name="poi_id"]').prev().hide();
        }else{
		   tr.find('select[name="poi_id"]').prev().show();
           var landscapeId = $(this).parent().next().find('select[name="landscape_id"]').val();
           var poiSelect = $(this).parent().next().next().find('select[name="poi_id"]');
           $.get('/assistive/landscape/poiOptions', {landscape_id: landscapeId}, function(data) {
                data = '<option value="0">全部</option>' + data;
                poiSelect.html(data);
                poiSelect.select2();
            });
        }
	});

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
	jQuery('.select2.no-match').select2({
		minimumResultsForSearch: -1
	});
   
});

</script>