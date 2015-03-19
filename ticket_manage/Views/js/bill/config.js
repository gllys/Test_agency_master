$('#setting-form-button').click(function(){
	var obj = $('#setting-form');
	var cycle = $('#account_cycle').val();
	var day   = $('#account_cycle_day').val();
	
	//结算周期
	if (cycle == 'undefined') {
		alert('请选择结算周期');
		$('#account_cycle').focus();
		return false;
	}

	//结算日
	if(day == '__NULL__') {
		alert('请选择结算日');
		$('#account_cycle_day').focus();
		return false;
	}

	$.post('index.php?c=bill&a=saveSetting', obj.serialize(),function(data){
		if(data.errors){
			var tmp_errors = '';
			$.each(data.errors, function(i, n){
				tmp_errors += n;
			});
			var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
			$('#show_msg').html(warn_msg);
			location.href='#show_msg';
		}else if(data['data'][0]['conf_bill_type']){
			var type_msg,redirect;
			type_msg = '设置成功';
			redirect = 'bill_config.html';
			var succss_msg = '<div class="alert alert-success"><strong>'+type_msg+'!</strong></div>';
			$('#show_msg').html(succss_msg);
			location.href='#show_msg';
			setTimeout("location.href='"+redirect+"'", 2000);
		}
	}, "json");
	return false;
});

//确认结算并生成账单
$('#doSettle').click(function(){
	if ($('#password').val()) {
		$.post('index.php?c=bill&a=checkPass', {password : $('#password').val()},function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
				$('#settle_show_msg').html(warn_msg);
				location.href='#settle_show_msg';
			}else if(data['data']['result']){
				var type_msg,redirect;
				redirect = 'bill_config.html';
				var succss_msg = '<div class="alert alert-success"><strong>'+data['data']['result']+'!</strong></div>';
				$('#settle_show_msg').html(succss_msg);
				location.href='#settle_show_msg';
				setTimeout("location.href='"+redirect+"'", 2000);
			}
		}, "json");
		return false;
	} else {
		alert('请输入密码进行改期');
		$('#password').focus();
	}
});

//修改结算日的格式
function changeDayShow(type)
{
	var obj = $('#account_cycle_day');
	var default_option = '<option value="__NULL__">请选择结算日</option>';
	if(type == 'month'){
		obj.html(default_option+phpvars.month_tpl);
	}else if(type == 'week'){
		obj.html(default_option+phpvars.week_tpl);
	}else{
		obj.html('<option value="__NULL__">请选择结算日</option>');
	}
	$.uniform.update('#account_cycle_day');
}