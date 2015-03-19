$(document).ready(function() {
	// 表单验证
	$('#addrole-form').validationEngine({
		promptPosition : 'topLeft',
		addFailureCssClassToField: 'error',
		autoHidePrompt: true,
		autoHideDelay:3000
	});

	$('input,select,textarea').bind('jqv.field.result',function(event,field,errorFound,prompText){ 
		var obj = field.parents('label').find('.note');
		prompText = prompText.replace(/[*|<br\/>]/g,'');
		if(errorFound){ 
			obj.html(prompText);
			obj.addClass('status-error');
		}else{ 
			obj.html(prompText);
			obj.removeClass('status-error');
		}
		errorFound==false; 
	});

	$('#addrole-form-button').click(function(){
		var obj = $('#addrole-form');
		if(obj.validationEngine('validate')== true){
			$.post('index.php?c=system&a=roleSave', obj.serialize(),function(data){
				if(data.errors){
					var tmp_errors = '';
					$.each(data.errors, function(i, n){
						tmp_errors += n;
					});
					var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
					$('#show_msg').html(warn_msg);
					location.href='#show_msg';
				}else if(data['data'][0]['id']){
					var type_msg,redirect;
					if(phpvars.pageType == 'edit'){
						type_msg = '编辑成功';
					}else{
						type_msg = '添加成功';
					}

					redirect = 'system_role.html';
					var succss_msg = '<div class="alert alert-success"><strong>'+type_msg+'!</strong></div>';
					$('#show_msg').html(succss_msg);
					location.href='#show_msg';

					setTimeout("location.href='"+redirect+"'", 2000);
				}
			}, "json");
		};
		return false;
	});

	$('.permission th input').each(function(){
		if(!this.checked){
			$(this).parents('tr').find('td input').iCheck('uncheck');
			$(this).parents('tr').find('td input').iCheck('disable');
		}else{
			$(this).parents('tr').find('td input').iCheck('enable');
		}
	});
	$('.permission th input').click(function(event){
		if(!this.checked){
			$(this).parents('tr').find('td input').iCheck('enable')
		}else{
			$(this).parents('tr').find('td input').iCheck('uncheck');
			$(this).parents('tr').find('td input').iCheck('disable')
		}
	})

})