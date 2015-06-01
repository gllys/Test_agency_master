<?php
// var_dump($product);
?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
			<h4 class="modal-title">增加渠道属性</h4>
		</div>
		<form id="channel" method="post" enctype="mutltipart/form-data">
			<input type="hidden" value="" name="id">
			<div class="modal-body">
				<div class="form-group">
					<label class="col-sm-4 control-label">请选择添加渠道:</label>
					<div class="col-sm-8" style="position: static">
						<select class="select2" data-placeholder="Choose One" style="width: 300px;" id="id" name="id">
							<option selected='selected' value="">请选择一个渠道</option>
							<?php foreach($channels as $channel) { ?>
							<option value="<?= $channel['id'] ?>"><?= $channel['name'] ?></option>
							<?php } ?>		
						</select>
					</div>
				</div>
				<div class="form-group uploadFile">
					<label class="col-sm-4 control-label">请选择文档：</label>
					<div class="col-sm-8" style="position: static">
						<span id="doc"><input type="text" disabled="disabled" value="" /></span> <input type="hidden" id="hidFileName" /> <input type="button" id="btnUploadFile" value="上传" /> <input style="display: none;" type="button" id="btnDeleteFile" value="删除" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">请输入文档开发人员:</label>
					<div class="col-sm-8" style="position: static">
						<input type="text" class="form-control validate[required]" tag="文档开发人员" name="author" id="author" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">备注:</label>
					<div class="col-sm-8" style="position: static">
						<textarea type="text" rows="6" class="form-control validate[required]" tag="备注" name="remark" id="remark"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" id="cancel" style="margin: 0 10%;">取消</button>
				<button type="button" class="btn btn-default" style="margin: 0 10%;">
					<a id="preview" href="/channel/config/preview?id=0" target="_blank">预览</a>
				</button>
				<button type="button" class="btn btn-success" id="update" style="margin: 0 10%;">上传</button>
			</div>
		</form>
	</div>
</div>
<!-- 图片上传-->
<script type="text/javascript" charset="utf8" src="/js/ajaxUpload.js"></script>
<script type="text/javascript">
var id;
$('select[name="id"]').change(function() {
	id = $(this).val();
	ajaxUploadObj._settings.action = '/channel/config/upload/id/' + id;
	$('#preview').attr('href', '/channel/config/preview?id=' + id);
});

init();   
//初始化  
function init() {  
    //初始化文档上传  
    var btnFile = document.getElementById("btnUploadFile");  
    var doc = document.getElementById("doc");  
    var hidFileName = document.getElementById("hidFileName");  
    document.getElementById("btnDeleteFile").onclick = function() { DelFile(doc, hidFileName); };  
    g_AjxUploadFile(btnFile, doc, hidFileName);  
}

//文档上传  
function g_AjxUploadFile(btn, doc, hidPut, action) {
    var button = btn, interval;
    ajaxUploadObj = 
    new AjaxUpload(button, {
    action: ((action == null || action == undefined) ? '/channel/config/upload/id/0' : action),  
        data: {},  
        name: 'template_name',  
        onSubmit: function(file, ext) {
            if($('#id').val() == '') {
				alert('请先选择渠道');
				return false;
            }
            if (!(ext && /^(html)$/.test(ext))) {  
                alert("您上传的文档格式不对，请重新选择！");  
                return false;  
            }  
        },  
        onComplete: function(file, response) {  
            flagValue = response;  
            if (flagValue == "1") {  
                alert("您上传的文档格式不对，请重新选择！");  
            }  
            else if (flagValue == "2") {  
                alert("您上传的文档大于2M，请重新选择！");  
            }  
            else if (flagValue == "3") {  
                alert("文档上传失败！");  
            }  
            else {  
                hidPut.value = response;
                doc.innerHTML = response;
            }  
        }  
    });  
}  

$(function() {
	$('.select2').select2();
	
	// 取消按钮
	$('#cancel').click(function() {
		$('.close').trigger('click');
	});

	// 上传按钮
	$('#update').click(function() {
		if($('#id').val() == '') {
			$('#id').parent().PWShowPrompt('请选择添加渠道');
            return false;
        }
		if($('#doc').text() == '') {
			$('#btnUploadFile').PWShowPrompt('请上传文档');
            return false;
        }
		if($('#author').val() == '') {
			$('#author').parent().PWShowPrompt('请输入文档开发人员');
            return false;
        }
		if($('#remark').val() == '') {
			$('#remark').parent().PWShowPrompt('请输入备注');
            return false;
        }
		$.post('/channel/config/update', $('#channel').serialize(), function(data){
			if(data.error) {
				alert(data.msg);
			} else {
				location.reload();
			}			
		}, 'json')
	});
	
});

</script>