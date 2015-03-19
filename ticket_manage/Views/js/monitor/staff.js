$(document).ready(function() {

	//选择
	$('#allcheck').click(function(){
		if($(this).text() == '全选'){
			$('#staff-body').find('input').iCheck('check');
			$(this).text('反选');
		}else{
			$('#staff-body').find('input').iCheck('uncheck');
			$(this).text('全选');
		};
		return false;
	});

	//删除、启用按钮的点击动作
	$("button[name='operate']").click(function(){
		$("input[name='type']").val($(this).val());
		var supervise_id = $('#supervise_id').val();
		var ty = $('#ty').val();
		var ids = $("input[name='id[]']:checked").val();
		if (ids == undefined) {
			var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>请至少选择一个用户进行操作</div>';
			$('#show_msg').html(warn_msg);
			return false;
		}
		var obj = $('#staff-form');
		if(obj.validationEngine('validate') == true){
			$.post('index.php?c=monitor&a=doStaff', obj.serialize(),function(data){
				if(data.data=='fail'){
					var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>保存用户失败!'+data.errors+'</div>';
					$('#show_msg').html(warn_msg);
				}else{
					if($("input[name='type']").val() == 'del'){
					var type_msg = '删除成功';
					}else{
					var type_msg = '修改成功';
					}
					var succss_msg = '<div class="alert alert-success"><strong>'+type_msg+'!</strong> 2 秒后跳转到机构员工页..</div>';
					$('#show_msg').html(succss_msg);
					setTimeout("location.href='/monitor_accountLists_"+supervise_id+".html?type="+ty+"'", 3000);
				}
			},"json");
		
		};
		return false;
	});
});