<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<style>
.ui-datepicker { z-index:9999!important }
</style>
<div class="contentpanel" id="maincontent">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
                <h4 class="panel-title">
					<a class="btn btn-primary btn-sm pull-right" href="/system/notice/add/" style="color: #ffffff">新建公告</a>公告管理
				</h4>
			</h4>
		</div>
		<div id="show_msg"></div>
		<div class="panel-body">
			<form class="form-inline" method="get" action="/system/notice">
				<div class="form-group">
					<div class="control-label" style="display:inline-block; line-height: 28px;margin-right:10px">提交时间:</div>
					<input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" class="form-control datepicker" type="text" readonly="readonly" placeholder="开始时间" value="<?php echo isset($get['start_date']) ? $get['start_date'] : ""; ?>"> ~
					<input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" class="form-control datepicker" type="text" readonly="readonly" placeholder="结束时间" value="<?php echo isset($get['end_date']) ? $get['end_date'] : ""; ?>">
				</div>
				<div class="form-group">
					<div class="control-label" style="display:inline-block; line-height: 28px;">发给:</div>
				</div>
				<div class="form-group" style="width: 130px;">
					<select class="form-control select2" name="receiver_organization_type">
						<option value="" <?php echo (!isset($get['receiver_organization_type']) || $get['receiver_organization_type'] === '') ? 'selected="selected"' : ''; ?>>全部</option>
						<option value="1" <?php echo (isset($get['receiver_organization_type']) && $get['receiver_organization_type'] == '1') ? 'selected="selected"' : ''; ?>>全平台</option>
						<option value="2" <?php echo (isset($get['receiver_organization_type']) && $get['receiver_organization_type'] == '2') ? 'selected="selected"' : ''; ?>>仅分销商</option>
						<option value="3" <?php echo (isset($get['receiver_organization_type']) && $get['receiver_organization_type'] == '3') ? 'selected="selected"' : ''; ?>>仅供应商</option>
						<option value="0" <?php echo (isset($get['receiver_organization_type']) && $get['receiver_organization_type'] === '0') ? 'selected="selected"' : ''; ?>>仅合作分销商</option>
					</select>
				</div>
				<div class="form-group">
					<div class="control-label" style="display:inline-block; line-height: 28px;">发布人:</div>
				</div>
				<div class="form-group" style="width: 130px;">
					<select class="form-control select2" name="send_source">
						<option value="" <?php echo (!isset($get['send_source']) || $get['send_source'] === '') ? 'selected="selected"' : ''; ?>>全部</option>
						<option value="0" <?php echo (isset($get['send_source']) && $get['send_source'] === '0') ? 'selected="selected"' : ''; ?>>汇联运营团队</option>
						<option value="1" <?php echo (isset($get['send_source']) && $get['send_source'] === '1') ? 'selected="selected"' : ''; ?>>供应商</option>
					</select>
				</div>
				<div class="form-group" style="width: 110px;">
					<select class="form-control select2" name="is_allow">
						<option value=""  <?php echo (!isset($get['is_allow']) || $get['is_allow'] === '') ? 'selected="selected"' : ''; ?>>所有状态</option>
						<option value="0" <?php echo (isset($get['is_allow']) && $get['is_allow'] === '0') ? 'selected="selected"' : ''; ?>>未发布</option>
						<option value="1" <?php echo (isset($get['is_allow']) && $get['is_allow'] === '1') ? 'selected="selected"' : ''; ?>>已发布</option>
						<option value="2" <?php echo (isset($get['is_allow']) && $get['is_allow'] === '2') ? 'selected="selected"' : ''; ?>>已驳回</option>
					</select>
				</div>
				<!-- form-group -->
				<div class="form-group">
					<button type="submit" id="searchBtn" class="btn btn-primary btn-sm">搜索</button>
				</div>
			</form>
		</div>
	</div>
	<div class="table-responsive">
        <table class="table table-bordered mb30" style="min-width: 1006px;">
			<thead>
			<th width="60px">编号</th>
			<th width="100px">发布用户名称</th>
			<th width="100px">操作人</th>
			<th width="100px">发布人</th>
			<th width="150px">提交日期</th>
			<th>主题</th>
			<th width="80px">状态</th>
			<th>操作</th>
			</thead>
			<tbody id="notice-body">
                <?php foreach ($datas as $v): ?>
					<tr>
						<td><?php echo isset($v['id']) ? $v['id'] : ''; ?></td>
						<td><?php echo isset($users[$v['send_user']]['account']) ? $users[$v['send_user']]['account'] : ''; ?></td>
						<td><?php echo isset($users[$v['send_user']]['name']) ? $users[$v['send_user']]['name'] : ''; ?></td>
						<td><?php echo (isset($v['send_source']) && $v['send_source'] == 1) ? "供应商" : "汇联运营团队"; ?></td>
						<td><?php echo date('Y-m-d H:i:s', $v['created_at']); ?></td>
						<td class="center" title="<?php echo $v['title']; ?>">
							<?php
							$length = mb_strlen($v['title'], 'UTF-8');
							if ($length > 15) {
								echo mb_substr($v['title'], 0, 15, 'UTF-8') . "...";
							} else {
								echo $v['title'];
							}
							?>
						</td>
						<td><?php echo $v['is_allow'] == 0 ? "未发布" : ($v['is_allow'] == 1 ? "已发布" : "已驳回"); ?></td>
						<td>
							<a onclick="modal_jump_view(<?php echo isset($v['id']) ? $v['id'] : '';?>, 0)" data-target=".modal-notice" data-toggle="modal">查看</a>
							<?php if ($v['is_allow'] == 0) { ?>
								<a class="clearPart" onclick="pubNotice(<?php echo $v['id'];?>)">发布</a>
								<a onclick="modal_jump_view(<?php echo isset($v['id']) ? $v['id'] : '';?>, 2)" data-target=".modal-notice" data-toggle="modal">驳回</a>
							<?php } else if($v['is_allow'] == 1) { ?>
								<a class="clearPart" onclick="revocation(<?= $v['id'] ?>)">撤回</a>
							<?php } else { ?>
								<a class="btn btn-warning btn-xs" disabled="disabled">驳回</a>
							<?php } ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="panel-footer">
		<div class="pagenumQu">
			<?php
			if ($datas) {
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
<div id='verify-modal' class="modal fade modal-notice" tabindex="-1" role="dialog">
</div>
<script type="text/javascript">
    $(document).ready(function () {

        // 设置时间空间
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

		// id表示操作公告id 
		// type 0表示查看公告
		// type 2表示驳回公告
		this.modal_jump_view = function(id, type) {
			$('#verify-modal').html('');
			$.get('/system/notice/view/id/'+id+'/type/'+type, function(data) {
				$('#verify-modal').html(data);
			});
		}

		function showMsg(data, type_msg) {
			if (data.code == 'succ') {
                setTimeout(function() {
                    alert(type_msg + '成功!', function () {
                        location.partReload();
                    });
                }, 500);
			} else {
				var tmp_errors = '';
				$.each(data.message, function (i, n) {
					tmp_errors += n;
				});
                setTimeout(function() {
                    alert(tmp_errors);
                }, 500);
			}

		}
        this.pubNotice = function (id) {
			// 驳回操作
			PWConfirm("确定要发布这条公告吗", function() {
				$.post('/system/notice/pub', {id:id, type:1}, function(data){
					showMsg(data, '发布公告');
				}, 'json');
			});
        }
		
        this.delNotice = function (id) {
			// 驳回操作
			PWConfirm("确定要删除这条公告吗", function() {
				$.post('/system/notice/del/id/'+id, function(data){
					showMsg(data, '删除公告');
				}, 'json');
			});
        }

		// 撤回
        this.revocation = function(id) {
			PWConfirm("确定要撤回这条公告吗", function() {
				$.post('/system/notice/revocation',{id:id}, function(data){
                    if(data.error) {
                        setTimeout(function() {
                            alert(data.msg);
                        }, 500);
                    } else {
                        location.partReload();
                    }
				}, 'json');
			});
        }	
		
		/* select2 框体设置 */
		$("[name=receiver_organization_type],[name=send_source],[name=is_allow]").select2();
		
	});

</script>
