$(document).ready(function() {

	$('.form-time').daterangepicker({
		format:'YYYY-MM-DD'
	});

//选择
	$('#allcheck').click(function(){
		if($(this).text() == '全选'){
			$('#tickets-use').find('input').iCheck('check')
			$(this).text('反选')
		}else{
			$('#tickets-use').find('input').iCheck('uncheck')
			$(this).text('全选')
		}
		return false
	})
	
	
	$('.pass').click(function(){

		if($('#tickets-use input').is(':checked')){

		
		//...........
		
		} else {
			alert('您尚未选择需要的项')
		}
		return false;
	})
	
	$('.content').slimScrollHorizontal({
		width: '100%',
		alwaysVisible: true,
		start: 'left',
		wheelStep: 10
	}).css({paddingBottom: '10px'})
})

	function validate()
	{
		if (confirm('确认退款么？')) {
			var val = $('input[type=checkbox]:checked').val();
			if (!val) {
				alert('请至少选择一个退款申请单再进行退款操作');
				return false;
			}
			return true;
		}
		return false;
	}

	function prefund(id)
	{
		if (confirm('确认退款么？')) {
			location.href="refund_prefund_"+id+".html";
		}
	}

	//审核按钮
	// $('.refundVerify').popover({'placement':'bottom','html':true}).click(function(){
	// 	var refund_id = $(this).attr('data-refund-id');
	// 	var html='<div class="editable-buttons refund"><button class="btn btn-primary btn-sm" data-refund-id="'+refund_id+'"><i class="icon-ok"></i></button><button class="btn btn-default btn-sm" data-refund-id="'+refund_id+'"><i class="icon-remove"></i></button></div>'
	// 	$('.popover-content').html(html);
	// 	return false;
	// })
	
	//审核按钮 - 同意
	// $(document).on('click','.refund .btn-primary',function(){
	// 	var refund_id = $(this).attr('data-refund-id');
	// 	if(confirm('确定要审核通过吗？')) {
	// 		verifyRefund(refund_id, 'checked');
	// 	}
	// 	$('.refundVerify').popover('hide');
	// });

	//审核按钮 - 不同意
	// $(document).on('click','.refund .btn-default',function(){
	// 	var refund_id = $(this).attr('data-refund-id');
	// 	if(confirm('确定要驳回吗？')) {
	// 		verifyRefund(refund_id, 'reject');
	// 	}
	// 	$('.refundVerify').popover('hide');
	// });

	//TODO::审核退款
	function verify(refund_id)
	{
		if(confirm('确定要审核通过吗？')) {
			verifyRefund(refund_id, 'checked');
		}
	}

	//TODO::驳回退款
	function reject(refund_id)
	{
		if(confirm('确定要驳回吗？')) {
			verifyRefund(refund_id, 'reject');
		}
	}

	//退票审核
	function verifyRefund(refund_id, status)
	{
		$.post('index.php?c=refund&a=refundVerify', {id:refund_id,status:status},function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
				$('#show_msg').html(warn_msg);
			}else if(data['data'][0]['audited_at']){
				var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功!</strong></div>';
				$('#show_msg').html(succss_msg);
				setTimeout("location.href='refund_verify.html'", '2000');
			}
		}, "json");
		return false;
	}

