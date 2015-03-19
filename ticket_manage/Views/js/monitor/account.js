$(document).ready(function() {
	
// 表单验证
	$('#staff-form').validationEngine({
		promptPosition : 'topLeft',
		addFailureCssClassToField: 'error'
	});
	
	$('#staff-form-button').click(function(){
		var obj = $('#staff-form');
		var supervise_id = $('#supervise_id').val();
		if(obj.validationEngine('validate')== true){
			$.post('index.php?c=monitor&a=saveAccount', obj.serialize(),function(data){
				if(data.errors){
					var tmp_errors = '';
					$.each(data.errors, function(i, n){
						tmp_errors += n;
					});
					var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>保存用户失败!'+tmp_errors+'</div>';
					$('#show_msg').html(warn_msg);
				}else{
					var succss_msg = '<div class="alert alert-success"><strong>保存成功!</strong> 2 秒后跳转到账号列表页面..</div>';
					$('#show_msg').html(succss_msg);
					setTimeout("location.href='/monitor_accountLists_"+supervise_id+".html?type="+$("input[name=type]").val()+"'", 3000);
				}
			},"json");
		
		};
		return false;
	});
});
