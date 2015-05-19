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

	$.post('/finance/config/savesetting', obj.serialize(),function(data){
		if(data.errors){
			var tmp_errors = '';
			$.each(data.errors, function(i, n){
				tmp_errors += n;
			});
			alert(tmp_errors);
		}else if(data['data'][0]['conf_bill_type']){
                        alert('设置成功',function(){
                            location.partReload();
                        });
		}
	}, "json");
	return false;
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