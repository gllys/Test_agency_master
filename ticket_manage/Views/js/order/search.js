$(document).ready(function() {
	$('.form-time').daterangepicker({
		format:'YYYY-MM-DD'
		})

	$('.popover-btn').popover({
		'placement': 'left'
	
	})
	
	$('.content').slimScrollHorizontal({
		width: '100%',
		alwaysVisible: true,
		start: 'left',
		wheelStep: 10
	}).css({paddingBottom: '10px'})
		
		
	$('.date').datepicker({
		format: 'yyyy-mm-dd'
	});

});

//申请退票
function modal_jump_refund(hash)
{
	$('#return-ticket').html('');
	$.get('index.php?c=order&a=getRefund&hash='+hash, function(data){
		$('#return-ticket').html(data);
	});
}

//申请改期
function modal_jump_useday(order_id)
{
	$('#postpone').html('');
	$.get('index.php?c=order&a=getUseday&order_id='+order_id, function(data){
		$('#postpone').html(data);
	});
}

//验票记录
function modal_jump_record(order_id)
{
	$('#wicket-record').html('');
	$.get('index.php?c=order&a=getRecord&order_id='+order_id,function(data){
		$('#wicket-record').html(data);
	});
}

//发送短信
function modal_jump_sms(mobile)
{	
	$('#sms').find('#sms_mobile').html(mobile);		
}

//define hide msg event:
var hideMsg = function(){
	$('#show_msg').fadeOut('slow');
}

//确认发送短信
$('#send_sms').click(function(){
	var mobile = $('#sms_mobile').text();
	var content = $('#sms_content').val();
	if (!content) { alert('请输入短信内容'); return false;};
	$.post(
		'index.php?c=order&a=doSMS',
		{
			'mobile' : mobile,
			'content': content
		},
		function(data){
			$('#sms .close').trigger('click');
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
				$('#show_msg').html(warn_msg);
			}else if(data['data']){
				var type_msg = '短信发送成功';
				var succss_msg = '<div class="alert alert-success"><strong>'+type_msg+'!</strong></div>';
				$('#show_msg').html(succss_msg);
				setTimeout('hideMsg()', 5000);
			}
	}, 'json');
	return false;
});