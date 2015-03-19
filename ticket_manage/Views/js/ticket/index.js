$(document).ready(function() {

	//点击缩略图查看大图事件
	$('.thumbs').touchTouch();

	//审核按钮
	$('.verify').popover({'placement':'bottom','html':true}).click(function(){
		var landscape_id = $(this).attr('data-info-id');
		var html='<div class="editable-buttons landscape"><button class="btn btn-primary btn-sm" data-landscape-id="'+landscape_id+'"><i class="icon-ok"></i></button><button class="btn btn-default btn-sm" data-landscape-id="'+landscape_id+'"><i class="icon-remove"></i></button></div>'
		$('.popover-content').html(html)
		return false
	})
	
	//审核按钮 - 同意
	$(document).on('click','.landscape .btn-primary',function(){
		var landscapeId = $(this).attr('data-landscape-id');
		if(confirm('确定要审核通过吗？')) {
			verify(landscapeId, 'normal');
		}
		$('.verify').popover('hide')
	});

	//审核按钮 - 不同意
	$(document).on('click','.landscape .btn-default',function(){
		var landscapeId = $(this).attr('data-landscape-id');
		if(confirm('确定要驳回吗？')) {
			verify(landscapeId, 'failed');
		}
		$('.verify').popover('hide')
	});

	function verify(landscapeId, status){
		$.post('index.php?c=ticket&a=landscapeVerify', {id:landscapeId,status:status},function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
				$('#show_msg').html(warn_msg);
				location.href='#show_msg';
			}else if(data['data'][0]['id']){
				var succss_msg = '<div class="alert alert-success"><strong>操作成功!</strong></div>';
				$('#show_msg').html(succss_msg);
				location.href='#show_msg';
				setTimeout("location.href='ticket_index.html'", 2000);
			}
		}, "json");
		return false;
	}

	//删除
	$('.del-ticket').click(function(){
		if(confirm('确认删除吗?')){
			$(this).parents('tr').remove()
		}
		return false
	});

});

$('input.form-time').daterangepicker({format:'YYYY-MM-DD'});

function modal_jump(id,status)
{
	$('#verify-modal').html();
	$.get('index.php?c=ticket&a=getModalJump&id='+id+'&status='+status,function(data){
		$('#verify-modal').html(data);
	});
}

//删除接入
function itourismOut(landscapeId)
{
	if (confirm('确认删除接入么？')) 
	{
		$.post('index.php?c=ticket&a=itourismLandscapeOut', {id:landscapeId},function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
				$('#show_msg').html(warn_msg);
				location.href='#show_msg';
			}else if(data['data'][0]['id']){
				var succss_msg = '<div class="alert alert-success"><strong>操作成功!</strong></div>';
				$('#show_msg').html(succss_msg);
				location.href='#show_msg';
				setTimeout("location.href='ticket_index.html'", '2000');
			}
		}, "json");
		return false;
	}
}

