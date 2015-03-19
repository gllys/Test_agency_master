//表单提交 将enter键绑定到表单提交事件
$('#btn-login').click(function() {
	self_form_submit();
});

$(document).bind('keydown', function (e) {
	var key = e.keyCode;
	if(key == 13){
		self_form_submit();
	}
});

function self_form_submit(){
	// var reg = /^[\u4E00-\u9FA5\uf900-\ufa2d\w\.\s]{1,16}$/;
	// var nameVal = $('input[name=account]').val();
	// var passVal = $('input[name=password]').val();
	// if(!reg.test(nameVal)){
	// 	alert('用户名须在6-16个字符！');
	// 	return false;
	// }else if(!reg.test(passVal)){
	// 	alert('密码须在6-16个字符！');
	// 	return false;
	// }else{
		$('#login-form').ajaxSubmit({dataType: 'json',success: function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				alert(tmp_errors);
			}else if(data['data'][0]['id'] && data['data'][0]['account']){
				location.href='landscape_lists.html';
                        }
	
		}});
		return false;
	// }
}