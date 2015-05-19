<div class="modal-dialog" style="width: 1100px !important;">
	<div class="modal-content">
		<div id="show_msg1"></div>
		<div class="modal-header">
			<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
			<h4 class="modal-title"><?php echo $get['type'] == 1 ? '驳回公告' : '查看公告'; ?></h4>
		</div>
		<div class="form-group">
			<div class="col-sm-4">
				<label class="control-label col-sm-3">消息类型 </label>
				<label class="control-label col-sm-9"><?php echo (isset($sys_type) && $sys_type == 0) ? "公告" : "非公告"; ?></label>
			</div>
			<div class="col-sm-4">
				<label class="control-label col-sm-3">用户名称 </label>
				<label class="control-label col-sm-9"><?php echo isset($name) ? $name : ""; ?></label>
			</div>
			<div class="col-sm-4">
				<label class="control-label col-sm-3">提交日期 </label>
				<label class="control-label col-sm-9"><?php echo isset($created_at) ? date("Y-m-d H:i:s", $created_at) : ""; ?></label>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-4">
				<label class="control-label col-sm-3">发给 </label>
				<label class="control-label col-sm-9">
					<?php
					if (isset($receiver_organization_type)) {
						switch ($receiver_organization_type) {
							case '0': echo "仅合作分销商";
								break;
							case '1': echo "全平台";
								break;
							case '2': echo "仅分销商";
								break;
							case '3': echo "仅供应商";
								break;
						}
					};
					?>
				</label>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<label class="control-label col-sm-1">内容 </label>
				<label class="control-label col-sm-11"><?php echo isset($content) ? $content : ""; ?></label>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<label class="control-label col-sm-1"> 驳回理由 </label>
				<textarea name="remark" id="remark" class="form-control col-sm-5" cols="60" rows="5" <?php echo (isset($is_allow) && $is_allow != 0) ? 'disabled="disabled"' : "" ?>><?php echo (isset($is_allow) && $is_allow == 2) ? $remark : ""; ?></textarea>
			</div>
		</div>
		<div class="modal-footer">
			<?php $disabled = ((isset($is_allow) && $is_allow != 0)); ?>
			<a class="btn btn-warning" onclick="pubNotice(<?php echo $get['id'];?>, 2)" <?php echo $disabled ? 'disabled="disabled"' : "" ?>>驳回</a>
			<a class="btn btn-success" onclick="pubNotice(<?php echo $get['id'];?>, 1)" <?php echo $disabled || $get['type']==2 ? 'disabled="disabled"' : "" ?>>同意发布</a>
            <button class="btn btn-default" aria-hidden="true" data-dismiss="modal" class="close" type="button">取消</button>
		</div>
	</div>
</div>
<script type="text/javascript">

    $(document).ready(function () {

        /* 公告操作 type 1 发布公告 2 驳回公告 */
        this.pubNotice = function (id, type) {
			// 驳回操作
			var name = "发布公告";
			var postdata = {id:id, type:type};
			if(type == 2) {
				if($('#remark').val() == '') {
					$('#remark').PWShowPrompt('驳回理由不能为空！');
					return ;
				}
				postdata.remark = $('#remark').val();
				name = "驳回公告";
			} 
			$.post('/system/notice/pub', postdata, function(data){
				if (data.code == 'succ') {
					var type_msg, redirect;
					type_msg = name;
					redirect = '/site/switch/#/system/notice/';
					alert(type_msg+'成功!', function() {
                        location.href = '/site/switch/#/system/notice/';
                    });
				} else {
					var tmp_errors = '';
					$.each(data.message, function (i, n) {
						tmp_errors += n;
					});
                    alert(tmp_errors);
				}
			}, 'json');
        }

    });

</script>