<div class="contentpanel" id="maincontent">
    <div class="row">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-btns">
					<a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
					<a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
				</div>
				<h4 class="panel-title">新建公告</h4>
			</div>
			<div id="show_msg"></div>
			<div class="panel-body nopadding">
				<form method="post" action="/system/notice/preview" target="_blank" id="addnotice-form" class="form-horizontal clearPart">
					<div class="form-group">
						<label class="col-sm-1 control-label"><span class="text-danger">*</span> 主题</label>
						<div class="col-sm-5">
							<input type="text" name="title" id="title" placeholder="" maxlength="20" tag="主题" class="validate[required] form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-1 control-label"><span class="text-danger">*</span>发给</label>
                                                <div class="col-sm-5" style="width:500px;">
							<div class="radio-inline">
								<label>
									<input type="radio" name="receiver_organization_type" id="receiver_organization_type" checked value="1"/>全平台
								</label>
							</div>
							<div class="radio-inline">
								<label>
									<input type="radio" name="receiver_organization_type" id="receiver_organization_type" value="2"/>仅分销商
								</label>
							</div>
							<div class="radio-inline">
								<label>
									<input type="radio" name="receiver_organization_type" id="receiver_organization_type" value="3"/>仅供应商
								</label>
							</div>
                                                        <div class="radio-inline">
								<label>
									<input type="radio" name="receiver_organization_type" id="receiver_organization_type" value="4"/>仅电子票务系统
								</label>
							</div>
                                                        <div class="radio-inline">
								<label>
									<input type="radio" name="receiver_organization_type" id="receiver_organization_type" value="5"/>仅验票账号
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-1 control-label"><span class="text-danger">*</span>内容</label>
						<div class="col-sm-10">
							<textarea id="remark" name="content"  style=" width: 100%;height: 250px; visibility: hidden;"></textarea>
						</div>
					</div>
					<div class="panel-footer" style="padding-left:8%">
						<input id="preview" type="submit" class="btn btn-primary" value="预览">
						<a href="/system/notice/" class="btn btn-default">取消</a>
						<input type="button" class="btn btn-success" id="pubNotice" value="发布"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script  charset="utf-8" src="/js/kindeditor-4.1.10/kindeditor.js"></script>
<script  charset="utf-8" src="/js/kindeditor-4.1.10/lang/zh_CN.js"></script>
<script type="text/javascript">

	$(document).ready(function () {

        var editor = KindEditor.create('#remark', {
            resizeType: 1,
            allowPreviewEmoticons: false,
            allowImageUpload: false,
            items: [
                'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'underline',
                'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                'insertunorderedlist', 'link']
        });

		function showMsg(data, type_msg) {
			if (data.code == 'succ') {
				var type_msg;
                alert(type_msg + '成功!', function() {
                    location.href='/site/switch/#/system/notice/';
                });
			} else {
				var tmp_errors = '';
				$.each(data.message, function (i, n) {
					tmp_errors += n;
				});
                alert(tmp_errors);
			}

		}
		/* 新建发布公告 */
        $('#pubNotice').click(function () {

            $(this).attr('disabled', 'disabled');
            /**
             * @desc 提交表单信息，保存
             */
            var obj = $("#addnotice-form");
            editor.sync();
            obj.validationEngine({
                autoHidePrompt: true,
                scroll: false,
                autoHideDelay: 3000,
                maxErrorsPerField: 1
            });
            if (obj.validationEngine('validate') == true) {
                $.post('/system/notice/save', obj.serialize(), function (result) {
                    if (result.code == 1) {
                        $('#remark').PWShowPrompt('公告内容不能为空！');
                        $(this).removeAttr('disabled');
                    } else {
                        showMsg(result, '发布公告')
                    }
                }, 'json');
            } else {
                $(this).removeAttr('disabled');
            }
            return false;
        });

        // 内容改变，激活发布按钮
        $('#title').change(function() {
            $('#pubNotice').removeAttr('disabled');
        });

		//　预览，本处对公告的完整性进行检测
		$('#preview').click(function() {
			if($('input[name="title"]').val() == '') {
				$('input[name="title"]').PWShowPrompt('公告主题不能为空');
				return false;
			}

			if(editor.html() == '') {
				$('#remark').PWShowPrompt('公告内容不能为空！');
				return false;
			}
			
		});
		
	});

</script>