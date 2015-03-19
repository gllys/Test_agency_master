$(document).ready(function() {

	$('#msg-form').validationEngine({
        addFailureCssClassToField: 'error',
        showPrompts: true
    });

	$('.sms-content').click(function(){
		$(this).next().toggle();
		var _self    = $(this);
		var has_read = $(this).attr('data-read');
		var id       = $(this).attr('data-id');
		if(has_read != 'read') {
			target_url = 'message_read.html';
			$.post(target_url,{id:id},function(data){
				if(data.succ){
					_self.attr('data-read', 'read');
					if(_self.hasClass('unread')) {
						_self.removeClass('unread');
					}
				}
			},'json');
			return false;
		}
	});

	//删除
	$('.del-msg').click(function(){
		$('#show_msg').empty();
		var tr_parent = $(this).parents('li');
		if(confirm('确认删除吗?')){
			var id         = $(this).attr('data-id');
			var arr = [];
			arr.push(id);
			deleteMsg(arr);
		}
		return false;
	});

	function deleteMsg(id) {
		var target_url = 'message_delete.html';
		$.post(target_url,{id:id},function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				tmp_errors = '删除失败!'+tmp_errors;
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
				$('#show_msg').html(warn_msg);
				location.href='#show_msg';
			}else if(data.succ){
				var succss_msg = '<div class="alert alert-success"><strong>删除成功!</strong></div>';
				$('#show_msg').html(succss_msg);
				location.href='#show_msg';
				setTimeout("location.href='message_publish.html'", '2000');
			}
		},'json');
	}

	//选择
	$('.allcheck').click(function(){
		var obj = $(this).find('ins');
		var input = $(this).parents('.tab-pane').find('input');
		if(obj.text() == '全选'){
			input.iCheck('check');
			obj.text('反选');
		}else{
			input.iCheck('uncheck');
			obj.text('全选');
		}
		return false;
	});

	//批量删除
	$('.alldel').click(function(){
		var obj = $(this).parents('.tab-pane');
		var arr = [];
		if(obj.find('input').is(':checked')){
			obj.find('input:checked').each(function(i){
				arr.push($(this).val());
			});
			deleteMsg(arr);
		} else {
			alert('您尚未选择需要删除的项');
		}
		return false;
	});

	//单删除消息
	$('.msg-remove').click(function(){
		var value = $(this).parents('li').attr('mid');
	});

	//发布公告点击事件
	$('#notice').click(function(){
		if($(this).is(':checked')){
			$('#noticebox').show();
			$('.summary').attr('placeholder','');
		}else{
			$('#noticebox').hide();
			$('.summary').attr('placeholder','您将对整个平台的机构进行广播，请谨慎编辑发送。');
		}
	});

	//发送消息|公告
	$('#msg-form-button').click(function(){
		var obj = $('#msg-form');
		$('#show_msg').empty();
		$.post('message_addmsg.html', obj.serialize(),function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
				$('#show_msg').html(warn_msg);
				location.href='#show_msg';
			}else if(data['data']){
				var succss_msg = '<div class="alert alert-success"><strong>发送成功!</strong></div>';
				$('#show_msg').html(succss_msg);
				location.href='#show_msg';
				setTimeout("location.href='message_publish.html?viewType=3'", '2000');
			}
		}, "json");
		return false;
	});
});