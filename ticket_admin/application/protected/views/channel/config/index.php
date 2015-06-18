<?php
use common\huilian\utils\TwoDimensionalArray;
use common\huilian\utils\GET;
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
			<h4 class="panel-title">
				属性配置<a style="float: right; color: white;" href="/channel/config/updateList" class="btn btn-sm btn-primary clearPart" onclick="modal_jump(this);" data-target=".modal-bank" data-toggle="modal">增加渠道属性</a>
			</h4>
		</div>
		<div class="panel-body">
			<form class="form-inline" method="get" action="/channel/config/index">
				<div class="mb10">
					<!-- form-group -->
					<div class="form-group" style="width: 150px;">
						<select name="id" class="select2" style="width: 150px;">
							<option value="">请选择渠道</option>
							<?php foreach($cannelNamesWithTemplate as $k => $v) {?>         
							<option value="<?= $k ?>" <?= GET::name('id') == $k ? ' selected' : '' ?>><?= $v ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group" style="">
						<input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="created_at_start" value="<?php if (isset($_GET['created_at_start'])) echo $_GET['created_at_start'] ?>" placeholder="开始日期" type="text" readonly="readonly"> ~ <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="created_at_end" value="<?php if (isset($_GET['created_at_end'])) echo $_GET['created_at_end'] ?>" placeholder="结束日期"
							type="text" readonly="readonly">
					</div>
					<!-- form-group -->
					<div class="form-group" style="width: 150px;">
						<input type="text" name="template_name" class="form-control" placeholder="文件名称" value="<?= empty($_GET['template_name']) ? '' : $_GET['template_name'] ?>">
					</div>
					<!-- form-group -->
					<div class="form-group" style="width: 150px;">
						<input type="text" name="author" class="form-control" placeholder="开发人员" value="<?= empty($_GET['author']) ? '' : $_GET['author'] ?>">
					</div>
					<!-- form-group -->
					<div class="form-group" style="width: 150px;">
						<select name="op_user" class="select2" style="width: 150px;">
							<option value="">请选择操作员</option>
							<?php foreach($admins as $v) {?>         
							<option value="<?= $v ?>" <?= GET::name('op_user') == $v ? ' selected' : '' ?>><?= $v ?></option>
							<?php } ?>
						</select>
					</div>
					<!-- form-group -->
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

.rules {
    position: relative;
    display: inline-block;
}
.rules+.rules {
    margin-left: 20px;
}
.rules > span {
    color: #999;
    font-size: 12px;
    cursor: pointer
}
.rules > div >span {
    margin: 0 10px
}
.rules > div {
    display: none;
    position: absolute;
    top: 15px;
    left: 50px;
    z-index: 999;
    width: 500px;
    padding: 10px;
    background-color: #f6fafd;
    border: 1px solid #2a84d2;
    border-radius: 2px;
    box-shadow: 0 0 10px rgba(0, 0, 0, .2);
    word-wrap: break-word;
}
.rules > div .table {
    background: none;
}
.rules > div .table tr > * {
    border: 1px solid #e0d9b6
}
.rules:hover > div {
    display: block;
}
.prov_p {
width: 120px;
display: inline-block;
height: 20px;
text-align: left;
cursor: pointer;
}
.table-bordered th:nth-child(1){padding-left:35px;}
.table-bordered td:nth-child(1){padding-left:35px;}
.ui-datepicker{ z-index:9999!important }
</style>
	<table class="table table-bordered mb30">
		<thead>
			<tr>
				<th style="width:10%;">渠道名称</th>
				<th style="width:10%;">文件名称</th>
				<th style="width:10%;">上传时间</th>
				<th style="width:10%;">开发人员</th>
				<th style="width:10%;">操作人员</th>
				<th>备注</th>
				<th style="width:10%;">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($channels as $channel) { ?>
			<tr>
				<td><?= $channel['name'] ?></td>
				<td><?= $channel['template_name'] ?></td>
				<td><?= date('Y-m-d H:i:s', $channel['created_at']) ?></td>
				<td><?= $channel['author'] ?></td>
				<td><?= $channel['op_user'] ?></td>
				<td>
					<div class="rules">
						<span><?= mb_strlen($channel['remark'],'utf8') > 15 ? mb_substr($channel['remark'], 0, 15,'utf8') . '...' : $channel['remark'] ?></span>
						<?php if(mb_strlen($channel['remark'],'utf8') > 15) { ?>
						<div class="table-responsive">
							<table class="table table-bordered mb30"><?= $channel['remark'] ?></table>
						</div>
						<?php } ?>
					</div>
				</td>
				<td><a href="/channel/config/update?id=<?= $channel['id'] ?>" class="clearPart" onclick="modal_jump(this);" data-target=".modal-bank" data-toggle="modal">修改</a> <a href="javascript:;" class="clearPart" onclick="clearTemplate(<?= $channel['id'] ?>)">删除</a></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<div style="text-align: left;" class="panel-footer">
		<div id="basicTable_paginate" class="pagenumQu">
            <?php
            if(!empty($channels)) {
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

function del(id) {
	PWConfirm('确定要删除此渠道吗？', function(){
		$.get('/channel/config/del/', {id:id}, function(data) {
			if (data.error) {
				setTimeout(function(){
					alert(data.msg);
				}, 500);
			} else  {
				setTimeout(function(){
					alert('删除渠道成功', function () {location.reload();});
				}, 500);
			}
		}, 'json');
	});
}

function clearTemplate(id) {
	PWConfirm('确定要删除此渠道吗？', function(){
		$.get('/channel/config/clearTemplate/', {id:id}, function(data) {
			if (data.error) {
				setTimeout(function(){
					alert(data.msg);
				}, 500);
			} else  {
				setTimeout(function(){
					alert('删除渠道成功', function () {location.reload();});
				}, 500);
			}
		}, 'json');
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
	
	$('.select2').select2({
		minimumResultsForSearch: -1
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

});

</script>