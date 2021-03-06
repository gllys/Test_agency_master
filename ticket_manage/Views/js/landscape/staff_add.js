$(document).ready(function() {
	
// 表单验证
	$('#staff-form').validationEngine({
		promptPosition : 'topLeft',
		addFailureCssClassToField: 'error'
	});
	
	$('#staff-form-button').click(function(){
		var obj = $('#staff-form');
		var organization_id = $('#organization_id').val();
		if(obj.validationEngine('validate')== true){
			$.post('index.php?c=landscape&a=saveStaff', obj.serialize(),function(data){
				if(data.errors){
					var tmp_errors = '';
					$.each(data.errors, function(i, n){
						tmp_errors += n;
					});
					var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>保存用户失败!'+tmp_errors+'</div>';
					$('#show_msg').html(warn_msg);
				}else if(data['data'][0]['id']){
					if($("input[name=type]").val() == 'edit'){
					var type_msg = '修改成功';
					}else{
					var type_msg = '新增成功';
					}
					var succss_msg = '<div class="alert alert-success"><strong>'+type_msg+'!</strong> 2 秒后跳转到员工列表页..</div>';
					$('#show_msg').html(succss_msg);
					setTimeout("location.href='landscape_staff_"+organization_id+".html'", 3000);
				}
			},"json");
		
		};
		return false;
	});
});