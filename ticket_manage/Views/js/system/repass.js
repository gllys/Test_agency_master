$(document).ready(function() {
	
// 表单验证
	$('#repass-form').validationEngine({
		promptPosition : 'topLeft',
		addFailureCssClassToField: 'error'
	});
	$('#repass-form-button').click(function(){
		var obj = $('#repass-form');
		if(obj.validationEngine('validate')== true){
			$.post('system_repass.html', obj.serialize(),function(data){
				if(data.errors){
					var tmp_errors = '';
					$.each(data.errors, function(i, n){
						tmp_errors += n;
					});
					var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>'+tmp_errors+'</div>';
					$('#show_msg').html(warn_msg);
				}else{
					type_msg = '修改成功';
					var succss_msg = '<div class="alert alert-success"><strong>'+type_msg+'!</strong> 2 秒后跳转到修改密码页..</div>';
					$('#show_msg').html(succss_msg);
					setTimeout("location.href='system_repass.html'", 2000);
				}
			},"json");
		};
	
		return false;
	});
	
});